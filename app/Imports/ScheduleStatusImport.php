<?php

namespace App\Imports;

use App\Models\ScheduleStatus;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ScheduleStatusImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // dd($row);

        return new ScheduleStatus([
            'code' => $row['code'],
            'name' => $row['name'],
            'color' => $row['color'],
            'background' => $row['background'],
            'sel_order' => $row['sel_order'],
        ]);
    }
}
