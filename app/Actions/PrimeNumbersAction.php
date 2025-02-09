<?php

declare(strict_types=1);

namespace App\Actions;

class PrimeNumbersAction
{
    public function handle(): array
    {
        $output = [];

        for ($n = 1; $n <= 100; $n++) {
            if ($n === 1) {
                $output[] = "1 [PRIME]";
                continue;
            }

            $divisors = $this->getDivisors($n);

            if (count($divisors) === 1 && $divisors[0] === $n) {
                $output[] = "$n [PRIME]";
            } else {
                $output[] = "$n [" . implode(", ", $divisors) . "]";
            }
        }

        return $output;
    }

    protected function getDivisors(int $n): array
    {
        $divisors = [];
        for ($i = 2; $i <= sqrt($n); $i++) {
            if ($n % $i === 0) {
                $divisors[] = $i;
                if ($i !== $n / $i) {
                    $divisors[] = $n / $i;
                }
            }
        }

        sort($divisors, SORT_NUMERIC);
        $divisors[] = $n;

        return $divisors;
    }
}
