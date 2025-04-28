<?php

namespace App\Controller;

use App\Entity\ReservationTaxi;
use App\Form\ReservationTaxiType;
use App\Repository\ReservationTaxiRepository;
use App\Repository\UserRepository;
use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;



#[Route('/reservation/taxi')]
final class ReservationTaxiController extends AbstractController
{
    #[Route(name: 'app_reservation_taxi_index', methods: ['GET'])]
    public function index(ReservationTaxiRepository $reservationTaxiRepository): Response
    {
        return $this->render('Admin/reservation_taxi/index.html.twig', [
            'reservation_taxis' => $reservationTaxiRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_reservation_taxi_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reservationTaxi = new ReservationTaxi();
        $form = $this->createForm(ReservationTaxiType::class, $reservationTaxi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reservationTaxi);
            $entityManager->flush();

            return $this->redirectToRoute('app_reservation_taxi_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Admin/reservation_taxi/new.html.twig', [
            'reservation_taxi' => $reservationTaxi,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reservation_taxi_show', methods: ['GET'])]
    public function show(ReservationTaxi $reservationTaxi): Response
    {
        return $this->render('Admin/reservation_taxi/show.html.twig', [
            'reservation_taxi' => $reservationTaxi,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reservation_taxi_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ReservationTaxi $reservationTaxi, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReservationTaxiType::class, $reservationTaxi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reservation_taxi_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Admin/reservation_taxi/edit.html.twig', [
            'reservation_taxi' => $reservationTaxi,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reservation_taxi_delete', methods: ['POST'])]
    public function delete(Request $request, ReservationTaxi $reservationTaxi, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservationTaxi->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($reservationTaxi);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reservation_taxi_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/{id}/reserver', name: 'app_vehicule_reserver', methods: ['GET', 'POST'])]
    public function reserver(
        $id,
        EntityManagerInterface $em,
        UserRepository $ur,
        VehiculeRepository $vr,
        MailerInterface $mailer,
        ParameterBagInterface $params
    ): Response {
        // Get the current user (authenticated user)
        $user = $ur->find('1');
        if (!$user) {
            $this->addFlash('danger', 'Vous devez être connecté pour réserver.');
            return $this->redirectToRoute('front_vehicules');
        }

        // Find the vehicle
        $vehicule = $vr->find($id);
        if (!$vehicule) {
            $this->addFlash('danger', 'Véhicule introuvable.');
            return $this->redirectToRoute('front_vehicules');
        }

        // Create a new reservation
        $reservation = new ReservationTaxi();
        $reservation->setVehicule($vehicule);
        $reservation->setUser($user);
        $reservation->setStatus('en attente');

        try {
            // Send email
            $emailMessage = (new Email())
                ->from($params->get('app.email_sender'))
                ->to($user->getEmail())
                ->subject('Confirmation de Réservation')
                ->html("
                    <div style='font-family: Arial, sans-serif; border: 1px solid #ddd; border-radius: 6px; padding: 20px; max-width: 600px; margin: auto;'>
                        <p><b>WASALNI</b> Team</p>
                        <h3 style='color:rgb(9, 146, 27);'>Réservation Confirmée</h3>
                        <p>Merci pour votre réservation !</p>
                        <p><strong>Véhicule :</strong> {$vehicule->getMarque()}<br>
                           <strong>Statut :</strong> En attente</p>
                        <p>Nous vous contacterons bientôt pour confirmer les détails.</p>
                        <br>
                        <p>Merci,<br><strong>Wasalni Team</strong></p>
                    </div>
                ");

            $this->addFlash('success','Sending reservation confirmation email');
            $mailer->send($emailMessage);

            // Persist reservation only if email is sent successfully
            $em->persist($reservation);
            $em->flush();

            $this->addFlash('success', 'Une confirmation de réservation a été envoyée avec succès.');
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Erreur lors de l\'envoi de l\'email : ' . $e->getMessage());
            return $this->redirectToRoute('front_vehicules');
        }

        return $this->redirectToRoute('front_vehicules');
    }



    

}
