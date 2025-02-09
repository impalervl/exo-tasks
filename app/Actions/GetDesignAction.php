<?php

declare(strict_types=1);

namespace App\Actions;

use App\Dto\DesignDto;
use App\Services\ABTest\ABTestService;
use Exads\ABTestData;
use Exads\ABTestException;

class GetDesignAction
{
    /**
     * @throws ABTestException
     */
    public function handle(int $promotionId, int $designId): DesignDto
    {
        $testData = new ABTestData($promotionId);
        return (new ABTestService($testData))->getAssignedDesign($promotionId, $designId);
    }

    public function getFallbackPromotionId(): int
    {
        return rand(1, 3);
    }
}
