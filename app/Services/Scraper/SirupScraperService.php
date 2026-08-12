<?php

namespace App\Services\Scraper;

use App\Repositories\SirupRepository;

class SirupScraperService
{
    public function __construct(private readonly SirupRepository $repository)
    {
    }

    public function prepareDailyUpdate(): array
    {
        return [
            'status' => 'prepared',
            'message' => 'Daily update pipeline is ready for scraper integration.',
            'summary' => $this->repository->summary(),
        ];
    }
}