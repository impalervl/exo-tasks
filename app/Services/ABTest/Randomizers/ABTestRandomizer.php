<?php

declare(strict_types=1);

namespace App\Services\ABTest\Randomizers;

use App\Dto\DesignDto;

interface ABTestRandomizer
{
    public function assignDesignByPercentage(int $promotionId): DesignDto;
}
