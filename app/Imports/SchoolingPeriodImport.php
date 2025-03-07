<?php

namespace App\Imports;

use App\Models\SchoolingPeriod;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SchoolingPeriodImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new SchoolingPeriod([
            'short_description' => $row['short_description'],
            'started_at' => \Carbon\Carbon::parse($row['started_at'])->format('Y-m-d'),
            'finished_at' => \Carbon\Carbon::parse($row['finished_at'])->format('Y-m-d'),
            'sel_order' => $row['sel_order'],
            'erp_id' => $row['erp_id'],

        ]);
    }
}
