<?php

declare(strict_types=1);

namespace App\Enums;

enum WeekEnum: int
{
    case MONDAY = 1;
    case TUESDAY = 2;
    case WEDNESDAY = 3;
    case THURSDAY = 4;
    case FRIDAY = 5;
    case SATURDAY = 6;
    case SUNDAY = 7;

    public static function getAllValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function isValidDay(string $day): bool
    {
        return in_array($day, self::getNames(), true);
    }

    private static function getNames(): array
    {
        return array_map(fn($case) => strtolower($case->name), self::cases());
    }
}
