<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\StudentClass;
use App\Models\Teacher;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentClassImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $employee = Employee::where('erp_id', $row['erp_form_teacher_id'])->first();

        if ($employee && $employee->teacher) {
            $formTeacherId = $employee->teacher->id;
        } else {
            $formTeacherId = null;
        }


        return new StudentClass([
            'erp_id' => $row['erp_id'],
            'short_description' => $row['name'],
            'teacher_id' => $formTeacherId,
            'erp_schooling_period_id' => $row['erp_schooling_period_id'],
            'erp_form_teacher_id' => $row['erp_form_teacher_id'],
        ]);
    }
}
