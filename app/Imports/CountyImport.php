<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CountyImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new \App\Models\County([
            'erp_id' => $row['erp_id'],
            'short_description' => $row['short_description'],
            'long_description' => $row['long_description'],
            'abbreviation' => $row['abbreviation'],
            'sel_order' => $row['sel_order'],
        ]);
    }
}
