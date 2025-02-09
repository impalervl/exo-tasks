<?php
declare(strict_types=1);

namespace App\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;

class HomeController
{
    public function index(): JsonResponse
    {
        return new JsonResponse([
            'message' => 'Hello, World!',
            'status' => 'success',
        ], 200);
    }
}
