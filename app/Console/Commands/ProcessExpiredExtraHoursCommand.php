<?php

namespace App\Console\Commands;

use App\Services\ExtraHourService;
use Illuminate\Console\Command;

class ProcessExpiredExtraHoursCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'extra-hours:process-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process expired extra hours';

    /**
     * Execute the console command.
     */
    public function handle(ExtraHourService $extraHourService)
    {
        $this->info("Processing expired extra hours...");

        $expiredCount = $extraHourService->processExpiredExtraHours();

        $this->info("Processed {$expiredCount} expired extra hour records");

        return 0;
    }
}
