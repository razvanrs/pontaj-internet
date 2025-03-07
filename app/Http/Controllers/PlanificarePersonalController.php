<?php

namespace App\Http\Controllers;

use App\Data\EmployeeScheduleData;
use App\Models\ScheduleStatus;
use App\Models\BusinessUnitGroup;
use App\Models\Employee;
use App\Models\EmployeeSchedule;
use App\Models\Day;
use App\Models\Week;
use App\Models\Year;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class PlanificarePersonalController extends Controller
{

    public $successStatus = 200;

    public function index()
	{
        $employees = Employee::get();
        $scheduleStatuses = ScheduleStatus::orderBy('sel_order')->get();
        $businessUnitGroups = BusinessUnitGroup::orderBy('sel_order')->get(); // Get all business unit groups

        return Inertia::render('PlanificarePersonal')
        ->with([
            "employees" => $employees,
            "scheduleStatuses" => $scheduleStatuses,
            "businessUnitGroups" => $businessUnitGroups, // Pass business unit groups to the view
            "events" => []
        ]);
	}

    // Add a new method to get employees by business unit group
    public function getEmployeesByBusinessUnitGroup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'businessUnitGroupId' => ['required', 'integer'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                "result" => "RESULT_ERROR",
                "employees" => [],
                'error' => $validator->errors()
            ], 422);
        }

        $businessUnitGroupId = $request->businessUnitGroupId;
        
        // Get employees belonging to the selected business unit group
        $employees = Employee::with('militaryRank')->whereHas('businessUnitEmployees.businessUnit', function($query) use ($businessUnitGroupId) {
            $query->where('business_unit_group_id', $businessUnitGroupId);
        })->orderBy('full_name')->get();

        return response()->json([
            "result" => "RESULT_OK",
            "employees" => $employees,
            'error' => null
        ], $this->successStatus);
    }

    public function addEmployeeActivity(Request $request)
    {

        ray($request->input());

        $messages = [
            'required' => 'Câmpul :attribute este obligatoriu.',
        ];

        $attributes = [
            'scheduleStatus' => 'Situație prezență',
            'employee' => 'Persoană',
            'dateStart' => 'Data începerii activității',
            'dateEnd' => 'Data finalizării activității',
        ];

        $validator = Validator::make($request->all(), [
            'scheduleStatus' => ['required'],
            'employee' => ['required'],
            'dateStart' => ['required'],
            'dateEnd' => ['required'],
        ], $messages, $attributes);

        $validator->after(function ($validator) use ($request) {
            if ($request->formAction == 'add') {
                $start = (new Carbon($request->dateStart));
                $end = (new Carbon($request->dateEnd));
                
                ray()->showQueries();
                
                if ($request->employee) {
                    $employeeSchedule = new EmployeeSchedule();
                    
                    // Check for any overlapping events
                    $overlappingEvents = $employeeSchedule
                        ->where('employee_id', $request->employee["id"])
                        ->where(function ($query) use ($start, $end) {
                            $query->where(function ($q) use ($start, $end) {
                                // New event starts during an existing event
                                $q->where('date_start', '<=', $start)
                                   ->where('date_finish', '>', $start);
                            })
                            ->orWhere(function ($q) use ($start, $end) {
                                // New event ends during an existing event
                                $q->where('date_start', '<', $end)
                                   ->where('date_finish', '>=', $end);
                            })
                            ->orWhere(function ($q) use ($start, $end) {
                                // New event completely contains an existing event
                                $q->where('date_start', '>=', $start)
                                   ->where('date_finish', '<=', $end);
                            });
                        })
                        ->get();
        
                    ray($overlappingEvents);
        
                    if ($overlappingEvents->count() > 0) {
                        $validator->errors()->add(
                            'dateStart',
                            'Perioada selectată se suprapune peste un eveniment deja introdus pentru această persoană!'
                        );
                    }
                }
                
                ray()->stopShowingQueries();
            }
        });


        if ($validator->fails()) {
            return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
        }

        if($request->formAction == 'add'){

            $dates = new Carbon($request->dateStart);
            $datef = new Carbon($request->dateEnd);

            $year = new Year();
            $year = $year->whereCode($dates->year)->first();

            $week = new Week();
            $week = $week->whereCode($dates->week)->first();

            $day = new Day();
            $day = $day->whereCode($dates->day)->first();

            $employeeSchedule = new EmployeeSchedule();
            $employeeSchedule->schedule_status_id = $request->scheduleStatus["id"];
            $employeeSchedule->employee_id = $request->employee["id"];
            $employeeSchedule->year_id = $year->id;
            $employeeSchedule->week_id = $week->id;
            $employeeSchedule->day_id = $day->id;
            $employeeSchedule->date_start = $dates->format("Y-m-d H:i:s");
            $employeeSchedule->date_finish = $datef->format("Y-m-d H:i:s");
            $employeeSchedule->total_minutes =$datef->diffInMinutes($dates);
            $employeeSchedule->display_code = $request->displayCode;
            $employeeSchedule->save();
        }

        return to_route('employeeSchedule');
    }

    public function addEmployeeActivityBulk(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'scheduleStatus' => ['required'],
            'employee' => ['required'],
            'startDate' => ['required', 'date'],
            'endDate' => ['required', 'date', 'after_or_equal:startDate'],
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
    
        // Parse dates
        $startDate = Carbon::parse($request->startDate);
        $endDate = Carbon::parse($request->endDate);
        
        // First, check if ANY day in the range has an existing event
        $conflictingDates = [];
        $currentDate = $startDate->copy();
        
        while ($currentDate->lte($endDate)) {
            // Removed weekend check to include all days
            $dayStart = $currentDate->copy()->setHour(8)->setMinute(0);
            $dayEnd = $currentDate->copy()->setHour(16)->setMinute(0);
    
            // Check for overlapping events
            $overlappingEvents = EmployeeSchedule::where('employee_id', $request->employee['id'])
                ->where(function ($query) use ($dayStart, $dayEnd) {
                    $query->where(function ($q) use ($dayStart, $dayEnd) {
                        // Existing event starts during the new event time
                        $q->where('date_start', '>=', $dayStart)
                          ->where('date_start', '<', $dayEnd);
                    })
                    ->orWhere(function ($q) use ($dayStart, $dayEnd) {
                        // Existing event ends during the new event time
                        $q->where('date_finish', '>', $dayStart)
                          ->where('date_finish', '<=', $dayEnd);
                    })
                    ->orWhere(function ($q) use ($dayStart, $dayEnd) {
                        // Existing event completely contains the new event
                        $q->where('date_start', '<=', $dayStart)
                          ->where('date_finish', '>=', $dayEnd);
                    });
                })
                ->get();
    
            if ($overlappingEvents->count() > 0) {
                // Store date in dd/mm/yyyy format for the error message
                $conflictingDates[] = $currentDate->format('d/m/Y');
            }
            $currentDate->addDay();
        }
    
        // If we found conflicting dates, return an error with the list of dates
        if (count($conflictingDates) > 0) {
            return response()->json([
                'error' => true,
                'message' => 'Există evenimente deja planificate în datele selectate',
                'conflicting_dates' => $conflictingDates
            ], 422);
        }
    
        // If no conflicts, proceed with creating the schedules
        $createdSchedules = [];
        $currentDate = $startDate->copy();
    
        while ($currentDate->lte($endDate)) {
            // Removed weekend check to include all days
            $dayStart = $currentDate->copy()->setHour(8)->setMinute(0);
            $dayEnd = $currentDate->copy()->setHour(16)->setMinute(0);
    
            // Create schedule entry
            $employeeSchedule = new EmployeeSchedule();
            $employeeSchedule->schedule_status_id = $request->scheduleStatus['id'];
            $employeeSchedule->employee_id = $request->employee['id'];
            
            // Get the appropriate year, week, and day IDs
            $year = Year::where('code', $dayStart->year)->first();
            $week = Week::where('code', $dayStart->weekOfYear)->first();
            $day = Day::where('code', $dayStart->day)->first();
            
            if (!$year || !$week || !$day) {
                return response()->json([
                    'error' => true,
                    'message' => 'Nu s-au putut găsi referințele pentru an, săptămână sau zi'
                ], 422);
            }
            
            $employeeSchedule->year_id = $year->id;
            $employeeSchedule->week_id = $week->id;
            $employeeSchedule->day_id = $day->id;
            $employeeSchedule->date_start = $dayStart->format("Y-m-d H:i:s");
            $employeeSchedule->date_finish = $dayEnd->format("Y-m-d H:i:s");
            $employeeSchedule->total_minutes = $dayEnd->diffInMinutes($dayStart);
            $employeeSchedule->display_code = $request->displayCode;
            $employeeSchedule->save();
    
            $createdSchedules[] = $employeeSchedule;
            $currentDate->addDay();
        }
    
        return response()->json([
            'success' => true,
            'message' => 'Programare realizată cu succes',
            'created_schedules' => count($createdSchedules)
        ]);
    }

    public function getEvents(Request $request){

        ray($request->input());

        $messages = [
            'required' => 'Câmpul :attribute este obligatoriu.',
        ];

        $attributes = [
            'employeeId' => 'Persoană',
        ];

        $validator = Validator::make($request->all(), [
            'employeeId' => ['required'],
        ], $messages, $attributes);


        if ($validator->fails()) {
            return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
        }

        $employeeSchedule = new EmployeeSchedule();
        $startDayMonth = !$request->startString ? now()->startOfWeek() : $request->startString;
        $endDayMonth = !$request->endString ? now()->endOfWeek() : $request->endString;

        $events = $employeeSchedule
            ->with(['scheduleStatus']) // Add this line to eager load the relationship
            ->whereBetween('date_start', [$startDayMonth, $endDayMonth])
            ->where("employee_id", "=", $request->employeeId)
            ->get();

        $events = EmployeeScheduleData::collection($events);

        return response()->json([
            "result" => "RESULT_OK",
            "events" => $events,
            'error' => null
        ], $this->successStatus);

    }

    public function getEvent(Request $request){

        ray($request->input());

        $messages = [
            'required' => 'Câmpul :attribute este obligatoriu.',
        ];

        $attributes = [
            'rowId' => 'Record',
        ];

        $validator = Validator::make($request->all(), [
            'rowId' => ['required'],
        ], $messages, $attributes);


        if ($validator->fails()) {
            return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
        }

        $employeeSchedule = new EmployeeSchedule();
        $employeeSchedule = $employeeSchedule
            ->with([
                'scheduleStatus', 'employee'
            ])
            ->where("id", "=", $request->rowId)->first();

        if(!$employeeSchedule){
            return response()->json([
                "result" => "RESULT_ERRR",
                "event" => null,
                'error' => "Cannot found record !"
            ], $this->successStatus);
        }

        return response()->json([
            "result" => "RESULT_OK",
            "event" => $employeeSchedule,
            'error' => null
        ], $this->successStatus);

    }

    public function updateEmployeeActivity(Request $request)
    {

        ray($request->input());

        $messages = [
            'required' => 'Câmpul :attribute este obligatoriu.',
        ];

        $attributes = [
            'Id' => 'Identificator',
            'scheduleStatus' => 'Situație prezență',
            'employee' => 'Persoană',
            'dateStart' => 'Data începerii activității',
            'dateEnd' => 'Data finalizării activității',
        ];

        $validator = Validator::make($request->all(), [
            'recordId' => ['required'],
            'scheduleStatus' => ['required'],
            'employee' => ['required'],
            'dateStart' => ['required'],
            'dateEnd' => ['required'],
        ], $messages, $attributes);


        if ($validator->fails()) {
            return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
        }

        if($request->formAction == 'edit'){

            $dates = new Carbon($request->dateStart);
            $datef = new Carbon($request->dateEnd);

            $year = new Year();
            $year = $year->whereCode($dates->year)->first();

            $week = new Week();
            $week = $week->whereCode($dates->week)->first();

            $day = new Day();
            $day = $day->whereCode($dates->day)->first();

            $employeeSchedule = new EmployeeSchedule();
            $employeeSchedule = $employeeSchedule->where("id", "=", $request->recordId)->first();

            if(!$employeeSchedule){
                throw new Exception('Missing teacher schedule');
            }

            $employeeSchedule->schedule_status_id = $request->scheduleStatus["id"];
            $employeeSchedule->year_id = $year->id;
            $employeeSchedule->week_id = $week->id;
            $employeeSchedule->day_id = $day->id;
            $employeeSchedule->employee_id = $request->employee["id"];
            $employeeSchedule->date_start = $dates->format("Y-m-d H:i:s");
            $employeeSchedule->date_finish = $datef->format("Y-m-d H:i:s");
            $employeeSchedule->total_minutes =$datef->diffInMinutes($dates);
            $employeeSchedule->display_code = $request->displayCode;
            $employeeSchedule->save();
        }

        return to_route('employeeSchedule');
    }

    public function deleteEmployeeActivity(Request $request)
    {

        ray($request->input());

        $messages = [
            'required' => 'Câmpul :attribute este obligatoriu.',
        ];

        $attributes = [
            'Id' => 'Identificator',
        ];

        $validator = Validator::make($request->all(), [
            'recordId' => ['required'],
        ], $messages, $attributes);


        if ($validator->fails()) {
            return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
        }

        if($request->formAction == 'delete'){

            $employeeSchedule = new EmployeeSchedule();
            $employeeSchedule = $employeeSchedule->where("id", "=", $request->recordId)->first();

            if(!$employeeSchedule){
                throw new Exception('Missing teacher schedule');
            }

            $employeeSchedule->delete();
        }

        return to_route('employeeSchedule');
    }
}
