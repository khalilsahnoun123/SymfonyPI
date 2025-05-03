<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;

final class OAuthRegistrationService
{
    public function __construct(private EntityManagerInterface $em) {}

    public function persist(ResourceOwnerInterface $resourceOwner): User
    {
        /** @var GoogleUser $googleUser */
        $googleUser = $resourceOwner;

        $user = (new User())
            ->setEmail($googleUser->getEmail())
            ->setGoogleId($googleUser->getId())
            ->setFirstName($googleUser->getFirstName() ?? 'Google')
            ->setLastName($googleUser->getLastName() ?? 'User')
            ->setNom($googleUser->getFirstName() ?? 'Google') // required!
            ->setRoles(['ROLE_USER'])
            ->setIsVerified(true);

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
}
