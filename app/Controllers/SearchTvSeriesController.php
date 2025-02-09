<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Actions\SearchTvSeriesAction;
use App\Dto\TvScheduleDto;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SearchTvSeriesController
{
    private SearchTvSeriesAction $searchTvSeriesAction;

    public function __construct()
    {
        $this->searchTvSeriesAction = new SearchTvSeriesAction();
    }

    /**
     * @throws \DateMalformedStringException
     * @throws \Exception
     */
    public function index(Request $request): JsonResponse
    {
        $dto = TvScheduleDto::fromRequest($request);
        $dto->validate();

        return new JsonResponse($this->searchTvSeriesAction->handle($dto));
    }
}
