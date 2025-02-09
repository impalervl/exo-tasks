<?php

declare(strict_types=1);

namespace App\Services\ABTest\Randomizers;

use Core\Helpers\Config;
use Exads\ABTestData;

class RandomizerFactory
{
    public static function getRandomizer(ABTestData $testData): ABTestRandomizer
    {
        $randomizer = Config::get('ab-test.randomizer');

        return match ($randomizer) {
            'storage' => new StorageRandomizer($testData),
            'random'  => new RandomRandomizer($testData),
            default   => new RandomRandomizer($testData),
        };
    }
}
