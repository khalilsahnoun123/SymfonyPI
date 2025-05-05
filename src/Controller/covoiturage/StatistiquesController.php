<?php

namespace App\Controller\covoiturage;

use App\Entity\covoiturage\Covoiturage;                    
use App\Repository\covoiturage\CovoiturageRepository;        
use App\Repository\covoiturage\ReservationCovRepository;    
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatistiquesController extends AbstractController
{
    #[Route('/statistiques', name: 'app_statistiques', methods: ['GET'])]
    public function index(
        CovoiturageRepository $covoiturageRepository,
        ReservationCovRepository $reservationCovRepository
    ): Response {
      
        $covoiturages = $covoiturageRepository->findAll();

  
        $chartData = [];
        foreach ($covoiturages as $cov) {
            $chartData[] = [
                'label' => $cov->getPointDeDepart(),
                'value' => $reservationCovRepository->countByCovoiturage($cov),
            ];
        }

        
        return $this->render('Admin/reservation_cov/Statistiques.html.twig', [
            'chartData' => json_encode($chartData),
        ]);
    }
}
