<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/oauth/keycloack', name: 'app_oauth_keycloack')]
    public function oAuthKeycloack(ClientRegistry $clientRegistry): Response
    {
        return $clientRegistry
        ->getClient('keycloack')
        ->redirect(['email', 'openid'], []);
    }

    #[Route(path: '/oauth/keycloack/callback', name: 'app_oauth_keycloack_callback')]
    public function oAuthKeycloackCallback(): void
    {
    }
}
