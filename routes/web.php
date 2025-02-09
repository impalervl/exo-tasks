<?php

declare(strict_types=1);

use App\Controllers\ABTestDesignRedirectController;
use App\Controllers\GetABDesignPageController;
use App\Controllers\HomeController;
use App\Controllers\MissingCharacterController;
use App\Controllers\PrimeNumbersController;
use App\Controllers\SearchTvSeriesController;
use Core\Router\Router;

/** @var Router $router */
$router->get('/', [HomeController::class, 'index']);
$router->get('/prime-numbers', [PrimeNumbersController::class, 'index']);
$router->get('/missing-character', [MissingCharacterController::class, 'index']);
$router->get('/tv-series', [SearchTvSeriesController::class, 'index']);
$router->get('/ab-design-page', [GetABDesignPageController::class, 'index']);
$router->get('/promotion-designs/{promotion}', [ABTestDesignRedirectController::class, 'index']);
