<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\MissingCharacterAction;
use Core\Console\Contracts\CommandInterface;

class MissingCharacterCommand implements CommandInterface
{
    private MissingCharacterAction $missingCharacterAction;

    public function __construct()
    {
        $this->missingCharacterAction = new MissingCharacterAction();
    }

    public function handle(array $args): void
    {
        echo 'Missing character:  ' . $this->missingCharacterAction->handle() . PHP_EOL;
    }

    public function getName(): string
    {
        return 'print-missing-character';
    }
}
