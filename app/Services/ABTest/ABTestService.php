<?php

declare(strict_types=1);

namespace App\Services\ABTest;

use App\Dto\DesignDto;
use App\Services\ABTest\Randomizers\ABTestRandomizer;
use App\Services\ABTest\Randomizers\RandomizerFactory;
use Exads\ABTestData;
use Exads\ABTestException;

class ABTestService
{
    private ABTestRandomizer $randomizer;

    private ABTestData $abTestData;

    public function __construct(ABTestData $testData)
    {
        $this->randomizer = RandomizerFactory::getRandomizer($testData);
        $this->abTestData = $testData;
    }

    public function getAssignedDesign(int $promotionId, int $designId = null): DesignDto
    {
        if ($designId) {
            try {
                return DesignDto::fromArray($this->abTestData->getDesign($designId));
            } catch (ABTestException $e) {
                // log warning, use fallback
            }
        }

        return $this->assignDesignByPercentage($promotionId);
    }

    private function assignDesignByPercentage(int $promotionId): DesignDto
    {
        return $this->randomizer->assignDesignByPercentage($promotionId);
    }
}
