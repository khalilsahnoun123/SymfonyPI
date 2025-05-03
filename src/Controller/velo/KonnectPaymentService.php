<?php
// src/Service/KonnectPaymentService.php

namespace App\Controller\velo;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class KonnectPaymentService
{
    private HttpClientInterface $client;
    private string $apiKey;
    private string $baseUrl;
    private string $domain;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
        $this->apiKey = '680e79d636eae5c3e033a843:D4Swagmv2wEsXGkkCMx';
        $this->baseUrl = 'https://api.preprod.konnect.network/api/v2';
        $this->domain = 'https://127.0.0.1:8000'; // your domain
    }

    public function createPayment(int $amountInMillimes, string $receiverWalletId): ?string
    {
        $response = $this->client->request('POST', $this->baseUrl . '/payments/init-payment', [
            'headers' => [
                'x-api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'receiverWalletId' => $receiverWalletId,
                'token' => 'TND',
                'amount' => $amountInMillimes,
                'type' => 'immediate',
                'callbackUrls' => [
                    'success' => $this->domain . '/reservationvelo/payment/success',
                    'fail' => $this->domain . '/reservationvelo/payment/fail',
                ],
            ],
        ]);

        $data = $response->toArray(false);

        return $data['payUrl'] ?? null;
    }

    public function checkPaymentStatus(string $paymentRef): ?string
    {
        $response = $this->client->request('GET', $this->baseUrl . '/payments/' . $paymentRef, [
            'headers' => [
                'x-api-key' => $this->apiKey,
            ],
        ]);

        $data = $response->toArray(false);

        return $data['payment']['status'] ?? null;
    }
}
