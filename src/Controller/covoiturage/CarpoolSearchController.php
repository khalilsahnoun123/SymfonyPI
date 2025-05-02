<?php

namespace App\Controller\covoiturage;

use App\Repository\covoiturage\CovoiturageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class CarpoolSearchController extends AbstractController
{
    #[Route('/api/carpools/search', name: 'api_carpool_search', methods: ['POST'])]
    public function search(Request $request, CovoiturageRepository $covoiturageRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $departure = $data['point_de_depart'] ?? null;
        $destination = $data['point_de_destination'] ?? null;

        if (!$departure || !$destination) {
            return $this->json(['error' => 'Departure and destination are required'], 400);
        }

        // Appeler une méthode dans ton Repository
        $carpools = $covoiturageRepository->findByDepartureAndDestination($departure, $destination);

        $results = [];

        foreach ($carpools as $carpool) {
            $results[] = [
                'id' => $carpool->getId(),
                'departure' => $carpool->getPointDeDepart(),
                'destination' => $carpool->getPointDeDestination(),
                'availableSeats' => $carpool->getNbrPlace(),
                'price' => $carpool->getPrix(),
            ];
        }

        return $this->json([
            'carpools' => $results
        ]);
    }
}
