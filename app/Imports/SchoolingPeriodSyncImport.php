<?php

namespace App\Imports;

use App\Models\SchoolingPeriod;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SchoolingPeriodSyncImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return SchoolingPeriod::updateOrCreate(
            ['erp_id' => $row['erp_id']],
            [
                'short_description' => $row['short_description'],
                'started_at' => \Carbon\Carbon::parse($row['started_at']),
                'finished_at' => \Carbon\Carbon::parse($row['finished_at']),
                'sel_order' => $row['sel_order'],
                'erp_id' => $row['erp_id'],
            ]
        );
    }
}
