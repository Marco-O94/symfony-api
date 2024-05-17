<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ApiLoginController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(#[CurrentUser] ?User $user): JsonResponse
    {
        if (!$this->IsGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->json(['message' => 'Richiesta di login non valida'], Response::HTTP_UNAUTHORIZED);
        }


        return $this->json([
            'user' => $user->getUserIdentifier(),
        ]);
    }

    #[Route('/api/logout', name: 'api_logout', methods: ['POST'])]
    public function logout(): JsonResponse
    {
        // Invalidate Cookie session
        setcookie('PHPSESSID', '', time() - 3600, '/');


        return $this->json(['message' => 'Logout effettuato con successo']);
    }
}
