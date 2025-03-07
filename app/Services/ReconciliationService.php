<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\ExtraHour;
use App\Models\ExtraHourReconciliation;
use App\Models\EmployeeScheduleReconcile;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReconciliationService
{
    public function createReconciliation(
        int $employeeId,
        array $extraHourIds,
        array $minutesReconciled,
        string $notes = '', // Default to empty string
        $reconciliationDate = null,
        ?int $businessUnitId = null
    ): EmployeeScheduleReconcile {
        // Validate input
        if (empty($extraHourIds) || empty($minutesReconciled) || count($extraHourIds) !== count($minutesReconciled)) {
            throw new \InvalidArgumentException('Invalid input parameters');
        }
    
        // Ensure $notes is a string (defensive programming)
        $notes = (string)$notes;
    
        // Set reconciliation date to today if not provided
        if ($reconciliationDate === null) {
            $reconciliationDate = Carbon::now();
        } elseif (is_string($reconciliationDate)) {
            $reconciliationDate = Carbon::parse($reconciliationDate);
        }

        // Calculate total minutes for the reconciliation
        $totalMinutes = array_sum($minutesReconciled);

        // Calculate total hours
        $totalHours = ceil($totalMinutes / 60);

        // Fetch the first extra hour to get its employee_schedule_id
        $firstExtraHour = ExtraHour::findOrFail($extraHourIds[0]);
        $employeeScheduleId = $firstExtraHour->employee_schedule_id;

        // Set date_start and date_finish for the reconciliation
        $dateStart = $reconciliationDate->copy()->startOfDay();
        $dateFinish = $reconciliationDate->copy()->endOfDay();

        // Begin transaction
        return DB::transaction(function () use (
            $employeeId,
            $extraHourIds,
            $minutesReconciled,
            $notes,
            $reconciliationDate,
            $businessUnitId,
            $totalMinutes,
            $totalHours,
            $employeeScheduleId,
            $dateStart,
            $dateFinish
        ) {
            // Create the main reconciliation record
            $reconcile = EmployeeScheduleReconcile::create([
                'employee_id' => $employeeId,
                'employee_schedule_id' => $employeeScheduleId,
                'reconcile_date' => $reconciliationDate,
                'date_start' => $dateStart,
                'date_finish' => $dateFinish,
                'total_minutes' => $totalMinutes,
                'total_hours' => $totalHours,
                'status' => 'pending',
                'notes' => $notes
            ]);

            // Create individual reconciliations for each extra hour
            for ($i = 0; $i < count($extraHourIds); $i++) {
                $extraHourId = $extraHourIds[$i];
                $minutesReconciledForHour = $minutesReconciled[$i];

                if ($minutesReconciledForHour <= 0) {
                    continue; // Skip if no minutes reconciled
                }

                // Create the extra hour reconciliation
                ExtraHourReconciliation::create([
                    'employee_id' => $employeeId,
                    'extra_hour_id' => $extraHourId,
                    'employee_schedule_reconcile_id' => $reconcile->id,
                    'reconciliation_date' => $reconciliationDate,
                    'minutes_reconciled' => $minutesReconciledForHour,
                    'status' => 'pending',
                    'notes' => $notes,
                    'business_unit_id' => $businessUnitId
                ]);
            }

            return $reconcile;
        });
    }

    /**
     * Approve a reconciliation
     *
     * @param int $reconcileId
     * @param int $approverUserId
     * @return EmployeeScheduleReconcile
     */
    public function approveReconciliation(int $reconcileId, int $approverUserId): EmployeeScheduleReconcile
    {
        return DB::transaction(function () use ($reconcileId, $approverUserId) {
            $reconcile = EmployeeScheduleReconcile::findOrFail($reconcileId);

            // Update the main reconcile record
            $reconcile->status = 'approved';
            $reconcile->approved_by_user_id = $approverUserId;
            $reconcile->approved_at = Carbon::now();
            $reconcile->save();

            // Update all the extra hour reconciliations
            foreach ($reconcile->extraHourReconciliations as $extraHourReconciliation) {
                $extraHourReconciliation->approve($approverUserId);
            }

            return $reconcile;
        });
    }

    /**
     * Reject a reconciliation
     *
     * @param int $reconcileId
     * @param int $approverUserId
     * @param string $notes
     * @return EmployeeScheduleReconcile
     */
    public function rejectReconciliation(int $reconcileId, int $approverUserId, string $notes = ''): EmployeeScheduleReconcile
    {
        return DB::transaction(function () use ($reconcileId, $approverUserId, $notes) {
            $reconcile = EmployeeScheduleReconcile::findOrFail($reconcileId);

            // Update the main reconcile record
            $reconcile->status = 'rejected';
            $reconcile->approved_by_user_id = $approverUserId;
            $reconcile->approved_at = Carbon::now();
            $reconcile->notes = $notes;
            $reconcile->save();

            // Update all the extra hour reconciliations and restore the extra hours
            foreach ($reconcile->extraHourReconciliations as $extraHourReconciliation) {
                // Update status
                $extraHourReconciliation->status = 'rejected';
                $extraHourReconciliation->approved_by_user_id = $approverUserId;
                $extraHourReconciliation->approved_at = Carbon::now();
                $extraHourReconciliation->notes = $notes;
                $extraHourReconciliation->save();

                // Restore the extra hour minutes
                $extraHour = $extraHourReconciliation->extraHour;
                $extraHour->remaining_minutes += $extraHourReconciliation->minutes_reconciled;

                // Update the status
                if ($extraHour->remaining_minutes >= $extraHour->total_minutes) {
                    $extraHour->status = 'available';
                } else {
                    $extraHour->status = 'partially_reconciled';
                }

                $extraHour->is_fully_reconciled = false;
                $extraHour->save();
            }

            return $reconcile;
        });
    }

    /**
     * Get reconciliation summary for an employee
     *
     * @param int $employeeId
     * @param Carbon|string|null $startDate
     * @param Carbon|string|null $endDate
     * @return array
     */
    public function getReconciliationSummary(
        int $employeeId,
        $startDate = null,
        $endDate = null
    ): array {
        // Set default date range to current month if not provided
        if ($startDate === null) {
            $startDate = Carbon::now()->startOfMonth();
        } elseif (is_string($startDate)) {
            $startDate = Carbon::parse($startDate)->startOfDay();
        }
    
        if ($endDate === null) {
            $endDate = Carbon::now()->endOfMonth();
        } elseif (is_string($endDate)) {
            $endDate = Carbon::parse($endDate)->endOfDay();
        }
    
        // Get total extra hours earned (total of all extra hours)
        $extraHoursEarned = ExtraHour::where('employee_id', $employeeId)
            ->sum('total_minutes');
    
        // Get total extra hours reconciled (used)
        $extraHoursReconciled = ExtraHourReconciliation::where('employee_id', $employeeId)
            ->where('status', '!=', 'rejected')
            ->sum('minutes_reconciled');
    
        // Get total extra hours expired
        $extraHoursExpired = ExtraHour::where('employee_id', $employeeId)
            ->where('status', 'expired')
            ->sum('total_minutes');
    
        // Get current available extra hours
        $availableExtraHours = ExtraHour::where('employee_id', $employeeId)
            ->where('expiry_date', '>=', Carbon::now())
            ->where('is_fully_reconciled', false)
            ->sum('remaining_minutes');
    
        return [
            'earned_minutes' => $extraHoursEarned,
            'reconciled_minutes' => $extraHoursReconciled,
            'expired_minutes' => $extraHoursExpired,
            'available_minutes' => $availableExtraHours,
            'earned_formatted' => $this->formatMinutes($extraHoursEarned),
            'reconciled_formatted' => $this->formatMinutes($extraHoursReconciled),
            'expired_formatted' => $this->formatMinutes($extraHoursExpired),
            'available_formatted' => $this->formatMinutes($availableExtraHours),
        ];
    }

    /**
     * Format minutes as hours:minutes
     */
    private function formatMinutes(int $minutes): string
    {
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        return sprintf('%02d:%02d', $hours, $remainingMinutes);
    }
}
