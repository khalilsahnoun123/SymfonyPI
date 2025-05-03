<?php

namespace App\Controller\velo;

use Knp\Snappy\Pdf;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use App\Entity\velo\Reservationvelo;
use App\Entity\velo\User;
use App\Entity\velo\Velo;
use App\Form\velo\ReservationveloType;
use App\Repository\velo\ReservationveloRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/reservationvelo')]
final class ReservationveloController extends AbstractController
{
    #[Route(name: 'app_reservationvelo_index', methods: ['GET'])]
    public function index(ReservationveloRepository $reservationveloRepository): Response
    {
        return $this->render('Front/reservationvelo/index.html.twig', [
            // Filter by user ID 1 using repository method
            'reservationvelos' => $reservationveloRepository->findBy(['user' => 1]),
        ]);
    }
    #[Route('/start', name: 'app_reservationvelo_start', methods: ['POST'])]
    public function startReservation(Request $request, EntityManagerInterface $entityManager, KonnectPaymentService $paymentService): Response
    {
        $veloId = $request->request->get('velo_id');
        $startDate = $request->request->get('start_date');
        $endDate = $request->request->get('end_date');

        if (!$veloId || !$startDate || !$endDate) {
            $this->addFlash('error', 'Missing reservation parameters');
            return $this->redirectToRoute('app_stations_search');
        }

        $user = $entityManager->getRepository(User::class)->find(1); // TODO: replace with current user
        if (!$user) {
            $this->addFlash('error', 'You must be logged in to make a reservation');
            return $this->redirectToRoute('app_login');
        }

        try {
            $startDateTime = new \DateTime($startDate);
            $endDateTime = new \DateTime($endDate);
            if ($endDateTime <= $startDateTime) {
                $this->addFlash('error', 'End date must be after start date');
                return $this->redirectToRoute('app_stations_search');
            }
        } catch (\Exception $e) {
            $this->addFlash('error', 'Invalid date format');
            return $this->redirectToRoute('app_stations_search');
        }

        $velo = $entityManager->getRepository(Velo::class)->find($veloId);
        if (!$velo) {
            $this->addFlash('error', 'Bike not found');
            return $this->redirectToRoute('app_stations_search');
        }

        // Check overlapping reservations
        $existingReservation = $entityManager->getRepository(Reservationvelo::class)
            ->findOverlappingReservation($velo, $startDateTime, $endDateTime);
        if ($existingReservation) {
            $this->addFlash('error', 'This bike is already reserved for the selected dates');
            return $this->redirectToRoute('app_stations_search');
        }

        $interval = $startDateTime->diff($endDateTime);
        $hours = $interval->h + ($interval->days * 24) + ($interval->i / 60);
        $price = $velo->getVeloType()->getPrice() * $hours;

        // 1. Call payment creation
        $paymentUrl = $paymentService->createPayment((int) ($price * 1000), '680e79d636eae5c3e033a84b');

        // Extract the payment_ref manually from the URL
        $parsedUrl = parse_url($paymentUrl);
        parse_str($parsedUrl['query'], $queryParams);
        $paymentRef = $queryParams['payment_ref'] ?? null;

        if (!$paymentUrl || !$paymentRef) {
            $this->addFlash('error', 'Failed to initiate payment');
            return $this->redirectToRoute('app_stations_search');
        }

        // 2. Save temporary reservation data in session (or you can pass them in URL params encrypted)
        $session = $request->getSession();

        $session->set('reservation_data', [
            'velo_id' => $velo->getId_Velo(),
            'id_user' => $user->getId(),
            'start' => $startDate,
            'end' => $endDate,
            'price' => $price,
            'payment_ref' => $paymentRef, // 
        ]);




        // 3. Redirect user to payment page
        return $this->render('Front/reservationvelo/redirect_to_payment.html.twig', [
            'paymentUrl' => $paymentUrl,
        ]);
    }

    #[Route('/wait', name: 'app_reservationvelo_wait_payment')]
    public function waitPayment(Request $request): Response
    {
        $session = $request->getSession();
        $reservationData = $session->get('reservation_data');

        if (!$reservationData) {
            $this->addFlash('error', 'No reservation data found.');
            return $this->redirectToRoute('app_stations_search');
        }

        return $this->render('Front/reservationvelo/wait_payment.html.twig', [
            'payment_ref' => $reservationData['payment_ref'] ?? null,
        ]);
    }
    #[Route('/payment/check-status', name: 'app_reservationvelo_check_payment_status', methods: ['GET'])]
    public function checkPaymentStatus(Request $request, KonnectPaymentService $paymentService): Response
    {
        $paymentRef = $request->query->get('payment_ref');

        if (!$paymentRef) {
            return $this->json(['status' => 'error', 'message' => 'No payment ref']);
        }

        $status = $paymentService->checkPaymentStatus($paymentRef);

        return $this->json(['status' => $status]);
    }


