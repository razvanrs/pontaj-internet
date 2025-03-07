<?php

namespace App\Observers;

use App\Models\EmployeeSchedule;
use Illuminate\Support\Str;

class EmployeeScheduleObserver
{

    /**
     * Handle the EmployeeSchedule "created" event.
     *
     * @param  \App\Models\EmployeeSchedule $EmployeeSchedule
     * @return void
     */
    public function creating(EmployeeSchedule $EmployeeSchedule)
    {
    }

    /**
     * Handle the EmployeeSchedule "created" event.
     */
    public function created(EmployeeSchedule $EmployeeSchedule): void
    {
        //
    }

    /**
     * Handle the EmployeeSchedule "updated" event.
     *
     * @param  \App\Models\EmployeeSchedule  $EmployeeSchedule
     * @return void
     */
    public function updating(EmployeeSchedule $EmployeeSchedule)
    {
        
    }

    /**
     * Handle the EmployeeSchedule "updated" event.
     */
    public function updated(EmployeeSchedule $EmployeeSchedule): void
    {
        //
    }

    /**
     * Handle the EmployeeSchedule "deleted" event.
     */
    public function deleted(EmployeeSchedule $EmployeeSchedule): void
    {
        //
    }

    /**
     * Handle the EmployeeSchedule "restored" event.
     */
    public function restored(EmployeeSchedule $EmployeeSchedule): void
    {
        //
    }

    /**
     * Handle the EmployeeSchedule "force deleted" event.
     */
    public function forceDeleted(EmployeeSchedule $EmployeeSchedule): void
    {
        //
    }
}
