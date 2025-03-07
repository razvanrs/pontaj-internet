<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\EmployeeSchedule;
use App\Models\ExtraHour;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class ExtraHourService
{
    /**
     * Calculate and create extra hours for a given employee schedule
     *
     * @param EmployeeSchedule $schedule
     * @return Collection|null
     */
    public function calculateExtraHours(EmployeeSchedule $schedule): ?Collection
    {
        // Skip if the schedule is not completed or has some other status that doesn't qualify
        if ($schedule->schedule_status_id != 1) { // Assuming 1 is the "completed" status
            return null;
        }

        // Get configuration values
        $workingStartTime = Config::get('extra_hours.regular_hours.start', 8); // Default 8 AM
        $workingEndTime = Config::get('extra_hours.regular_hours.end', 16);    // Default 4 PM
        $weekendDays = Config::get('extra_hours.weekend_days', [0, 6]);        // Default Saturday and Sunday

        // Define specific segment boundaries for overnight shifts
        $eveningSegment1End = 22; // 10 PM
        $nightSegment1End = 0;    // Midnight
        $nightSegment2End = 6;    // 6 AM
        $morningSegmentEnd = $workingStartTime; // 8 AM

        $extraHours = collect();

        // Convert schedule times to Carbon objects
        $scheduleStart = Carbon::parse($schedule->date_start);
        $scheduleEnd = Carbon::parse($schedule->date_finish);

        // Process the schedule based on whether it's on a weekend or a weekday
        if (in_array($scheduleStart->dayOfWeek, $weekendDays)) {
            // Weekend shift - all hours are extra
            $extraHours = $this->processWeekendShift($schedule, $scheduleStart, $scheduleEnd);
        } else {
            // Weekday shift - process according to specific segments
            $extraHours = $this->processWeekdayShift(
                $schedule,
                $scheduleStart,
                $scheduleEnd,
                $workingStartTime,
                $workingEndTime,
                $eveningSegment1End,
                $nightSegment1End,
                $nightSegment2End,
                $morningSegmentEnd
            );
        }

        return $extraHours->filter();
    }

    /**
     * Process a shift occurring on a weekend (all hours are extra)
     *
     * @param EmployeeSchedule $schedule
     * @param Carbon $start
     * @param Carbon $end
     * @return Collection
     */
    private function processWeekendShift(
        EmployeeSchedule $schedule,
        Carbon $start,
        Carbon $end
    ): Collection {
        $extraHours = collect();
        $currentDay = $start->copy()->startOfDay();
        $nextDay = $currentDay->copy()->addDay()->startOfDay();

        // If schedule is within the same day
        if ($start->isSameDay($end)) {
            $extraHours->push($this->createExtraHour(
                $schedule->employee_id,
                $schedule->id,
                $currentDay,
                $start,
                $end,
                $schedule->businessUnit->id ?? null,
                'Weekend shift: Full day'
            ));
        } else {
            // First day
            $extraHours->push($this->createExtraHour(
                $schedule->employee_id,
                $schedule->id,
                $currentDay,
                $start,
                $nextDay,
                $schedule->businessUnit->id ?? null,
                'Weekend shift: Day 1'
            ));

            // Additional days if schedule spans more than 2 days
            $currentDay = $nextDay->copy();
            while (!$currentDay->isSameDay($end) && $currentDay < $end) {
                $nextDay = $currentDay->copy()->addDay()->startOfDay();
                $extraHours->push($this->createExtraHour(
                    $schedule->employee_id,
                    $schedule->id,
                    $currentDay,
                    $currentDay,
                    $nextDay,
                    $schedule->businessUnit->id ?? null,
                    'Weekend shift: Intermediate day'
                ));
                $currentDay = $nextDay;
            }

            // Last day
            if ($currentDay < $end) {
                $extraHours->push($this->createExtraHour(
                    $schedule->employee_id,
                    $schedule->id,
                    $currentDay,
                    $currentDay,
                    $end,
                    $schedule->businessUnit->id ?? null,
                    'Weekend shift: Last day'
                ));
            }
        }

        return $extraHours;
    }

    /**
     * Process a shift occurring on weekdays (only non-regular hours are extra)
     * This implementation uses specific time segments as required
     *
     * @param EmployeeSchedule $schedule
     * @param Carbon $start
     * @param Carbon $end
     * @param int $workingStartTime
     * @param int $workingEndTime
     * @param int $eveningSegment1End
     * @param int $nightSegment1End
     * @param int $nightSegment2End
     * @param int $morningSegmentEnd
     * @return Collection
     */
    private function processWeekdayShift(
        EmployeeSchedule $schedule,
        Carbon $start,
        Carbon $end,
        int $workingStartTime,
        int $workingEndTime,
        int $eveningSegment1End,
        int $nightSegment1End,
        int $nightSegment2End,
        int $morningSegmentEnd
    ): Collection {
        $extraHours = collect();

        // Get the date for the start day
        $startDay = $start->copy()->startOfDay();

        // Define time boundaries for the first day
        $regularStart = $startDay->copy()->setHour($workingStartTime)->setMinute(0)->setSecond(0);
        $regularEnd = $startDay->copy()->setHour($workingEndTime)->setMinute(0)->setSecond(0);
        $eveningSegment1EndTime = $startDay->copy()->setHour($eveningSegment1End)->setMinute(0)->setSecond(0);
        $midnightTime = $startDay->copy()->addDay()->startOfDay();

        // Check if schedule starts before working hours on the first day
        if ($start < $regularStart) {
            // Morning extra hours (before regular hours)
            $extraHours->push($this->createExtraHour(
                $schedule->employee_id,
                $schedule->id,
                $startDay,
                $start,
                min($regularStart, $end),
                $schedule->businessUnit->id ?? null,
                'Morning extra hours (before work)'
            ));
        }

        // Check if schedule continues after working hours on the first day
        if ($end > $regularEnd && $start <= $regularEnd) {
            // Evening segment 1 (workingEndTime to eveningSegment1End)
            if ($end > $regularEnd) {
                $segmentEnd = min($eveningSegment1EndTime, $end);
                if ($segmentEnd > $regularEnd) {
                    $extraHours->push($this->createExtraHour(
                        $schedule->employee_id,
                        $schedule->id,
                        $startDay,
                        $regularEnd,
                        $segmentEnd,
                        $schedule->businessUnit->id ?? null,
                        'Evening extra hours (after work, before 22:00)'
                    ));
                }
            }

            // Evening segment 2 (eveningSegment1End to midnight)
            if ($end > $eveningSegment1EndTime) {
                $segmentEnd = min($midnightTime, $end);
                if ($segmentEnd > $eveningSegment1EndTime) {
                    $extraHours->push($this->createExtraHour(
                        $schedule->employee_id,
                        $schedule->id,
                        $startDay,
                        $eveningSegment1EndTime,
                        $segmentEnd,
                        $schedule->businessUnit->id ?? null,
                        'Night extra hours (22:00 to midnight)'
                    ));
                }
            }
        } else if ($start > $regularEnd) {
            // If shift starts after regular hours on first day

            // Evening segment 1 (workingEndTime to eveningSegment1End)
            if ($start < $eveningSegment1EndTime) {
                $segmentEnd = min($eveningSegment1EndTime, $end);
                $extraHours->push($this->createExtraHour(
                    $schedule->employee_id,
                    $schedule->id,
                    $startDay,
                    $start,
                    $segmentEnd,
                    $schedule->businessUnit->id ?? null,
                    'Evening extra hours (after work, before 22:00)'
                ));
            }

            // Evening segment 2 (eveningSegment1End to midnight)
            if ($start < $midnightTime && $end > $eveningSegment1EndTime) {
                $segmentStart = max($start, $eveningSegment1EndTime);
                $segmentEnd = min($midnightTime, $end);
                if ($segmentEnd > $segmentStart) {
                    $extraHours->push($this->createExtraHour(
                        $schedule->employee_id,
                        $schedule->id,
                        $startDay,
                        $segmentStart,
                        $segmentEnd,
                        $schedule->businessUnit->id ?? null,
                        'Night extra hours (22:00 to midnight)'
                    ));
                }
            }
        }

        // Handle overnight shifts (crossing to next day)
        if ($end > $midnightTime) {
            $nextDay = $midnightTime->copy();

            // Define time boundaries for the next day
            $nextDayRegularStart = $nextDay->copy()->setHour($workingStartTime)->setMinute(0)->setSecond(0);
            $nightSegment2EndTime = $nextDay->copy()->setHour($nightSegment2End)->setMinute(0)->setSecond(0);
            $morningSegmentEndTime = $nextDay->copy()->setHour($morningSegmentEnd)->setMinute(0)->setSecond(0);

            // Night segment 1 (midnight to 6 AM)
            if ($end > $nextDay) {
                $segmentEnd = min($nightSegment2EndTime, $end);
                $extraHours->push($this->createExtraHour(
                    $schedule->employee_id,
                    $schedule->id,
                    $nextDay,
                    $nextDay,
                    $segmentEnd,
                    $schedule->businessUnit->id ?? null,
                    'Night extra hours (midnight to 6:00)'
                ));
            }

            // Morning segment (6 AM to working start time, usually 8 AM)
            if ($end > $nightSegment2EndTime) {
                $segmentEnd = min($morningSegmentEndTime, $end);
                if ($segmentEnd > $nightSegment2EndTime) {
                    $extraHours->push($this->createExtraHour(
                        $schedule->employee_id,
                        $schedule->id,
                        $nextDay,
                        $nightSegment2EndTime,
                        $segmentEnd,
                        $schedule->businessUnit->id ?? null,
                        'Morning extra hours (6:00 to 8:00)'
                    ));
                }
            }

            // If shift extends beyond working start time on next day
            if ($end > $nextDayRegularStart) {
                // Additional processing for multi-day shifts if needed
                // This is for shifts that extend beyond the second day's working start time
            }
        }

        return $extraHours;
    }

    /**
     * Create an extra hour record
     */
    private function createExtraHour(
        int $employeeId,
        int $scheduleId,
        Carbon $date,
        Carbon $startTime,
        Carbon $endTime,
        ?int $businessUnitId,
        string $description = ''
    ): ?ExtraHour {
        // Check if the range is valid (at least 1 minute)
        if ($startTime->diffInMinutes($endTime) < 1) {
            return null;
        }

        return ExtraHour::create([
            'employee_id' => $employeeId,
            'employee_schedule_id' => $scheduleId,
            'date' => $date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'business_unit_id' => $businessUnitId,
            'description' => $description
        ]);
    }

    /**
     * Helper method to format minutes as hours:minutes
     */
    protected static function formatMinutes(int $minutes): string
    {
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        return $remainingMinutes > 0 ? 
            sprintf('%d.%02d', $hours, $remainingMinutes) : 
            sprintf('%d', $hours);
    }

    /**
     * Get available extra hours for an employee
     */
    public function getAvailableExtraHours(int $employeeId): Collection
    {
        return ExtraHour::getAvailableForEmployee($employeeId);
    }

    /**
     * Process expired extra hours (should be run via a scheduled task)
     */
    public function processExpiredExtraHours(): int
    {
        $today = Carbon::now()->startOfDay();

        $expiredCount = ExtraHour::where('expiry_date', '<', $today)
            ->where('is_fully_reconciled', false)
            ->update([
                'status' => 'expired',
                'is_fully_reconciled' => true
            ]);

        return $expiredCount;
    }

    /**
     * Get reconciled extra hours for an employee
     *
     * @param int $employeeId
     * @return Collection
     */
    public function getReconciledExtraHours(int $employeeId): Collection
    {
        return ExtraHour::where(function($query) use ($employeeId) {
                $query->where('employee_id', $employeeId)
                    ->where(function($q) {
                        $q->where('is_fully_reconciled', true)
                          ->orWhere(function($subQ) {
                              $subQ->where('is_fully_reconciled', false)
                                   ->whereRaw('remaining_minutes < total_minutes');
                          });
                    });
            })
            ->with(['reconciliations' => function($query) {
                $query->orderBy('reconciliation_date', 'desc');
            }])
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get()
            ->map(function($extraHour) {
                // Calculate reconciled minutes
                $reconciledMinutes = $extraHour->total_minutes - $extraHour->remaining_minutes;
                $lastReconciliationDate = null;
                
                if ($extraHour->reconciliations->isNotEmpty()) {
                    $lastReconciliationDate = $extraHour->reconciliations->first()->reconciliation_date;
                }
                
                return [
                    'id' => $extraHour->id,
                    'date' => $extraHour->date,
                    'start_time' => $extraHour->start_time,
                    'end_time' => $extraHour->end_time,
                    'total_minutes' => $extraHour->total_minutes,
                    'reconciled_minutes' => $reconciledMinutes,
                    'is_fully_reconciled' => $extraHour->is_fully_reconciled,
                    'description' => $extraHour->description,
                    'employee_schedule_id' => $extraHour->employee_schedule_id,
                    'last_reconciled_date' => $lastReconciliationDate
                ];
            });
    }
}
