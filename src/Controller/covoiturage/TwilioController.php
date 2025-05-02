<?php

namespace App\Controller\covoiturage;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twilio\Rest\Client;

class TwilioController extends AbstractController
{
    #[Route('/send-sms', name: 'send_sms')]
    public function sendSms(): Response
    {
        $sid = $_ENV['TWILIO_SID'];
        $token = $_ENV['TWILIO_TOKEN'];
        $from = $_ENV['TWILIO_FROM'];

        $twilio = new Client($sid, $token);

        try {
            $twilio->messages->create(
                '+1234567890', // Recipient phone number
                [
                    'from' => $from,
                    'body' => 'Hello from Symfony 6.4 and Twilio!'
                ]
            );

            return new Response('SMS sent successfully!');
        } catch (\Exception $e) {
            return new Response('Failed to send SMS: ' . $e->getMessage());
        }
    }
}
