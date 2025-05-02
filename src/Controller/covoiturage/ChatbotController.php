<?php

namespace App\Controller\covoiturage;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ChatbotController extends AbstractController
{
    #[Route('/chatbot/ask', name: 'chatbot_ask', methods: ['POST'])]
    public function ask(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $question = strtolower($data['question'] ?? '');

        $answer = $this->generateAnswer($question);

        return new JsonResponse(['answer' => $answer]);
    }

    private function generateAnswer(string $question): string
    {
        // Expanded FAQ with both French and English questions
        $faq = [
            // Carpooling (Covoiturage)
            'how to book a ride' => 'To book a ride, you must be logged in. Then select your preferred carpool and click "Reserve".',
            'comment reserver un covoiturage' => 'Pour réserver un covoiturage, vous devez être connecté. Ensuite, sélectionnez le covoiturage de votre choix et cliquez sur "Réserver".',
            'how to create a carpool' => 'Only admins can create a new carpool via the admin dashboard.',
            'comment creer un covoiturage' => 'Seuls les administrateurs peuvent créer un nouveau covoiturage via le tableau de bord de l\'administration.',
            'can i cancel a booking' => 'Yes, you can cancel a booking from your profile under "My Reservations".',
            'puis-je annuler une réservation' => 'Oui, vous pouvez annuler une réservation depuis votre profil sous "Mes réservations".',
            'what types of transport are available' => 'You can book carpools, taxis, bicycles, or check public transport options.',
            'quels types de transports sont disponibles' => 'Vous pouvez réserver des covoiturages, des taxis, des vélos, ou consulter les options de transports publics.',
            'do i need an account' => 'Yes, creating an account is required to book a ride.',
            'ai-je besoin d\'un compte' => 'Oui, la création d\'un compte est requise pour réserver un trajet.',
            // Payment and Account (Paiement & Compte)
            'how do i enable two-factor authentication' => 'You can enable two-factor authentication in your account settings.',
            'comment activer l\'authentification à deux facteurs' => 'Vous pouvez activer l\'authentification à deux facteurs dans les paramètres de votre compte.',
            'can i store multiple credit cards' => 'Yes, you can store multiple credit cards in your account.',
            'puis-je enregistrer plusieurs cartes bancaires' => 'Oui, vous pouvez enregistrer plusieurs cartes bancaires dans votre compte.',
            'how does refund work in case of cancellation' => 'Refunds are processed according to the cancellation policy, and may vary based on timing.',
            'comment fonctionne le remboursement en cas d\'annulation' => 'Les remboursements sont traités selon la politique d\'annulation et peuvent varier en fonction du moment de l\'annulation.',
            'where can i find my billing history' => 'You can find your billing history in your account settings under "Billing".',
            'où puis-je trouver mon historique de facturation' => 'Vous pouvez trouver votre historique de facturation dans les paramètres de votre compte sous "Facturation".',
            'how do i download an invoice' => 'You can download an invoice as a PDF from the "Billing" section in your profile.',
            'comment télécharger une facture' => 'Vous pouvez télécharger une facture au format PDF depuis la section "Facturation" dans votre profil.',
            // Taxi
            'can i choose the type of vehicle' => 'Yes, you can choose the type of vehicle (sedan, van, etc.) when booking.',
            'puis-je choisir le type de véhicule' => 'Oui, vous pouvez choisir le type de véhicule (berline, van, etc.) lors de la réservation.',
            'how is the per-kilometer rate determined' => 'The rate is based on distance, traffic conditions, and the vehicle type.',
            'comment est-imposé le tarif kilométrique' => 'Le tarif est basé sur la distance, les conditions de circulation et le type de véhicule.',
            'is there an extra charge for large luggage' => 'Yes, there is an extra charge for large luggage.',
            'y a-t-il un supplément pour un gros bagage' => 'Oui, il y a un supplément pour un gros bagage.',
            'can i schedule a taxi in advance' => 'Yes, you can book a taxi in advance.',
            'puis-je réserver un taxi à l\'avance' => 'Oui, vous pouvez réserver un taxi à l\'avance.',
            // Bike
            'what are the bike station hours' => 'Bike stations are open from 6 AM to 10 PM daily.',
            'quels sont les horaires des stations de vélo' => 'Les stations de vélo sont ouvertes de 6 h à 22 h tous les jours.',
            'can i reserve an electric bike' => 'Yes, you can specifically reserve an electric bike.',
            'puis-je réserver un vélo électrique' => 'Oui, vous pouvez réserver spécifiquement un vélo électrique.',
            'how do i unlock the bike' => 'To unlock the bike, use the app to scan the QR code on the lock.',
            'comment déverrouiller le vélo' => 'Pour déverrouiller le vélo, utilisez l\'application pour scanner le code QR sur le cadenas.',
            'how far in advance can i reserve a bike' => 'You can reserve a bike up to 48 hours in advance.',
            'combien de temps à l\'avance puis-je réserver un vélo' => 'Vous pouvez réserver un vélo jusqu\'à 48 heures à l\'avance.',
            'what do i do if i get a flat tire' => 'You should report it through the app, and we will assist with the issue.',
            'que faire en cas de crevaison' => 'Vous devez le signaler via l\'application et nous vous aiderons avec le problème.',
            // Public Transport
            'where can i buy bus tickets' => 'You can buy bus tickets online through our website or app.',
            'où puis-je acheter des tickets de bus' => 'Vous pouvez acheter des tickets de bus en ligne via notre site web ou notre application.',
            'how do i top up my monthly pass' => 'You can top up your pass online or at any participating station.',
            'comment recharger mon abonnement mensuel' => 'Vous pouvez recharger votre abonnement en ligne ou dans n\'importe quelle station participante.',
            'how can i check for delays' => 'Real-time updates for delays are available in the app.',
            'comment vérifier les retards' => 'Les mises à jour en temps réel des retards sont disponibles dans l\'application.',
            'can i combine multiple modes of transport' => 'Yes, the app allows you to combine different modes of transport in one journey.',
            'puis-je combiner plusieurs modes de transport' => 'Oui, l\'application permet de combiner différents modes de transport dans un même trajet.',
            'how do i get a student pass' => 'You can get a student pass by submitting your student ID through the app.',
            'comment obtenir une carte étudiante' => 'Vous pouvez obtenir une carte étudiante en soumettant votre carte d\'étudiant via l\'application.',
            'can i use an intermodal pass' => 'Yes, you can use an intermodal pass to pay for multiple transport services.',
            'puis-je utiliser un abonnement intermodal' => 'Oui, vous pouvez utiliser un abonnement intermodal pour payer plusieurs services de transport.',
        ];

        // Loop through FAQ and check if the question matches
        foreach ($faq as $key => $response) {
            if (str_contains($question, $key)) {
                return $response;
            }
        }

        return "I'm sorry, I can only answer questions about our services. Please ask differently!";
    }
}
