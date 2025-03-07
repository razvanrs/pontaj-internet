<?php

namespace App\Imports;

use App\Models\Module;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ModulesImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

        // dd($row);

        return new Module([
            'code' => $row['code'],
            'name' => $row['name'],
            'sel_order' => $row['sel_order'],
        ]);
    }
}
