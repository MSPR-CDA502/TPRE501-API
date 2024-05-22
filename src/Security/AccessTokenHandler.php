<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class AccessTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(
        private UserRepository $repository
    ) {
        dump('construct');
    }

    public function getUserBadgeFrom(string $accessToken): UserBadge
    {
        dump('here', $accessToken);
        $accessToken = $this->repository->findOneBy(['email' => $accessToken]);
        if (null === $accessToken) {
            throw new BadCredentialsException('Invalid credentials.');
        }

        return new UserBadge($accessToken->getEmail());
    }
}
