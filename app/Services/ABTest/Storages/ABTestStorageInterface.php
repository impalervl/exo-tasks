<?php

declare(strict_types=1);

namespace App\Services\AbTest\Storages;

interface ABTestStorageInterface
{
    public function getDesignCounts(int $promotionId): array;

    public function incrementDesignCount(int $promotionId, int $designId): void;
}
