<?php

namespace App\Http\Controllers;

use App\Models\TemporaryObservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TemporaryObservationController extends Controller
{
    /**
     * Store or update temporary observations
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_unit_group_id' => 'required',
            'year' => 'required|integer',
            'month' => 'required|integer',
            'person_index' => 'required|integer',
            'observations' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => 'Invalid data provided',
                'errors' => $validator->errors()
            ], 422);
        }

        // Create storage key (same format used in localStorage)
        $storageKey = "monthly-report-observations-{$request->business_unit_group_id}-{$request->year}-{$request->month}";
        $personKey = "observations_{$request->person_index}";

        // Find or create record
        $observation = TemporaryObservation::updateOrCreate(
            [
                'storage_key' => $storageKey,
                'person_index' => $request->person_index,
            ],
            [
                'business_unit_group_id' => $request->business_unit_group_id,
                'year' => $request->year,
                'month' => $request->month,
                'observations' => $request->observations,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Observations saved successfully',
            'data' => $observation
        ]);
    }

    /**
     * Retrieve temporary observations for a specific month and unit
     */
    public function getByMonth(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_unit_group_id' => 'required',
            'year' => 'required|integer',
            'month' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => 'Invalid data provided',
                'errors' => $validator->errors()
            ], 422);
        }

        // Create storage key (same format used in localStorage)
        $storageKey = "monthly-report-observations-{$request->business_unit_group_id}-{$request->year}-{$request->month}";

        // Get all records for this key
        $observations = TemporaryObservation::where('storage_key', $storageKey)->get();

        // Format the data to match the localStorage structure
        $formattedData = [];
        foreach ($observations as $observation) {
            $formattedData["observations_{$observation->person_index}"] = $observation->observations;
        }

        return response()->json([
            'success' => true,
            'data' => $formattedData
        ]);
    }

    /**
     * Delete all temporary observations
     * Useful for when you implement the permanent solution
     */
    public function deleteAll()
    {
        TemporaryObservation::truncate();
        
        return response()->json([
            'success' => true,
            'message' => 'All temporary observations have been deleted'
        ]);
    }
}