<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route("/logout", name: "app_logout", methods: ["GET"])]
    public function logout(): Response
    {
        return $this->redirectToRoute('mainick_keycloak_security_auth_logout');
    }
}
