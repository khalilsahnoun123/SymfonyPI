<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

class SocialController extends AbstractController
{
    public const SCOPES = [
        'google' => [],
    ];

    #[Route('/connect/{service}', name: 'connect_google_start')]
    public function connect(string $service, ClientRegistry $clientRegistry): RedirectResponse
    {
        if (! in_array($service, array_keys(self::SCOPES), true)) {
            throw $this->createNotFoundException();
        }

        return $clientRegistry
            ->getClient($service)
            ->redirect(self::SCOPES[$service]);
    }

    #[Route('/connect/{service}/check', name: 'connect_google_check')]
    public function check(): Response
    {
        return new Response(status: 200);
    }
}
