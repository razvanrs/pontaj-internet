<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReconciliationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReconciliationController extends Controller
{
    protected $reconciliationService;

    public function __construct(ReconciliationService $reconciliationService)
    {
        $this->reconciliationService = $reconciliationService;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|integer|exists:employees,id',
            'extra_hour_id' => 'required|array',
            'extra_hour_id.*' => 'integer|exists:extra_hours,id',
            'minutes_reconciled' => 'required|array',
            'minutes_reconciled.*' => 'integer|min:1',
            'reconciliation_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        try {
            // Ensure notes is a string, even if null
            $notes = $request->input('notes');
            $notes = is_null($notes) ? '' : (string)$notes;
            
            // Log the input parameters for debugging
            \Log::info('Reconciliation input', $request->all());
            \Log::info('Notes value (sanitized):', ['notes' => $notes]);
    
            $reconciliation = $this->reconciliationService->createReconciliation(
                $request->input('employee_id'),
                $request->input('extra_hour_id'),
                $request->input('minutes_reconciled'),
                $notes, // Pass the sanitized notes value
                $request->input('reconciliation_date')
            );
    
            return response()->json([
                'success' => true,
                'reconciliation' => $reconciliation
            ]);
        } catch (\Exception $e) {
            \Log::error('Reconciliation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
