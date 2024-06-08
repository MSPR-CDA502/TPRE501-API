<?php

namespace App\Security;

use App\Security\CustomUserProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Mainick\KeycloakClientBundle\DTO\KeycloakAuthorizationCodeEnum;
use Mainick\KeycloakClientBundle\Interface\IamClientInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\InteractiveAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class CustomAunthenticator extends AbstractAuthenticator implements InteractiveAuthenticatorInterface
{
    public function __construct(
        private readonly IamClientInterface $iamClient,
        private readonly CustomUserProvider $userProvider,
    ) {
    }

    public function supports(Request $request): ?bool
    {
        return 'mainick_keycloak_security_auth_connect_check' === $request->attributes->get('_route');
    }

    public function authenticate(Request $request): Passport
    {
        $queryState = $request->query->get(KeycloakAuthorizationCodeEnum::STATE_KEY);
        $sessionState = $request->getSession()->get(KeycloakAuthorizationCodeEnum::STATE_SESSION_KEY);
        if (null === $queryState || $queryState !== $sessionState) {
            throw new AuthenticationException(sprintf('query state (%s) is not the same as session state (%s)', $queryState ?? 'NULL', $sessionState ?? 'NULL'));
        }

        $queryCode = $request->query->get(KeycloakAuthorizationCodeEnum::CODE_KEY);
        if (null === $queryCode) {
            throw new AuthenticationException('Authentication failed! Did you authorize our app?');
        }

        try {
            $accessToken = $this->iamClient->authenticateCodeGrant($queryCode);
        }
        catch (IdentityProviderException $e) {
            throw new AuthenticationException(sprintf('Error authenticating code grant (%s)', $e->getMessage()), previous: $e);
        }
        catch (\Exception $e) {
            throw new AuthenticationException(sprintf('Bad status code returned by openID server (%s)', $e->getStatusCode()), previous: $e);
        }

        if (!$accessToken || !$accessToken->getToken()) {
            throw new CustomUserMessageAuthenticationException('No access token provided');
        }

        if (!$accessToken->getRefreshToken()) {
            throw new CustomUserMessageAuthenticationException('Refresh token not found');
        }

        return new SelfValidatingPassport(new UserBadge($accessToken->getToken(), fn () => $this->userProvider->loadUserByIdentifier($accessToken)));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new Response($exception->getMessage(), Response::HTTP_FORBIDDEN);
    }

    public function isInteractive(): bool
    {
        return true;
    }
}
