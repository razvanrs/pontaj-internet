<?php

namespace App\Imports;

use App\Models\Ability;
use App\Models\Module;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AbilitiesImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {

        $module = new Module();
        $module = $module->whereCode($row["module_code"])->first();

        if($module){
            return new Ability([
                'module_id' => $module->id,
                'code' => $row['code'],
                'name' => $row['name'],
                'sel_order' => 1,
            ]);
        }
    }
}
