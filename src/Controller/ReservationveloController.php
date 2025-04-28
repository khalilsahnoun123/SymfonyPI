<?php

namespace App\Controller;

use App\Entity\Reservationvelo;
use App\Entity\User;
use App\Entity\Velo;
use App\Form\ReservationveloType;
use App\Repository\ReservationveloRepository;
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
            'reservationvelos' => $reservationveloRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_reservationvelo_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $veloId = $request->request->get('velo_id');
        $velo = $entityManager->getRepository(Velo::class)->find($veloId);

        if (!$velo) {
            $this->addFlash('error', 'Vélo not found.');
            return $this->redirectToRoute('app_stations_search');
        }

        $reservationvelo = new Reservationvelo();
        $reservationvelo->setVelo($velo);
        $user = $entityManager->getRepository(User::class)->find(1); // Static user with ID 1
        $reservationvelo->setUser($user);
        $reservationvelo->setDateDebut(new \DateTime()); // Set the start date to now
        $reservationvelo->setDateFin((new \DateTime())->modify('+1 day')); // Example: 1-day reservation
        $reservationvelo->setStatut('booked');
        $reservationvelo->setPrice($velo->getVeloType()->getPrice()); // Fetch price from velo.veloType.price

        $velo->setDispo(false); // Set the vélo as not available
        $entityManager->persist($velo);

        $entityManager->persist($reservationvelo);
        $entityManager->flush();

        $this->addFlash('success', 'Vélo reserved successfully!');
        return $this->redirectToRoute('app_reservationvelo_index');
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
        if ($this->isCsrfTokenValid('delete'.$reservationvelo->getId_reservation_velo(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($reservationvelo);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reservationvelo_index', [], Response::HTTP_SEE_OTHER);
    }
}
