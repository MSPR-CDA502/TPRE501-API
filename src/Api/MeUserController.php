<?php

namespace App\Api;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class MeUserController extends AbstractController
{
    public function __invoke(): ?User
    {
        return $this->getUser();
    }
}
