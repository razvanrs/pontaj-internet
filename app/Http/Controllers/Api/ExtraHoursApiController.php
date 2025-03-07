<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmployeeSchedule;
use App\Models\ExtraHour;
use App\Services\ExtraHourService;
use App\Services\ReconciliationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ExtraHoursApiController extends Controller
{
    protected $extraHourService;
    protected $reconciliationService;

    public function __construct(ExtraHourService $extraHourService, ReconciliationService $reconciliationService)
    {
        $this->extraHourService = $extraHourService;
        $this->reconciliationService = $reconciliationService;
    }

    /**
     * Get available extra hours for an employee
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvailableExtraHours(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|integer|exists:employees,id',
            'group_by_schedule' => 'nullable|in:0,1,true,false',
            'include_reconciled' => 'nullable|in:0,1,true,false'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
    
        $employeeId = $request->input('employee_id');
    
        // Convert various formats of boolean to actual boolean
        $groupBySchedule = filter_var($request->input('group_by_schedule', false), FILTER_VALIDATE_BOOLEAN);
        $includeReconciled = filter_var($request->input('include_reconciled', false), FILTER_VALIDATE_BOOLEAN);
    
        // Get available extra hours
        $extraHours = $this->extraHourService->getAvailableExtraHours($employeeId);
    
        // Get reconciled hours if requested
        $reconciledHours = [];
        if ($includeReconciled) {
            $reconciledHours = $this->extraHourService->getReconciledExtraHours($employeeId);
        }
    
        // Get reconciliation summary
        $summary = $this->reconciliationService->getReconciliationSummary($employeeId);
    
        $response = [
            'success' => true,
            'extraHours' => $extraHours,
            'summary' => $summary
        ];
    
        // Add reconciled hours if requested
        if ($includeReconciled) {
            $response['reconciledHours'] = $reconciledHours;
        }
    
        return response()->json($response);
    }

    /**
     * Calculate extra hours for a specific employee schedule
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function calculateExtraHours(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'schedule_id' => 'required|integer|exists:employee_schedules,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $scheduleId = $request->input('schedule_id');
        $schedule = EmployeeSchedule::findOrFail($scheduleId);

        // Ensure user has permission to access this schedule
        // Add your permission check here...

        // Calculate extra hours
        $extraHours = $this->extraHourService->calculateExtraHours($schedule);

        if (!$extraHours) {
            return response()->json([
                'success' => false,
                'message' => 'No extra hours calculated. Schedule might not be completed or eligible.'
            ]);
        }

        return response()->json([
            'success' => true,
            'extraHours' => $extraHours,
            'count' => $extraHours->count(),
            'message' => 'Extra hours calculated successfully'
        ]);
    }

    /**
     * Recalculate extra hours for all completed schedules for a specific employee
     * within a date range
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function recalculateExtraHours(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|integer|exists:employees,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $employeeId = $request->input('employee_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Ensure user has permission
        // Add your permission check here...

        // Find all completed schedules within the date range
        $schedules = EmployeeSchedule::where('employee_id', $employeeId)
            ->where('schedule_status_id', 1) // Completed status
            ->whereBetween('date_start', [$startDate, $endDate])
            ->orderBy('date_start')
            ->get();

        $calculatedCount = 0;
        $results = [];

        foreach ($schedules as $schedule) {
            // Delete any existing extra hours for this schedule
            ExtraHour::where('employee_schedule_id', $schedule->id)->delete();

            // Recalculate extra hours
            $extraHours = $this->extraHourService->calculateExtraHours($schedule);

            if ($extraHours && $extraHours->count() > 0) {
                $calculatedCount++;
                $results[] = [
                    'schedule_id' => $schedule->id,
                    'date' => $schedule->date_start,
                    'extra_hours_count' => $extraHours->count()
                ];
            }
        }

        return response()->json([
            'success' => true,
            'schedules_processed' => $schedules->count(),
            'schedules_with_extra_hours' => $calculatedCount,
            'results' => $results,
            'message' => 'Extra hours recalculated successfully'
        ]);
    }

    /**
     * Get extra hours grouped by employee schedule
     *
     * @param int $employeeId
     * @return array
     */
    private function getExtraHoursGroupedBySchedule(int $employeeId): array
    {
        // Get all available extra hours
        $extraHours = ExtraHour::where('employee_id', $employeeId)
            ->where('expiry_date', '>=', now()->startOfDay())
            ->where('is_fully_reconciled', false)
            ->where('remaining_minutes', '>', 0)
            ->with('employeeSchedule')
            ->orderBy('date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        // Group by employee_schedule_id
        $grouped = [];
        foreach ($extraHours as $extraHour) {
            $scheduleId = $extraHour->employee_schedule_id;

            if (!isset($grouped[$scheduleId])) {
                // Get the schedule info
                $schedule = $extraHour->employeeSchedule;

                if (!$schedule) {
                    continue; // Skip if schedule no longer exists
                }

                $grouped[$scheduleId] = [
                    'schedule_id' => $scheduleId,
                    'schedule_date_start' => $schedule->date_start,
                    'schedule_date_finish' => $schedule->date_finish,
                    'extra_hours' => [],
                    'total_minutes' => 0,
                    'remaining_minutes' => 0
                ];
            }

            $grouped[$scheduleId]['extra_hours'][] = $extraHour;
            $grouped[$scheduleId]['total_minutes'] += $extraHour->total_minutes;
            $grouped[$scheduleId]['remaining_minutes'] += $extraHour->remaining_minutes;
        }

        // Convert to indexed array and sort by date
        $result = array_values($grouped);
        usort($result, function ($a, $b) {
            return strtotime($b['schedule_date_start']) - strtotime($a['schedule_date_start']);
        });

        return $result;
    }
}
