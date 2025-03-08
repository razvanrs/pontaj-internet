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

        $extraHours = collect();

        // Convert schedule times to Carbon objects
        $scheduleStart = Carbon::parse($schedule->date_start);
        $scheduleEnd = Carbon::parse($schedule->date_finish);
        
        // Process the schedule based on whether it's on a weekend or a weekday
        if (in_array($scheduleStart->dayOfWeek, $weekendDays)) {
            // For weekend shifts - all hours count as extra
            $extraHour = ExtraHour::create([
                'employee_id' => $schedule->employee_id,
                'employee_schedule_id' => $schedule->id,
                'date' => $scheduleStart->format('Y-m-d'),
                'start_time' => $scheduleStart,
                'end_time' => $scheduleEnd,
                'business_unit_id' => $schedule->businessUnit->id ?? null,
                'description' => 'Weekend: Toate orele',
            ]);
            
            $extraHours->push($extraHour);
        } else {
            // For weekday shifts
            $regularStart = $scheduleStart->copy()->setTime($workingStartTime, 0, 0);
            $regularEnd = $scheduleStart->copy()->setTime($workingEndTime, 0, 0);
            
            // If schedule starts before regular hours
            if ($scheduleStart < $regularStart) {
                $extraHour = ExtraHour::create([
                    'employee_id' => $schedule->employee_id,
                    'employee_schedule_id' => $schedule->id,
                    'date' => $scheduleStart->format('Y-m-d'),
                    'start_time' => $scheduleStart,
                    'end_time' => min($regularStart, $scheduleEnd),
                    'business_unit_id' => $schedule->businessUnit->id ?? null,
                    'description' => 'Ore suplimentare: înainte de program',
                ]);
                
                $extraHours->push($extraHour);
            }
            
            // If schedule extends beyond regular hours
            if ($scheduleEnd > $regularEnd) {
                $extraHour = ExtraHour::create([
                    'employee_id' => $schedule->employee_id,
                    'employee_schedule_id' => $schedule->id,
                    'date' => $scheduleStart->format('Y-m-d'),
                    'start_time' => max($regularEnd, $scheduleStart),
                    'end_time' => $scheduleEnd,
                    'business_unit_id' => $schedule->businessUnit->id ?? null,
                    'description' => 'Ore suplimentare: după program',
                ]);
                
                $extraHours->push($extraHour);
            }
        }

        return $extraHours->filter();
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
