<?php

namespace App\Imports;

use App\Models\BusinessUnit;
use App\Models\Employee;
use App\Models\BusinessUnitEmployee;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Log;

class BusinessUnitEmployeeImport implements ToCollection, WithHeadingRow
{
    private $businessUnitCache = [];

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        Log::info('Starting import with ' . $rows->count() . ' rows');

        // First pass: Extract and create unique business units
        $uniqueBusinessUnits = $rows->unique(function ($row) {
            return $row['idstructura'];
        })->filter(function ($row) {
            return !empty($row['idstructura']) && !empty($row['structura']);
        });

        Log::info('Found ' . $uniqueBusinessUnits->count() . ' unique business units');

        foreach ($uniqueBusinessUnits as $key => $row) {
            try {
                $businessUnit = BusinessUnit::updateOrCreate(
                    ['code' => $row['idstructura']],
                    [
                        'name' => $row['structura'],
                        'code' => $row['idstructura'],
                        'sel_order' => $key+1
                    ]
                );
                $this->businessUnitCache[$row['idstructura']] = $businessUnit->id;
                Log::info("Created/Updated business unit: {$row['structura']} with ID: {$businessUnit->id}");
            } catch (\Exception $e) {
                Log::error("Error creating business unit for structure: {$row['structura']}", [
                    'error' => $e->getMessage(),
                    'row' => $row
                ]);
            }
        }

        Log::info('Starting employee relations creation');
        $relationshipsCreated = 0;

        // Second pass: Create relationships
        foreach ($rows as $row) {
            try {
                if (empty($row['idstructura']) || empty($row['cnp'])) {
                    Log::warning('Skipping row due to missing data', [
                        'idstructura' => $row['idstructura'] ?? 'missing',
                        'social_number' => $row['cnp'] ?? 'missing'
                    ]);
                    continue;
                }

                $employee = Employee::where('social_number', $row['cnp'])->first();

                if (!$employee) {
                    Log::warning("Employee not found for social number: {$row['cnp']}");
                    continue;
                }

                if (!isset($this->businessUnitCache[$row['idstructura']])) {
                    Log::warning("Business unit not found in cache for idstructura: {$row['idstructura']}");
                    continue;
                }

                $relation = BusinessUnitEmployee::updateOrCreate(
                    [
                        'business_unit_id' => $this->businessUnitCache[$row['idstructura']],
                        'employee_id' => $employee->id,
                    ]
                );

                if ($relation) {
                    $relationshipsCreated++;
                    Log::info("Created relation for employee {$employee->id} with business unit {$row['idstructura']}");
                }

            } catch (\Throwable $th) {
                Log::error("Error creating relation", [
                    'error' => $th->getMessage(),
                    'row' => $row
                ]);
                ray($row);
                throw $th;
            }
        }

        Log::info("Import completed. Created $relationshipsCreated relationships");

        // Debug cache contents
        Log::info('Business Unit Cache contents:', $this->businessUnitCache);
    }
}
