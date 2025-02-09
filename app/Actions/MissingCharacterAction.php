<?php

declare(strict_types=1);

namespace App\Actions;

class MissingCharacterAction
{
    private const int CHARACTERS_START = 44;

    private const int CHARACTERS_END = 124;

    public function handle(): string
    {
        $expectedSum   = $this->calculateExpectedSum(self::CHARACTERS_START, self::CHARACTERS_END);
        $fullArray     = range(self::CHARACTERS_START, self::CHARACTERS_END);
        $randomIndex   = array_rand($fullArray);
        $modifiedArray = $fullArray;

        unset($modifiedArray[$randomIndex]);

        //let's assume we don't know which character is missing so we need to get real sum from array
        //otherwise we could use $realSum = $expectedSum - $fullArray[$randomIndex]
        $realSum = array_sum($modifiedArray);

        return $this->findMissingCharacter($expectedSum, $realSum);
    }

    protected function calculateExpectedSum(int $start, int $end): int
    {
        return (($end - $start + 1) * ($start + $end)) / 2;
    }

    protected function findMissingCharacter(int $expectedSum, int $realSum): string
    {
        $missingCharAscii = $expectedSum - $realSum;

        return chr($missingCharAscii);
    }
}
