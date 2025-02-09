<?php

declare(strict_types=1);

namespace App\Services\ABTest\Randomizers;

use App\Dto\DesignDto;
use App\Services\ABTest\Storages\ABTestStorageFactory;
use Exads\ABTestData;
use Exads\ABTestException;

class RandomRandomizer implements ABTestRandomizer
{

    private ABTestData $abTestData;

    public function __construct(ABTestData $testData)
    {
        $this->abTestData = $testData;
    }

    /**
     * @throws ABTestException
     */
    public function assignDesignByPercentage(int $promotionId): DesignDto
    {
        $designs = $this->abTestData->getAllDesigns();

        $totalWeight = array_sum(array_column($designs, 'splitPercent'));
        $random      = mt_rand(1, $totalWeight);

        foreach ($designs as $design) {
            if ($random <= $design['splitPercent']) {
                return DesignDto::fromArray($design);
            }
            $random -= $design['splitPercent'];
        }

        return DesignDto::fromArray($designs[0]);
    }
}
