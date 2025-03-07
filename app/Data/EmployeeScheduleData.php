<?php
namespace App\Data;
use App\Models\DayLimit;
use App\Models\EmployeeSchedule;
use Carbon\Carbon;
use Spatie\LaravelData\Data;

class EmployeeScheduleData extends Data
{
    public function __construct(
        public int $id,
        public int $yearId,
        public int $weekId,
        public int $dayId,
        public int $scheduleStatusId,
        public int $employeeId,
        public string $title, //this fields should match the require event data in FullCalendar.io
        public string $start, //this fields should match the require event data in FullCalendar.io
        public string $end, //this fields should match the require event data in FullCalendar.io
        public int $totalMinutes,
        public string $backgroundColor,
        public string $borderColor,
        public string $allDay,
        public string $shortTitle,
    ) {
    }

    public static function fromModel(EmployeeSchedule $employeeSchedule): self
    {
        $totalMinutes = $employeeSchedule->total_minutes;
        $totalHours = $totalMinutes / 60; // Convert minutes to hours
        // Round the total hours to two decimal places
        $totalHours = round($totalHours, 2);
        
        // Get color from the schedule status - this is the key change
        $color = $employeeSchedule->scheduleStatus->color ?? '#ff0000';
        
        return new self(
            $employeeSchedule->id,
            $employeeSchedule->year_id,
            $employeeSchedule->week_id,
            $employeeSchedule->day_id,
            $employeeSchedule->schedule_status_id,
            $employeeSchedule->employee_id,
            ($employeeSchedule->display_code ? $employeeSchedule->display_code : $employeeSchedule->scheduleStatus->name) . " (" . $totalHours . " ore)", //this is how the Fullcalendar.io displays data
            $employeeSchedule->date_start->format("Y-m-d H:i:s"),
            $employeeSchedule->date_finish->format("Y-m-d H:i:s"),
            $employeeSchedule->total_minutes,
            $color,
            $color,
            false,
            ($employeeSchedule->display_code ? $employeeSchedule->display_code : $employeeSchedule->scheduleStatus->name) . " (" . $totalHours . " ore)", //this is how the Fullcalendar.io displays data
        );
    }
}