<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SanctionsImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new \App\Models\Sanction([
            'short_description' => $row['short_description'],
            'long_description' => $row['long_description'],
            'sel_order' => $row['sel_order'],
        ]);
    }
}
