<?php

declare(strict_types=1);

namespace Core\Console\Contracts;

interface CommandInterface
{
    public function handle(array $args): void;

    public function getName(): string;
}
