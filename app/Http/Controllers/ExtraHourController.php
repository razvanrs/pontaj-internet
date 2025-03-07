<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\ExtraHour;
use App\Models\EmployeeSchedule;
use App\Models\BusinessUnit;
use App\Services\ExtraHourService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExtraHourController extends Controller
{
    protected $extraHourService;

    public function __construct(ExtraHourService $extraHourService)
    {
        $this->extraHourService = $extraHourService;
    }

    /**
     * Display a listing of extra hours for an employee
     */
    public function index(Request $request)
    {
        $employeeId = $request->input('employee_id');

        // Validate employee access if needed
        // e.g., check if current user has permission to see this employee's extra hours

        $extraHours = ExtraHour::where('employee_id', $employeeId)
            ->with(['employeeSchedule'])
            ->orderBy('date', 'desc')
            ->paginate(20);

        return view('extra-hours.index', [
            'extraHours' => $extraHours,
            'employee' => Employee::findOrFail($employeeId)
        ]);
    }

    /**
     * Display detailed information about a specific extra hour entry
     */
    public function show($id)
    {
        $extraHour = ExtraHour::with([
            'employee',
            'employeeSchedule',
            'reconciliations'
        ])->findOrFail($id);

        // Validate access permissions

        return view('extra-hours.show', [
            'extraHour' => $extraHour
        ]);
    }

    /**
     * Display the available extra hours for an employee that can be reconciled
     */
    public function available(Request $request)
    {
        $employeeId = $request->input('employee_id');

        // Get available extra hours that haven't expired
        $availableExtraHours = $this->extraHourService->getAvailableExtraHours($employeeId);

        return view('extra-hours.available', [
            'extraHours' => $availableExtraHours,
            'employee' => Employee::findOrFail($employeeId)
        ]);
    }

    /**
     * Manually create extra hours (for admin use)
     */
    public function create(Request $request)
    {
        // Only accessible by admins
        $employees = Employee::orderBy('full_name')->get();

        return view('extra-hours.create', [
            'employees' => $employees
        ]);
    }

    /**
     * Store a manually created extra hour entry
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'business_unit_id' => 'nullable|exists:business_units,id',
            'notes' => 'nullable|string'
        ]);

        // Create the Carbon datetime objects
        $date = Carbon::parse($validated['date']);
        $startTime = Carbon::parse($date->format('Y-m-d') . ' ' . $validated['start_time']);
        $endTime = Carbon::parse($date->format('Y-m-d') . ' ' . $validated['end_time']);

        // If end time is earlier than start time, assume it's the next day
        if ($endTime->lt($startTime)) {
            $endTime->addDay();
        }

        // Create the extra hour
        $extraHour = ExtraHour::create([
            'employee_id' => $validated['employee_id'],
            'date' => $date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'business_unit_id' => $validated['business_unit_id'] ?? null,
        ]);

        return redirect()->route('extra-hours.show', $extraHour->id)
            ->with('success', 'Extra hour entry created successfully');
    }

    /**
     * Calculate and create extra hours from an employee schedule
     */
    public function calculateFromSchedule($scheduleId)
    {
        $schedule = EmployeeSchedule::findOrFail($scheduleId);

        // Calculate extra hours from the schedule
        $extraHours = $this->extraHourService->calculateExtraHours($schedule);

        if ($extraHours && $extraHours->isNotEmpty()) {
            return redirect()->back()
                ->with('success', count($extraHours) . ' extra hour entries created successfully');
        } else {
            return redirect()->back()
                ->with('info', 'No extra hours found for this schedule');
        }
    }
}
