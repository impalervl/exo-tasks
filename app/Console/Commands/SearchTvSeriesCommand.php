<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\SearchTvSeriesAction;
use App\Dto\TvScheduleDto;
use Core\Console\Contracts\CommandInterface;

class SearchTvSeriesCommand implements CommandInterface
{
    private SearchTvSeriesAction $searchTvSeriesAction;

    public function __construct()
    {
        $this->searchTvSeriesAction = new SearchTvSeriesAction();
    }

    public function handle(array $args): void
    {
        $date  = $args[0] ?? null;
        $title = $args[1] ?? null;

        echo json_encode($this->searchTvSeriesAction->handle(TvScheduleDto::create($date, $title))) . PHP_EOL;
    }

    public function getName(): string
    {
        return 'search-tv-series';
    }
}
