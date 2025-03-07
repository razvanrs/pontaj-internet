<?php

namespace App\Console\Commands;

use App\Services\ExtraHourService;
use Illuminate\Console\Command;

class ExpireExtraHours extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'extra-hours:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark expired extra hours as no longer available';

    /**
     * The extra hour service.
     *
     * @var \App\Services\ExtraHourService
     */
    protected $extraHourService;

    /**
     * Create a new command instance.
     *
     * @param \App\Services\ExtraHourService $extraHourService
     * @return void
     */
    public function __construct(ExtraHourService $extraHourService)
    {
        parent::__construct();
        $this->extraHourService = $extraHourService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Processing expired extra hours...');

        $count = $this->extraHourService->processExpiredExtraHours();

        $this->info("Processed {$count} expired extra hour records.");

        return 0;
    }
}
