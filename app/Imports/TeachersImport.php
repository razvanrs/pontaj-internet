<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\Teacher;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TeachersImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

        $employee = new Employee();
        $employee = $employee->whereSocialNumber($row["cnp"])->first();

        if($employee){
            return new Teacher([
                'employee_id' => $employee->id,
                'code' => $row['code'],
            ]);
        }
    }
}