    #[Route('/payment/success', name: 'app_reservationvelo_payment_success')]
    public function paymentSuccess(Request $request, EntityManagerInterface $entityManager, KonnectPaymentService $paymentService, MailerInterface $mailer): Response
    {
        $paymentRef = $request->query->get('payment_ref'); // Get ?payment_ref=.... from URL

        if (!$paymentRef) {
            $this->addFlash('error', 'No payment reference found.');
            return $this->redirectToRoute('app_stations_search');
        }

        $session = $request->getSession();
        $reservationData = $session->get('reservation_data');

        if (!$reservationData) {
            $this->addFlash('error', 'No reservation data found.');
            return $this->redirectToRoute('app_stations_search');
        }

        $status = $paymentService->checkPaymentStatus($paymentRef);

        if ($status !== 'completed') {
            $this->addFlash('error', 'Payment was not successful.');
            return $this->redirectToRoute('app_stations_search');
        }


        // Create reservation after payment success
        $user = $entityManager->getRepository(User::class)->find($reservationData['id_user']);
        $velo = $entityManager->getRepository(Velo::class)->find($reservationData['velo_id']);

        if (!$user || !$velo) {
            $this->addFlash('error', 'Error creating reservation.');
            return $this->redirectToRoute('app_stations_search');
        }

        $reservation = new Reservationvelo();
        $reservation->setVelo($velo);
        $reservation->setUser($user);
        $reservation->setDateDebut(new \DateTime($reservationData['start']));
        $reservation->setDateFin(new \DateTime($reservationData['end']));
        $reservation->setStatut('booked');
        $reservation->setPrice($reservationData['price']);
        $reservation->setPaymentStatus('paid');

        $velo->setDispo(false);

        try {
            $entityManager->persist($reservation);
            $entityManager->persist($velo);
            $entityManager->flush();

            // Send email (same logic you already have)
            $email = (new TemplatedEmail())
                ->from('jihen.alayette123@gmail.com')
                ->to($user->getEmail())
                ->subject('Bike Reservation Confirmation')
                ->htmlTemplate('emails/reservation_confirmation.html.twig')
                ->context([
                    'user' => $user,
                    'reservation' => $reservation,
                    'velo' => $velo,
                ]);
            $mailer->send($email);

            $session->remove('reservation_data'); // Clear session
            $this->addFlash('success', 'Reservation created successfully!');

            return $this->redirectToRoute('app_reservationvelo_show', [
                'id_reservation_velo' => $reservation->getIdReservationVelo(),
            ]);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Failed to create reservation: ' . $e->getMessage());
            return $this->redirectToRoute('app_stations_search');
        }
    }




    #[Route('/{id_reservation_velo}', name: 'app_reservationvelo_show', methods: ['GET'])]
    public function show(Reservationvelo $reservationvelo): Response
    {
        return $this->render('Front/reservationvelo/show.html.twig', [
            'reservationvelo' => $reservationvelo,
        ]);
    }

    #[Route('/{id_reservation_velo}/edit', name: 'app_reservationvelo_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reservationvelo $reservationvelo, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReservationveloType::class, $reservationvelo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reservationvelo_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Front/reservationvelo/edit.html.twig', [
            'reservationvelo' => $reservationvelo,
            'form' => $form,
        ]);
    }

    #[Route('/{id_reservation_velo}', name: 'app_reservationvelo_delete', methods: ['POST'])]
    public function delete(Request $request, Reservationvelo $reservationvelo, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reservationvelo->getId_reservation_velo(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($reservationvelo);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reservationvelo_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/{id_reservation_velo}/download-pdf', name: 'app_reservationvelo_download_pdf')]
    public function downloadPdf(
        Reservationvelo $reservationvelo,
        Pdf $knpSnappyPdf
    ): Response {
        // Render a PDF-friendly Twig template
        $html = $this->renderView('pdf/reservation_detail.html.twig', [
            'reservation' => $reservationvelo,
        ]);

        $filename = sprintf('reservation_%d.pdf', $reservationvelo->getIdReservationVelo());

        $pdf = $knpSnappyPdf->getOutputFromHtml($html);

        return new Response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }
}
