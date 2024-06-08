<?php

namespace App\Security;

use App\Repository\UserRepository;
use Mainick\KeycloakClientBundle\Interface\IamClientInterface;
use Mainick\KeycloakClientBundle\Token\AccessToken;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class AccessTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(
        private UserRepository $repository,
        private readonly IamClientInterface $iamClient,
        private readonly CustomUserProvider $userProvider,
    ) {
    }

    public function getUserBadgeFrom(string $accessToken): UserBadge
    {
        $token = new AccessToken();
        $token
            ->setToken($accessToken)
            ->setRefreshToken('')
            ->setExpires(3600)
            ->setValues([]);

        return new UserBadge($accessToken, fn () => $this->userProvider->loadUserByIdentifier($token));
    }
}
