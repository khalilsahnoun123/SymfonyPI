<?php 
// src/Controller/StationveloController.php
namespace App\Controller;

use App\Entity\Stationvelo;
use App\Repository\StationveloRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\StationveloFormType;

class StationveloController extends AbstractController
{
    #[Route('/stations', name: 'app_stations_index')]
    public function index(StationveloRepository $stationveloRepository): Response
    {
        $stations = $stationveloRepository->findAllStations();
        
        return $this->render('Admin/stationvelo/index.html.twig', [
            'stations' => $stations,
        ]);
    }
    
    #[Route('/stations/search', name: 'app_stations_search')]
    public function searchStations(Request $request, StationveloRepository $stationveloRepository): Response
    {
        $searchTerm = $request->query->get('search', '');
        $stations = [];

        if ($searchTerm) {
            $stations = $stationveloRepository->createQueryBuilder('s')
                ->where('s.gouvernera LIKE :searchTerm')
                ->orWhere('s.municapilite LIKE :searchTerm')
                ->orWhere('s.adresse LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%')
                ->getQuery()
                ->getResult();
        }

        return $this->render('Front/stationvelo/search.html.twig', [
            'stations' => $stations,
            'searchTerm' => $searchTerm,
        ]);
    }

    #[Route('/station/new', name: 'app_station_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $station = new Stationvelo();
        $form = $this->createForm(StationveloFormType::class, $station); // Changed here
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($station);
            $entityManager->flush();
            
            return $this->redirectToRoute('app_stations_index');
        }
        
        return $this->render('Admin/stationvelo/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/station/{id}/edit', name: 'app_station_edit')]
    public function edit(Request $request, Stationvelo $station, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(StationveloFormType::class, $station); // Changed here
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            return $this->redirectToRoute('app_stations_index');
        }
        
        return $this->render('Admin/stationvelo/edit.html.twig', [
            'form' => $form->createView(),
            'station' => $station,
        ]);
    }

    #[Route('/station/{id}', name: 'app_station_delete', methods: ['POST'])]
    public function delete(Request $request, Stationvelo $station, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$station->getId_station(), $request->request->get('_token'))) {
            $entityManager->remove($station);
            $entityManager->flush();
        }
        
        return $this->redirectToRoute('app_stations_index');
    }

    #[Route('/stations/{id}/velos', name: 'velos_list')]
    public function listVelos(Stationvelo $station): Response
    {
        $velos = $station->getVelos()->filter(function ($velo) {
            return $velo->isDispo(); // Only show vélos that are available
        });

        return $this->render('Front/stationvelo/velos.html.twig', [
            'station' => $station,
            'velos' => $velos,
        ]);
    }
}