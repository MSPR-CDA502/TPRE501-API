<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Mainick\KeycloakClientBundle\Interface\AccessTokenInterface;
use Mainick\KeycloakClientBundle\Interface\IamClientInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class CustomUserProvider implements UserProviderInterface
{
    public function __construct(
        private readonly IamClientInterface $iamClient,
        private EntityManagerInterface $em,
    ) {
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        $keycloakResourceOwner = $user->getKeycloakResourceOwner();
        if ($keycloakResourceOwner) {
            $accessToken = $keycloakResourceOwner->getAccessToken();
            if ($accessToken && $accessToken->hasExpired()) {
                $accessToken = $this->iamClient->refreshToken($accessToken);
            }

            return $this->loadUserByIdentifier($accessToken);
        }
    }

    public function supportsClass(string $class): bool
    {
        return User::class === $class;
    }

    public function loadUserByIdentifier($identifier): UserInterface
    {
        if (!$identifier instanceof AccessTokenInterface) {
            throw new \LogicException('Could not load a KeycloakUser without an AccessToken.');
        }

        try {
            $resourceOwner = $this->iamClient->fetchUserFromToken($identifier);
            if (!$resourceOwner) {
                throw new UserNotFoundException(sprintf('User with access token "%s" not found.', $identifier));
            }

            $user = $this->em->getRepository(User::class)->findOneBy(['email' => $resourceOwner->getEmail()]);

            if (!$user) {
                $user = new User();
                $user->setEmail($resourceOwner->getEmail());
                $user->setDisplayName($resourceOwner->getUsername());
                $user->setRoles($resourceOwner->getRoles());
                $user->setBirthdate(new \DateTimeImmutable());

                $user->setKeycloakResourceOwner($resourceOwner);

                $this->em->persist($user);
                $this->em->flush();
            } else {
                $user->setDisplayName($resourceOwner->getUsername());
                $user->setRoles($resourceOwner->getRoles());
                $user->setKeycloakResourceOwner($resourceOwner);

                $this->em->flush();
            }
        } catch (\UnexpectedValueException $e) {
            throw new UserNotFoundException(sprintf('User with access token "%s" not found.', $identifier));
        }

        return $user;
    }
}
