<?php

namespace App\Controller\covoiturage;   
use Symfony\Component\Mime\Email;
use App\Entity\covoiturage\User; 
use App\Entity\covoiturage\ReservationCov; 
use App\Entity\covoiturage\Covoiturage;
use App\Form\covoiturage\CovoiturageType;
use App\Repository\covoiturage\CovoiturageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Dompdf\Dompdf;
use Symfony\Component\HttpFoundation\ResponseHeaderBag; 
use Symfony\Component\Mailer\MailerInterface;  
use SendGrid;
use SendGrid\Mail\Mail;
use Twilio\Rest\Client;


#[Route('/admin/covoiturage')]
class CovoiturageController extends AbstractController
{
    #[Route('/stations', name: 'app_covoiturage_index', methods: ['GET'])]
    public function index(CovoiturageRepository $covoiturageRepository): Response
    {
        return $this->render('admin/covoiturage/index.html.twig', [
            'covoiturages' => $covoiturageRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_covoiturage_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $covoiturage = new Covoiturage();
        $form = $this->createForm(CovoiturageType::class, $covoiturage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($covoiturage);
            $entityManager->flush();

            $this->addFlash('success', 'Le covoiturage a été créé avec succès');
            return $this->redirectToRoute('app_covoiturage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/covoiturage/new.html.twig', [
            'covoiturage' => $covoiturage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_covoiturage_show', methods: ['GET'])]
    public function show(Covoiturage $covoiturage): Response
    {
        return $this->render('admin/covoiturage/show.html.twig', [
            'covoiturage' => $covoiturage,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_covoiturage_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Covoiturage $covoiturage, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CovoiturageType::class, $covoiturage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le covoiturage a été modifié avec succès');
            return $this->redirectToRoute('app_covoiturage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/covoiturage/edit.html.twig', [
            'covoiturage' => $covoiturage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_covoiturage_delete', methods: ['POST'])]
    public function delete(Request $request, Covoiturage $covoiturage, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$covoiturage->getId(), $request->request->get('_token'))) {
            $entityManager->remove($covoiturage);
            $entityManager->flush();
            $this->addFlash('success', 'Le covoiturage a été supprimé avec succès');
        }

        return $this->redirectToRoute('app_covoiturage_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/export/pdf', name: 'app_covoiturage_export_pdf', methods: ['GET'])]
    public function exportPdf(CovoiturageRepository $covoiturageRepository): Response
    {
        $covoiturages = $covoiturageRepository->findAll();

        if (empty($covoiturages)) {
            $this->addFlash('warning', 'Aucun covoiturage à exporter');
            return $this->redirectToRoute('app_covoiturage_index');
        }

        $html = $this->renderView('admin/covoiturage/export_pdf.html.twig', [
            'covoiturages' => $covoiturages,
            'title' => 'Liste des covoiturages',
            'date' => new \DateTime()
        ]);

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $response = new Response($dompdf->output());
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'liste-covoiturages-'.date('Y-m-d').'.pdf'
        );
        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', 'application/pdf');

        return $response;
    }   



    #[Route('/public/list', name: 'app_covoiturage_public_list', methods: ['GET'])]
public function publicList(CovoiturageRepository $covoiturageRepository): Response
{
    return $this->render('front/cov/public_list.html.twig', [
        'covoiturages' => $covoiturageRepository->findAvailableCovoiturages(),
    ]);
}


#[Route('/public/reserve/{id}', name: 'app_covoiturage_public_reserve', methods: ['GET', 'POST'])]
public function publicReserve(Request $request, Covoiturage $covoiturage, EntityManagerInterface $entityManager): Response
{
    $user = $entityManager->getRepository(User::class)->find(1);
    
    if (!$user) {
        $this->addFlash('error', 'Utilisateur de test non trouvé');
        return $this->redirectToRoute('app_covoiturage_public_list');
    }

    $reservation = new ReservationCov();
    $reservation->setCovoiturage($covoiturage);
    $reservation->setUser($user);
    $reservation->setStatus('pending');

    if ($request->isMethod('POST')) {
        $places = (int)$request->request->get('places');

        if ($places <= 0 || $places > $covoiturage->getNbrPlace()) {
            $this->addFlash('error', 'Nombre de places invalide');
            return $this->redirectToRoute('app_covoiturage_public_reserve', ['id' => $covoiturage->getId()]);
        }

        $reservation->setNbrPlace($places);
        $covoiturage->setNbrPlace($covoiturage->getNbrPlace() - $places);

        // Save to DB
        $entityManager->persist($reservation);
        $entityManager->flush();

        // ✅ Send SMS with Twilio
        $twilioSid = $_ENV['TWILIO_SID'];
        $twilioToken = $_ENV['TWILIO_TOKEN'];
        $twilioFrom = $_ENV['TWILIO_FROM'];

        $twilio = new Client($twilioSid, $twilioToken);
        $to = '+216' . $user->getPhone_number();
        try {
            $twilio->messages->create(
                $to, // Replace with user's phone number if available
                [
                    'from' => $twilioFrom,
                    'body' => 'Votre réservation de covoiturage a été confirmée avec succès !'
                ]
            );
        } catch (\Exception $e) {
            $this->addFlash('error', 'Échec de l\'envoi du SMS : ' . $e->getMessage());
        }

        $this->addFlash('success', 'Réservation effectuée et SMS envoyé avec succès !');
        return $this->redirectToRoute('app_covoiturage_public_list');
    }

    return $this->render('front/cov/reservation.html.twig', [
        'covoiturage' => $covoiturage,
    ]);
}



public function confirmAction(MailerInterface $mailer) 
{
    $email = (new Email())
        ->from('noreply@yourdomain.com')  // Can be your Gmail
        ->to('soulaimagarroury@gmail.com') // Your real email
        ->subject('Booking Confirmed!')
        ->text('Thank you for your reservation!');
        dd($email->getTextBody());
    $mailer->send($email);
    $this->addFlash('success', 'Email sent! Check your inbox.');
    return $this->redirectToRoute('app_covoiturage_public_list');
}
/*#[Route('/public/my-reservations', name: 'app_covoiturage_user_reservations', methods: ['GET'])]
public function userReservations(EntityManagerInterface $entityManager): Response
{
    $user = $entityManager->getRepository(User::class)->find(1); // Get user with ID = 1

    if (!$user) {
        $this->addFlash('error', 'Utilisateur introuvable');
        return $this->redirectToRoute('app_covoiturage_public_list');
    }

    $reservations = $entityManager->getRepository(ReservationCov::class)->findBy(['user' => $user]);

    return $this->render('front/cov/user_reservations.html.twig', [
        'reservations' => $reservations,
    ]);
}*/

} 