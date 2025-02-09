<?php

declare(strict_types=1);

namespace Tests;

use Tests\Common\TestCase;

class PrimeNumbersTest extends TestCase
{
    public function testPrimeNumbers(): void
    {
        $response = $this->get('/prime-number');
        $result   = json_decode($response->getContent(), true);

        foreach ($result as $entry) {
            $number = (int) explode(' ', $entry)[0];
            $expectedDivisors = $this->parseDivisorsFromEntry($entry);

            $actualDivisors = $this->getDivisors($number);

            $this->assertEquals(
                $expectedDivisors,
                $actualDivisors,
                "Mismatch in divisors for number $number. Expected: "
                . implode(", ", $expectedDivisors) . ". Got: "
                . implode(", ", $actualDivisors)
            );
        }
    }

    private function parseDivisorsFromEntry(string $entry): array
    {
        preg_match('/\[(.*)\]/', $entry, $matches);
        return explode(', ', $matches[1]);
    }

    private function getDivisors(int $number): array
    {
        if ($number === 1) {
            return ['PRIME'];
        }

        $divisors = [];
        for ($i = 2; $i <= $number; $i++) {
            if ($number % $i === 0) {
                $divisors[] = $i;
            }
        }

        if (count($divisors) === 1) {
            return ['PRIME'];
        }

        return $divisors;
    }
}
