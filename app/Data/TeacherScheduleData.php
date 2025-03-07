<?php

namespace App\Data;

use App\Models\DayLimit;
use App\Models\TeacherSchedule;
use Carbon\Carbon;
use Spatie\LaravelData\Data;

class TeacherScheduleData extends Data
{
    public function __construct(
        public int $id,
        public int $learningActivityTypeId,
        public int $yearId,
        public int $weekId,
        public int $dayId,
        public int $moduleId,
        public int $abilityId,
        public int $themeId,
        public int $locationId,
        public int $teacherId,
        public string $title, //this fields should match the require event data in FullCalendar.io
        public string $start, //this fields should match the require event data in FullCalendar.io
        public string $end, //this fields should match the require event data in FullCalendar.io
        public int $totalMinutes,
        public string $backgroundColor,
        public string $borderColor,
        public string $allDay,
        public string $themeCode,
        public string $moduleCode,
        public string $locationCode
    ) {
    }

    public static function fromModel(TeacherSchedule $teacherSchedule): self
    {
        return new self(
            $teacherSchedule->id,
            $teacherSchedule->learning_activity_type_id,
            $teacherSchedule->year_id,
            $teacherSchedule->week_id,
            $teacherSchedule->day_id,
            $teacherSchedule->module_id,
            $teacherSchedule->ability_id,
            $teacherSchedule->theme_id,
            $teacherSchedule->location_id,
            $teacherSchedule->teacher_id,
            $teacherSchedule->module->code . " - " . $teacherSchedule->theme->code . " - " . " Sala  " . $teacherSchedule->location->code, //this is how the Fullcalendar.io displays data
            $teacherSchedule->date_start->format("Y-m-d H:i:s"),
            $teacherSchedule->date_finish->format("Y-m-d H:i:s"),
            $teacherSchedule->total_minutes,
            $teacherSchedule->learningActivityType->color,
            $teacherSchedule->learningActivityType->color,
            false,
            $teacherSchedule->module->code,
            $teacherSchedule->theme->code,
            $teacherSchedule->location->code,
        );
    }
}
