<?php

namespace App\Imports;

use App\Models\StudentClass;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudentClassSyncImport implements ToModel, WithHeadingRow
{
    protected $created = 0;
    protected $updated = 0;
    protected $newClasses = [];

    public function model(array $row)
    {
        try {
            // Debug info to identify the issue
            Log::info('StudentClassSyncImport processing row', [
                'row_data' => $row
            ]);

            // Check if erp_id exists in the row data
            if (!isset($row['erp_id']) || empty($row['erp_id'])) {
                Log::warning('Missing erp_id in student class import row', [
                    'row' => $row
                ]);
                return null; // Skip rows without erp_id
            }

            DB::beginTransaction();

            // Check if the class already exists
            $existingClass = StudentClass::where('erp_id', $row['erp_id'])->first();

            if ($existingClass) {
                // Update existing class
                $existingClass->update([
                    'short_description' => $row['name'] ?? '',
                    'erp_schooling_period_id' => $row['erp_schooling_period_id'] ?? null,
                    'erp_form_teacher_id' => $row['erp_form_teacher_id'] ?? null,
                    'sel_order' => $row['sel_order'] ?? 0,
                ]);

                $this->updated++;
                DB::commit();
                return $existingClass;
            }

            // Create new class with explicit erp_id assignment
            $studentClass = new StudentClass();
            $studentClass->erp_id = $row['erp_id'];
            $studentClass->short_description = $row['name'] ?? '';
            $studentClass->erp_schooling_period_id = $row['erp_schooling_period_id'] ?? null;
            $studentClass->erp_form_teacher_id = $row['erp_form_teacher_id'] ?? null;
            $studentClass->sel_order = $row['sel_order'] ?? 0;
            $studentClass->save();

            // Track new class
            $this->created++;
            $this->newClasses[] = [
                'id' => $studentClass->id,
                'erp_id' => $studentClass->erp_id,
                'name' => $studentClass->short_description,
            ];

            DB::commit();
            return $studentClass;

        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Student class sync import error: ' . $th->getMessage(), [
                'erp_id' => $row['erp_id'] ?? 'unknown',
                'short_description' => $row['name'] ?? 'unknown',
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
                'row_data' => $row
            ]);
            throw $th;
        }
    }

    public function getImportStats()
    {
        return [
            'created' => $this->created,
            'updated' => $this->updated,
            'newClasses' => $this->newClasses
        ];
    }
}
