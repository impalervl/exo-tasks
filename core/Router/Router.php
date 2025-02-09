<?php

declare(strict_types=1);

namespace Core\Router;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Router
{
    public array $routes = [];

    public function get(string $uri, callable|array $action): void
    {
        $this->routes['GET'][$uri] = $action;
    }

    public function dispatch(Request $request): Response
    {
        $method = $request->getMethod();
        $path = $request->getPathInfo();

        foreach ($this->routes[$method] as $uri => $action) {
            $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $uri);
            $pattern = "#^$pattern$#";

            if (preg_match($pattern, $path, $matches)) {
                [$class, $method] = $action;
                $controller = new $class();

                $request->attributes->replace($matches);

                $response = $controller->$method($request);

                if ($response instanceof Response) {
                    return $response;
                }
            }
        }

        return new JsonResponse(['error' => 'Route not found'], 404);
    }
}
