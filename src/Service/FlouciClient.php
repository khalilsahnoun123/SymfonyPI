<?php

// src/Service/FlouciClient.php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class FlouciClient
{
    private HttpClientInterface $client;
    private string $token;
    private string $secret;
    private string $trackingId;
    private string $baseUrl;
    
    public function __construct(HttpClientInterface $client, string $token, string $secret, string $trackingId, string $baseUrl)
    {
        $this->client  = $client;
        $this->token   = $token;
        $this->secret  = $secret;
        $this->trackingId = $trackingId;
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    public function generatePayment(int $amountMillimes, string $successLink, string $failLink): array
    {
        $resp = $this->client->request('POST', $this->baseUrl.'/generate_payment', [
            'json' => [
                'app_token'             => $this->token,
                'app_secret'            => $this->secret,
                'amount'                => (string) $amountMillimes,
                'developer_tracking_id' =>  $this->trackingId,
                'success_link'          => $successLink,
                'fail_link'             => $failLink,
                'accept_card'           => true,
                'session_timeout_secs'  => 1200
            ],
        ]);

        if (!in_array($resp->getStatusCode(), [200, 201], true)) {
            $body = $resp->getContent(false);
            throw new RuntimeException("Flouci returned {$resp}: $body");
        }
        
        $payload = $resp->toArray(false);
        if (!isset($payload['result']['link'], $payload['result']['payment_id'])) {
            throw new RuntimeException('Invalid response from Flouci');
        }
        
        return $payload['result'];
    }

    public function verifyPayment(string $paymentId): array
    {
        $resp = $this->client->request('GET', $this->baseUrl."/verify_payment/{$paymentId}", [
            'headers' => [
                'apppublic' => $this->token,
                'appsecret' => $this->secret,
            ],
        ]);

        if (200 !== $resp->getStatusCode()) {
            throw new BadRequestException('Flouci verify failed');
        }
        return $resp->toArray()['result'];
    }
}
