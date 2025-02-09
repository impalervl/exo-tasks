<?php

declare(strict_types=1);

namespace App\Services\ABTest\Randomizers;

use App\Dto\DesignDto;
use App\Services\ABTest\Storages\ABTestStorageFactory;
use App\Services\ABTest\Storages\ABTestStorageInterface;
use Exads\ABTestData;
use Exads\ABTestException;

class StorageRandomizer implements ABTestRandomizer
{
    private ABTestStorageInterface $storage;

    private ABTestData $abTestData;

    public function __construct(ABTestData $testData)
    {
        $this->abTestData = $testData;
        $this->storage    = ABTestStorageFactory::getStorage();
    }

    /**
     * @throws ABTestException
     */
    public function assignDesignByPercentage(int $promotionId): DesignDto
    {
        $counts  = $this->storage->getDesignCounts($promotionId);
        $designs = $this->abTestData->getAllDesigns();

        $totalAssigned  = array_sum($counts);
        $expectedCounts = [];

        foreach ($designs as $design) {
            $expectedCounts[$design['designId']] = ($design['splitPercent'] / 100) * ($totalAssigned + 1);
        }

        $leastAssignedDesign = null;
        $smallestDiff        = PHP_INT_MAX;

        foreach ($designs as $design) {
            $actual   = $counts[$design['designId']] ?? 0;
            $expected = $expectedCounts[$design['designId']];
            $diff     = $expected - $actual;

            if ($diff > 0 && $diff < $smallestDiff) {
                $smallestDiff        = $diff;
                $leastAssignedDesign = $design;
            }
        }

        //ensures equality if original percentage split was changed
        if ($leastAssignedDesign === null) {
            $leastAssignedDesign = $this->pickDesignWithLargestSplit($designs);
        }

        $this->storage->incrementDesignCount($promotionId, $leastAssignedDesign['designId']);

        return DesignDto::fromArray($leastAssignedDesign);
    }

    private function pickDesignWithLargestSplit(array $designs): array
    {
        usort($designs, fn($a, $b) => $b['splitPercent'] <=> $a['splitPercent']);
        return $designs[0];
    }
}
