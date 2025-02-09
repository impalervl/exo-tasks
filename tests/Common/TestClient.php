<?php

declare(strict_types=1);

namespace Tests\Common;

use Core\Router\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TestClient
{
    private Router $router;

    public function __construct()
    {
        $router = new Router();
        require __DIR__ . '/../../routes/web.php';

        $this->router = $router;
    }

    public function get(string $uri, array $queryParams = []): Response
    {
        $request = new Request(query: $queryParams, server: ['REQUEST_URI' => $uri, 'REQUEST_METHOD' => 'GET']);
        return $this->router->dispatch($request);
    }
}
