<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\PrimeNumbersAction;
use Core\Console\Contracts\CommandInterface;

class PrimeNumbersCommand implements CommandInterface
{
    private PrimeNumbersAction $primeNumbersAction;

    public function __construct()
    {
        $this->primeNumbersAction = new PrimeNumbersAction();
    }

    public function handle(array $args): void
    {
        foreach ($this->primeNumbersAction->handle() as $line) {
            echo $line . PHP_EOL;
        }
    }

    public function getName(): string
    {
        return 'print-prime-numbers';
    }
}
