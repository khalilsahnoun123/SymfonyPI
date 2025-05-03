<?php

namespace App\Controller\velo;

use App\Entity\velo\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{
    #[Route('/accueil', name: 'app_accueil')]
    public function index(WeatherController $weatherService, EntityManagerInterface $em): Response
    {
        // Load the user with ID 1 from the database
        $user = $em->getRepository(User::class)->find(1);

        if (!$user) {
            throw $this->createNotFoundException('User with ID 1 not found.');
        }

        $governorat = $user->getGouvernorat();
        $weather = null;
        $recommendations = [];
        $tip = '';

        if ($governorat) {
            $weather = $weatherService->getWeatherForLocation($governorat);

            if ($weather) {
                $recommendationData = $this->getBikeRecommendations($weather);
                $recommendations = $recommendationData['bikes'];
                $tip = $recommendationData['tip'];
            }
        }

        return $this->render('Front/acceuil/index.html.twig', [
            'user' => $user,
            'governorat' => $governorat,
            'weather' => $weather,
            'recommendations' => $recommendations,
            'tip' => $tip,
        ]);
    }

    private function getBikeRecommendations(array $weather): array
    {
        $recommendations = [];

        if ($weather['rain'] > 0 || $weather['precipitation'] > 0) {
            $recommendations[] = "🚴‍♂️ Vélo électrique - Stable et résistant aux intempéries";
            $recommendations[] = "🌿 Vélo hybride - Polyvalent pour les conditions humides";
            $recommendations[] = "🗺️ Vélo de randonnée - Pour divers types de météo";
        } elseif ($weather['temperature'] > 25) {
            $recommendations[] = "🚀 Vélo de route - Léger par temps chaud";
            $recommendations[] = "🏖️ Vélo cruiser - Confort optimal au soleil";
        } elseif ($weather['temperature'] < 10) {
            $recommendations[] = "📦 Vélo cargo - Bonne protection contre le froid";
            $recommendations[] = "📏 Vélo pliant - Idéal pour les trajets courts par temps froid";
        } else {
            $recommendations[] = "🚵‍♂️ VTT - Pour tout terrain";
            $recommendations[] = "🔄 Vélo hybride - Équilibré et polyvalent";
        }

        $tip = '';
        if ($weather['temperature'] > 30) {
            $tip = "🔥 Astuce : restez hydraté et portez des vêtements légers.";
        } elseif ($weather['rain'] > 0) {
            $tip = "☔ Astuce : pensez à porter des vêtements imperméables.";
        }

        return ['bikes' => $recommendations, 'tip' => $tip];
    }
}
