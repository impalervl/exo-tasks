<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap/app.php';

use Core\Router\Router;
use Symfony\Component\HttpFoundation\Request;

$router = new Router();

require_once __DIR__ . '/../routes/web.php';

$request = Request::createFromGlobals();
$response = $router->dispatch($request);

$response->send();
