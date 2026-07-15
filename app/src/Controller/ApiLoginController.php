<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class ApiLoginController extends AbstractController
{
    #[Route('/api/login', name: 'api_login')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome!',
        ]);
    }
}
