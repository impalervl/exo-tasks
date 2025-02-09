<?php

declare(strict_types=1);

namespace App\Actions;

use App\Dto\TvScheduleDto;
use App\Enums\WeekEnum;
use Core\Services\DatabaseService;

class SearchTvSeriesAction
{
    private DatabaseService $dbService;

    public function __construct()
    {
        $this->dbService = DatabaseService::getInstance();
    }

    public function handle(TvScheduleDto $tvScheduleDto): array
    {
        return $this->map($this->dbService->select(
            'tv_series',
            [
                'tv_series.id',
                'tv_series.title',
                'tv_series.channel',
                'tv_series.genre',
                'tv_series_intervals.week_day',
                'tv_series_intervals.show_time',
            ],
            [
                [
                    'type'      => 'INNER',
                    'table'     => 'tv_series_intervals',
                    'condition' => 'tv_series.id = tv_series_intervals.id_tv_series',
                ]
            ],
            $this->buildConditions($tvScheduleDto),
            'tv_series.id ASC',
            1
        ));
    }

    protected function buildConditions(TvScheduleDto $tvScheduleDto): array
    {
        $conditions = [
            ['show_time', '>=', $tvScheduleDto->time],
            ['week_day', '=', $tvScheduleDto->day->value]
        ];

        if ($tvScheduleDto->showTitle) {
            $conditions[] = ['title', 'LIKE', $tvScheduleDto->showTitle];
        }

        return $conditions;
    }

    protected function map(array $data): array
    {
        return array_map(fn(array $item) => array_merge(
            $item,
            ['week_day' => WeekEnum::tryFrom($item['week_day'])->name]
        ), $data);
    }
}
