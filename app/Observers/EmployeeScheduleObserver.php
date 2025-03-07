<?php

namespace App\Observers;

use App\Models\EmployeeSchedule;
use App\Services\ExtraHourService;

class EmployeeScheduleObserver
{
    protected $extraHourService;

    public function __construct(ExtraHourService $extraHourService)
    {
        $this->extraHourService = $extraHourService;
    }

    /**
     * Handle the EmployeeSchedule "created" event.
     */
    public function created(EmployeeSchedule $employeeSchedule): void
    {
        // Calculate and create extra hours when a schedule is created
        $this->extraHourService->calculateExtraHours($employeeSchedule);
    }

    /**
     * Handle the EmployeeSchedule "updated" event.
     */
    public function updated(EmployeeSchedule $employeeSchedule): void
    {
        // Only recalculate extra hours if the schedule times have changed
        if ($employeeSchedule->wasChanged('date_start') || $employeeSchedule->wasChanged('date_finish')) {
            // First, we might want to delete existing extra hours for this schedule
            // This would require extra logic to handle already reconciled hours

            // Then recalculate
            $this->extraHourService->calculateExtraHours($employeeSchedule);
        }
    }
}
