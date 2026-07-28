<?php

namespace App\Console\Commands;

use App\Services\Scraper\SirupScraperService;
use Illuminate\Console\Command;

class UpdateSirup extends Command
{
    protected $signature = 'sirup:update';

    protected $description = 'Prepare the daily Sirup update pipeline.';

    public function handle(SirupScraperService $service): int
    {
        $result = $service->prepareDailyUpdate();

        $this->info($result['message']);

        return self::SUCCESS;
    }
}