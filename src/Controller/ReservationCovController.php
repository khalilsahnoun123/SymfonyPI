<?php

namespace App\Controller;

use App\Entity\ReservationCov;
use App\Entity\Covoiturage;
use App\Form\ReservationCovType;
use App\Repository\ReservationCovRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/reservation/cov')]
final class ReservationCovController extends AbstractController
{
    #[Route('/', name: 'app_reservation_cov_index', methods: ['GET'])]
    public function index(ReservationCovRepository $reservationCovRepository): Response
    {
        return $this->render('Admin/reservation_cov/index.html.twig', [
            'reservation_covs' => $reservationCovRepository->findAllWithCovoiturage(),
        ]);
    }

    #[Route('/new/{covoiturageId}', name: 'app_reservation_cov_new', defaults: ['covoiturageId' => null], methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ?int $covoiturageId = null): Response
    {
        $reservationCov = new ReservationCov();
        
        if ($covoiturageId) {
            $covoiturage = $entityManager->getRepository(Covoiturage::class)->find($covoiturageId);
            if ($covoiturage) {
                $reservationCov->setCovoiturage($covoiturage);
            }
        }

        $form = $this->createForm(ReservationCovType::class, $reservationCov);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reservationCov);
            $entityManager->flush();

            $this->addFlash('success', 'Réservation créée avec succès!');
            return $this->redirectToRoute('app_reservation_cov_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Admin/reservation_cov/new.html.twig', [
            'reservation_cov' => $reservationCov,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reservation_cov_show', methods: ['GET'])]
    public function show(ReservationCov $reservationCov): Response
    {
        return $this->render('Admin/reservation_cov/show.html.twig', [
            'reservation_cov' => $reservationCov,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reservation_cov_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ReservationCov $reservationCov, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReservationCovType::class, $reservationCov);
        $form->handleRequest($request);
    
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $entityManager->flush();
                $this->addFlash('success', 'Réservation mise à jour avec succès!');
                return $this->redirectToRoute('app_reservation_cov_index');
            }
            $this->addFlash('error', 'Veuillez corriger les erreurs dans le formulaire');
        }
    
        return $this->render('Admin/reservation_cov/edit.html.twig', [
            'reservation_cov' => $reservationCov,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_reservation_cov_delete', methods: ['POST'])]
    public function delete(Request $request, ReservationCov $reservationCov, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservationCov->getId(), $request->get('_token'))) {
            $entityManager->remove($reservationCov);
            $entityManager->flush();
            $this->addFlash('success', 'Réservation supprimée avec succès!');
        }

        return $this->redirectToRoute('app_reservation_cov_index', [], Response::HTTP_SEE_OTHER);
    }
}