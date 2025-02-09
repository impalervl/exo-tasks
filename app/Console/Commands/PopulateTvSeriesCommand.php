<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\WeekEnum;
use Core\Console\Contracts\CommandInterface;
use Core\Services\DatabaseService;

class PopulateTvSeriesCommand implements CommandInterface
{
    private DatabaseService $dbService;

    private array $genres = ['Action', 'Comedy', 'Drama', 'Thriller', 'Romance'];

    private array $titles = [
        'Breaking Bad', 'Game of Thrones', 'Stranger Things', 'Friends', 'The Office',
        'The Crown', 'The Mandalorian', 'Black Mirror', 'Better Call Saul', 'The Witcher',
        'The Simpsons', 'Narcos', 'The Big Bang Theory', 'Money Heist', 'Sherlock',
        'The Walking Dead', 'Dark', 'The Boys', 'Westworld', 'Vikings'
    ];

    private array $channels = ['HBO', 'Netflix', 'BBC', 'NBC', 'Fox'];

    public function __construct()
    {
        $this->dbService = DatabaseService::getInstance();
    }

    public function handle(array $args): void
    {
        $count = isset($args[0]) ? (int) $args[0] : 33;

        $tvSeries = $this->createTvSeries($count);
        echo  'Populated TV Series successfully.' . PHP_EOL;

        $this->createTvSeriesIntervals($tvSeries);
        echo 'Populated TV series intervals successfully.' . PHP_EOL;
    }

    public function getName(): string
    {
        return 'populate-tv-series';
    }

    private function createTvSeries(int $count): array
    {
        $tvSeries             = [];
        $existingCombinations = [];
        $existingCount        = $this->dbService->count('tv_series');

        for ($i = $existingCount + 1; $i < $count + $existingCount + 1; $i++) {
            do {
                $title   = $this->titles[array_rand($this->titles)];
                $channel = $this->channels[array_rand($this->channels)];
            } while (in_array([$title, $channel], $existingCombinations));

            $existingCombinations[] = [$title, $channel];
            $genre                  = $this->genres[array_rand($this->genres)];
            $tvSeries[]             = ['id' => $i, 'title' => $title, 'channel' => $channel, 'genre' => $genre];
        }

        $this->dbService->insert('tv_series', $tvSeries);

        return array_map(
            fn($serial) => array_merge($serial, ['week_days' => $this->generateRandomWeekDays()]),
            $tvSeries
        );
    }

    private function createTvSeriesIntervals(array $tvSeriesArray): void
    {
        $insertValues = [];
        $usedTimes    = [];

        foreach ($tvSeriesArray as $tvSeries) {
            $tvSeriesId = $tvSeries['id'];
            $weekDays   = $tvSeries['week_days'];

            foreach ($weekDays as $day) {
                $showTime       = $this->generateUniqueShowtime($tvSeriesId, $day, $usedTimes);
                $insertValues[] = ['id_tv_series' => $tvSeriesId, 'week_day' => $day, 'show_time' => $showTime];
            }
        }

        $this->dbService->insert('tv_series_intervals', $insertValues);
    }

    private function generateUniqueShowtime(int $tvSeriesId, int $day, array &$usedTimes): string
    {
        $showTime = sprintf('%02d:%02d', rand(0, 23), rand(0, 59));

        while (isset($usedTimes[$day][$tvSeriesId]) && in_array($showTime, $usedTimes[$day][$tvSeriesId])) {
            $showTime = sprintf('%02d:%02d', rand(0, 23), rand(0, 59));
        }

        $usedTimes[$day][$tvSeriesId][] = $showTime;

        return $showTime;
    }

    private function generateRandomWeekDays(): array
    {
        $daysCount  = rand(1, 7);
        $weekDays   = WeekEnum::getAllValues();
        $randomDays = [];

        while (count($randomDays) < $daysCount) {
            $randomDay = $weekDays[array_rand($weekDays)];
            if (!in_array($randomDay, $randomDays)) {
                $randomDays[] = $randomDay;
            }
        }

        return $randomDays;
    }
}
