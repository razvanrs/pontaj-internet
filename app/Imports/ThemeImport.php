<?php

namespace App\Imports;

use App\Models\Ability;
use App\Models\Module;
use App\Models\Theme;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ThemeImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

        $module = new Module();
        $module = $module->whereCode($row["module"])->first();

        $ability = new Ability();
        $ability = $ability->whereCode($row["ability"])->first();

        if($module && $ability){
            return new Theme([
                'module_id' => $module->id,
                'ability_id' => $ability->id,
                'code' => $row["code"],
                'name' => $row["name"],
                'sel_order' => 1,
            ]);
        }
    }
}
