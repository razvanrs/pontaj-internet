<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\EmployeeSchedule;
use App\Models\ExtraHour;
use App\Services\ExtraHourService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RecalculateExtraHoursCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'extra-hours:recalculate
                            {--employee-id= : Specific employee ID to process}
                            {--start-date= : Start date (Y-m-d)}
                            {--end-date= : End date (Y-m-d)}
                            {--force : Force recalculation even if entries exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate extra hours for completed schedules';

    /**
     * Execute the console command.
     */
    public function handle(ExtraHourService $extraHourService)
    {
        $employeeId = $this->option('employee-id');
        $startDate = $this->option('start-date') ? Carbon::parse($this->option('start-date')) : Carbon::now()->subDays(30);
        $endDate = $this->option('end-date') ? Carbon::parse($this->option('end-date')) : Carbon::now();
        $force = $this->option('force');

        $this->info("Recalculating extra hours from {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}");

        // Build the query to get relevant employee schedules
        $query = EmployeeSchedule::query()
            ->where('schedule_status_id', 1) // Completed status
            ->whereBetween('date_start', [$startDate, $endDate]);

        // If employee ID is provided, filter by it
        if ($employeeId) {
            $query->where('employee_id', $employeeId);
            $this->info("Processing only employee ID: {$employeeId}");
        } else {
            $this->info("Processing all employees");
        }

        // Get the total count for progress bar
        $totalSchedules = $query->count();

        if ($totalSchedules === 0) {
            $this->error("No completed schedules found in the specified date range.");
            return 1;
        }

        $this->info("Found {$totalSchedules} completed schedules to process");

        // Create a progress bar
        $bar = $this->output->createProgressBar($totalSchedules);
        $bar->start();

        // Process each schedule
        $processedCount = 0;
        $createdCount = 0;
        $skippedCount = 0;

        $schedules = $query->get();

        foreach ($schedules as $schedule) {
            // Check if this schedule already has extra hours
            $existingHours = ExtraHour::where('employee_schedule_id', $schedule->id)->count();

            if ($existingHours > 0 && !$force) {
                $skippedCount++;
                $bar->advance();
                continue;
            }

            // If forced recalculation, delete existing entries
            if ($existingHours > 0 && $force) {
                ExtraHour::where('employee_schedule_id', $schedule->id)->delete();
                $this->line("\nDeleted {$existingHours} existing extra hour entries for schedule #{$schedule->id}");
            }

            // Calculate extra hours
            $extraHours = $extraHourService->calculateExtraHours($schedule);

            if ($extraHours && $extraHours->count() > 0) {
                $createdCount += $extraHours->count();
            }

            $processedCount++;
            $bar->advance();
        }

        $bar->finish();

        $this->newLine(2);
        $this->info("Completed processing {$processedCount} schedules");
        $this->info("Created {$createdCount} extra hour entries");
        $this->info("Skipped {$skippedCount} schedules (already had extra hours)");

        return 0;
    }
}
