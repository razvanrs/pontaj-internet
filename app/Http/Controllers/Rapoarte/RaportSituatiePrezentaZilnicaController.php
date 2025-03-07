<?php

namespace App\Http\Controllers\Rapoarte;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class RaportSituatiePrezentaZilnicaController extends Controller
{
    public function index()
    {
        // Get business unit groups instead of business units
        $businessUnitGroups = \App\Models\BusinessUnitGroup::orderBy('sel_order')
            ->get()
            ->map(function($group) {
                return [
                    'id' => $group->id,
                    'name' => $group->name
                ];
            });

        return Inertia::render('Rapoarte/RaportSituatiePrezentaZilnica')
            ->with([
                'businessUnitGroups' => $businessUnitGroups,
                'stats' => [
                    'total' => 0,
                    'present' => 0,
                    'absent' => 0
                ],
                'presentEmployees' => [],
                'absentEmployees' => [],
                'selectedDate' => now()->format('Y-m-d')
            ]);
    }

    public function getDailyData(Request $request)
    {
        try {
            $date = Carbon::parse($request->input('date'))->startOfDay();
            $businessUnitGroupId = $request->input('business_unit_group_id');
    
            // Get all business units in the selected group
            $businessUnitIds = \App\Models\BusinessUnit::where('business_unit_group_id', $businessUnitGroupId)
                ->pluck('id');
    
            // First, get the total number of employees in the business unit group
            $totalEmployees = Employee::whereHas('businessUnitEmployees', function($q) use ($businessUnitIds) {
                $q->whereIn('business_unit_id', $businessUnitIds);
            })->count();
    
            // Base query for schedules
            $query = EmployeeSchedule::with(['employee.businessUnitEmployees', 'scheduleStatus', 'employee.militaryRank'])
                ->whereDate('date_start', '<=', $date)
                ->whereDate('date_finish', '>=', $date)
                ->whereHas('employee', function($q) use ($businessUnitIds) {
                    $q->whereHas('businessUnitEmployees', function($q) use ($businessUnitIds) {
                        $q->whereIn('business_unit_id', $businessUnitIds);
                    });
                });
    
            $schedules = $query->get();
    
            // Group schedules by employee ID
            $employeeSchedules = $schedules->groupBy('employee_id');
            
            // Initialize arrays
            $present = [];
            $absent = [];
    
            foreach ($employeeSchedules as $employeeId => $employeeRecords) {
                $employee = $employeeRecords->first()->employee;
                if (!$employee) continue;
                
                // Check for R or R* status records
                $rStatusRecords = $employeeRecords->filter(function($schedule) {
                    return $schedule->scheduleStatus && 
                           ($schedule->scheduleStatus->code === 'R' || 
                            $schedule->scheduleStatus->code === 'R*');
                });
                
                // Check for present records
                $presentRecords = $employeeRecords->filter(function($schedule) {
                    return $schedule->schedule_status_id === 1;
                });
                
                // Check for other absence records (not R or R*)
                $otherAbsenceRecords = $employeeRecords->filter(function($schedule) {
                    return $schedule->schedule_status_id !== 1 && 
                           $schedule->scheduleStatus && 
                           $schedule->scheduleStatus->code !== 'R' && 
                           $schedule->scheduleStatus->code !== 'R*';
                });
                
                if ($rStatusRecords->isNotEmpty()) {
                    // Employee has R or R* status
                    
                    // Special case: Show time interval only if both present and R/R* on the same day
                    $timeIntervalString = '';
                    if ($presentRecords->isNotEmpty()) {
                        // Format time interval for R status records only when also present that day
                        $timeIntervals = [];
                        foreach ($rStatusRecords as $schedule) {
                            $timeIntervals[] = Carbon::parse($schedule->date_start)->format('H:i') . '-' . 
                                              Carbon::parse($schedule->date_finish)->format('H:i');
                        }
                        
                        $timeIntervalString = ' (' . implode(', ', $timeIntervals) . ')';
                    }
                    
                    $employeeData = [
                        'name' => $employee->full_name,
                        'military_rank' => $employee->militaryRank ?? '',
                        'military_rank_id' => $employee->military_rank_id ?? PHP_INT_MAX,
                        'status' => ($rStatusRecords->first()->scheduleStatus->code ?? '') . $timeIntervalString,
                    ];
                    
                    $absent[] = $employeeData;
                } else {
                    // Regular handling for other cases
                    
                    // Add to present list if has present records
                    if ($presentRecords->isNotEmpty()) {
                        $employeeData = [
                            'name' => $employee->full_name,
                            'military_rank' => $employee->militaryRank ?? '',
                            'military_rank_id' => $employee->military_rank_id ?? PHP_INT_MAX,
                            'status' => 'PREZ',
                        ];
                        
                        $present[] = $employeeData;
                    }
                    
                    // Add to absent list if has other absence records
                    if ($otherAbsenceRecords->isNotEmpty()) {
                        $employeeData = [
                            'name' => $employee->full_name,
                            'military_rank' => $employee->militaryRank ?? '',
                            'military_rank_id' => $employee->military_rank_id ?? PHP_INT_MAX,
                            'status' => $otherAbsenceRecords->first()->scheduleStatus->code ?? '',
                        ];
                        
                        $absent[] = $employeeData;
                    }
                }
            }
    
            // Sort each array by military_rank_id and then by name
            usort($present, function($a, $b) {
                if ($a['military_rank_id'] === $b['military_rank_id']) {
                    return strcmp($a['name'], $b['name']);
                }
                return $a['military_rank_id'] <=> $b['military_rank_id'];
            });
            
            usort($absent, function($a, $b) {
                if ($a['military_rank_id'] === $b['military_rank_id']) {
                    return strcmp($a['name'], $b['name']);
                }
                return $a['military_rank_id'] <=> $b['military_rank_id'];
            });
    
            // Calculate statistics
            $stats = [
                'total' => $totalEmployees,
                'present' => count($present),
                'absent' => count($absent)
            ];
    
            return response()->json([
                'stats' => $stats,
                'present' => $present,
                'absent' => $absent
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while fetching data',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
