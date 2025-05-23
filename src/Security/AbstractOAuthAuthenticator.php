<?php

namespace App\Security;


use App\Entity\User;
use App\Repository\UserRepository;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

abstract class AbstractOAuthAuthenticator extends OAuth2Authenticator
{
    use TargetPathTrait;

    protected string $serviceName = '';

    public function __construct(
        private ClientRegistry           $clientRegistry,
        private RouterInterface          $router,
        private UserRepository           $repository,
        private OAuthRegistrationService $registrationService
    )
    {
    }

    public function supports(Request $request): ?bool
    {
        return 'connect_google_check' === $request->attributes->get('_route') &&
            $request->attributes->get('service') === $this->serviceName;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $targetPath = $this->getTargetPath($request->getSession(), $firewallName);
        if ($targetPath) {
            return new RedirectResponse($targetPath);
        }

        $user = $token->getUser();
        $roles = $user->getRoles();

        if (in_array('ROLE_COVOITUREUR', $roles, true)) {
            return new RedirectResponse($this->router->generate('backoffice_covoitureur_dashboard'));
        }

        if (in_array('ROLE_CHAUFFEUR_TAXI', $roles, true)) {
            return new RedirectResponse($this->router->generate('backoffice_chauffeur_dashboard'));
        }

        if (in_array('ROLE_VOYAGEUR', $roles, true)) {
            return new RedirectResponse($this->router->generate('backoffice_voyageur_dashboard'));
        }

// Default fallback
        return new RedirectResponse($this->router->generate('app_user_index'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        if ($request->hasSession()) {
            $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        }

        return new RedirectResponse($this->router->generate('app_login'));
    }


    public function authenticate(Request $request): SelfValidatingPassport
    {
        $credentials = $this->fetchAccessToken($this->getClient());
        $resourceOwner = $this->getResourceOwnerFromCredentials($credentials);
        $user = $this->getUserFromResourceOwner($resourceOwner, $this->repository);

        if (null === $user) {
            $user = $this->registrationService->persist($resourceOwner, $this->repository);
        }

        return new SelfValidatingPassport(
            userBadge: new UserBadge($user->getUserIdentifier(), fn() => $user),
            badges: [
                new RememberMeBadge()
            ]
        );
    }

    protected function getResourceOwnerFromCredentials(AccessToken $credentials): ResourceOwnerInterface
    {
        return $this->getClient()->fetchUserFromToken($credentials);
    }

    private function getClient(): OAuth2ClientInterface
    {
        return $this->clientRegistry->getClient($this->serviceName);
    }

    abstract protected function getUserFromResourceOwner(ResourceOwnerInterface $resourceOwner, UserRepository $repository): ?User;
}