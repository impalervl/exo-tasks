<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Actions\PrimeNumbersAction;
use Symfony\Component\HttpFoundation\JsonResponse;

class PrimeNumbersController
{
    private PrimeNumbersAction $primeNumbersAction;

    public function __construct()
    {
        $this->primeNumbersAction = new PrimeNumbersAction();
    }

    /**
     * @throws \DateMalformedStringException
     * @throws \Exception
     */
    public function index(): JsonResponse
    {
        return new JsonResponse($this->primeNumbersAction->handle());
    }
}
