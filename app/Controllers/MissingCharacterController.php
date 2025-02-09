<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Actions\MissingCharacterAction;
use Symfony\Component\HttpFoundation\JsonResponse;

class MissingCharacterController
{
    private MissingCharacterAction $missingCharacterAction;

    public function __construct()
    {
        $this->missingCharacterAction = new MissingCharacterAction();
    }

    /**
     * @throws \DateMalformedStringException
     * @throws \Exception
     */
    public function index(): JsonResponse
    {
        return new JsonResponse(['missing_character' => $this->missingCharacterAction->handle()]);
    }
}
