<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\ExtraHour;
use App\Models\ExtraHourReconciliation;
use App\Models\EmployeeScheduleReconcile;
use App\Services\ReconciliationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReconciliationController extends Controller
{
    protected $reconciliationService;

    public function __construct(ReconciliationService $reconciliationService)
    {
        $this->reconciliationService = $reconciliationService;
    }

    /**
     * Display a listing of reconciliations for an employee
     */
    public function index(Request $request)
    {
        $employeeId = $request->input('employee_id');

        // Validate access permissions

        $reconciliations = EmployeeScheduleReconcile::where('employee_id', $employeeId)
            ->with(['approvedBy', 'extraHourReconciliations'])
            ->orderBy('reconcile_date', 'desc')
            ->paginate(20);

        return view('reconciliations.index', [
            'reconciliations' => $reconciliations,
            'employee' => Employee::findOrFail($employeeId)
        ]);
    }

    /**
     * Show form to create a new reconciliation
     */
    public function create(Request $request)
    {
        $employeeId = $request->input('employee_id');
        $employee = Employee::findOrFail($employeeId);

        // Get available extra hours
        $availableExtraHours = ExtraHour::getAvailableForEmployee($employeeId);

        return view('reconciliations.create', [
            'employee' => $employee,
            'availableExtraHours' => $availableExtraHours,
            'summary' => $this->reconciliationService->getReconciliationSummary($employeeId)
        ]);
    }

    /**
     * Store a newly created reconciliation
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'extra_hour_id' => 'required|array',
            'extra_hour_id.*' => 'exists:extra_hours,id',
            'minutes_reconciled' => 'required|array',
            'minutes_reconciled.*' => 'integer|min:1',
            'reconciliation_date' => 'required|date',
            'notes' => 'nullable|string',
            'business_unit_id' => 'nullable|exists:business_units,id',
        ]);

        try {
            // Create the reconciliation
            $reconciliation = $this->reconciliationService->createReconciliation(
                $validated['employee_id'],
                $validated['extra_hour_id'],
                $validated['minutes_reconciled'],
                $validated['notes'] ?? '',
                $validated['reconciliation_date'],
                $validated['business_unit_id'] ?? null
            );

            return redirect()->route('reconciliations.show', $reconciliation->id)
                ->with('success', 'Reconciliation created successfully and pending approval');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating reconciliation: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified reconciliation
     */
    public function show($id)
    {
        $reconciliation = EmployeeScheduleReconcile::with([
            'employee',
            'approvedBy',
            'extraHourReconciliations',
            'extraHourReconciliations.extraHour'
        ])->findOrFail($id);

        // Validate access permissions

        return view('reconciliations.show', [
            'reconciliation' => $reconciliation
        ]);
    }

    /**
     * Show approval form for a reconciliation
     */
    public function showApproval($id)
    {
        $reconciliation = EmployeeScheduleReconcile::with([
            'employee',
            'extraHourReconciliations',
            'extraHourReconciliations.extraHour'
        ])->findOrFail($id);

        // Validate that current user has approval permissions

        return view('reconciliations.approve', [
            'reconciliation' => $reconciliation
        ]);
    }

    /**
     * Approve a reconciliation
     */
    public function approve(Request $request, $id)
    {
        // Validate that current user has approval permissions
        $userId = Auth::id();

        try {
            $reconciliation = $this->reconciliationService->approveReconciliation($id, $userId);

            return redirect()->route('reconciliations.show', $reconciliation->id)
                ->with('success', 'Reconciliation approved successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error approving reconciliation: ' . $e->getMessage());
        }
    }

    /**
     * Reject a reconciliation
     */
    public function reject(Request $request, $id)
    {
        // Validate request
        $validated = $request->validate([
            'notes' => 'required|string'
        ]);

        // Validate that current user has approval permissions
        $userId = Auth::id();

        try {
            $reconciliation = $this->reconciliationService->rejectReconciliation($id, $userId, $validated['notes']);

            return redirect()->route('reconciliations.show', $reconciliation->id)
                ->with('success', 'Reconciliation rejected successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error rejecting reconciliation: ' . $e->getMessage());
        }
    }

    /**
     * Display summary of extra hours for an employee
     */
    public function summary(Request $request)
    {
        $employeeId = $request->input('employee_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $summary = $this->reconciliationService->getReconciliationSummary(
            $employeeId,
            $startDate,
            $endDate
        );

        return view('reconciliations.summary', [
            'employee' => Employee::findOrFail($employeeId),
            'summary' => $summary,
            'startDate' => $startDate ? Carbon::parse($startDate) : Carbon::now()->startOfMonth(),
            'endDate' => $endDate ? Carbon::parse($endDate) : Carbon::now()->endOfMonth()
        ]);
    }
}
