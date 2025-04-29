<?php
// src/Controller/StationController.php

namespace App\Controller\transportpublic;

use App\Entity\transportpublic\Station;
use App\Form\transportpublic\StationType;
use App\Repository\transportpublic\StationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/station')]
class StationController extends AbstractController
{
    #[Route('/', name: 'app_station_index', methods: ['GET'])]
    public function index(Request $request, StationRepository $repo): Response
    {
        $query = $request->query->get('q');
        if ($query) {
            $stations = $repo->findByNom($query);
        } else {
            $stations = $repo->findAll();
        }
    
        return $this->render('Admin/admin_transportPublic/station/index.html.twig', [
            'stations' => $stations,
        ]);
    }

    #[Route('/new', name: 'app_stations_new', methods: ['GET','POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $station = new Station();
        $form = $this->createForm(StationType::class, $station);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($station);
            $em->flush();
            $this->addFlash('success', 'Station créée avec succès.');
            return $this->redirectToRoute('app_station_index');
        }

        return $this->render('Admin/admin_transportPublic/station/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_station_show', methods: ['GET'])]
    public function show(Station $station): Response
    {
        return $this->render('Admin/admin_transportPublic/station/show.html.twig', [
            'station' => $station,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_stations_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Station $station, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(StationType::class, $station);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Station mise à jour.');
            return $this->redirectToRoute('app_station_index');
        }

        return $this->render('Admin/admin_transportPublic/station/edit.html.twig', [
            'form' => $form->createView(),
            'station' => $station,
        ]);
    }

    #[Route('/{id}', name: 'app_stationTB_delete', methods: ['POST'])]
    public function delete(Request $request, Station $station, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' .$station->getId(), $request->request->get('_token'))) {
            $em->remove($station);
            $em->flush();
            $this->addFlash('success', 'Station supprimée.');
        }
        return $this->redirectToRoute('app_station_index');
    }
}
