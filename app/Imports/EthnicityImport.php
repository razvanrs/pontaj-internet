<?php

namespace App\Imports;

use App\Models\Ethnicity;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EthnicityImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Ethnicity([
            'id' => $row['id'],
            'short_description' => $row['short_description'],
            'sel_order' => $row['sel_order'],
        ]);
    }
}
