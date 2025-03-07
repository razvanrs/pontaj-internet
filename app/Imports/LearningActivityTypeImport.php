<?php

namespace App\Imports;

use App\Models\LearningActivityType;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LearningActivityTypeImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new LearningActivityType([
            'code' => $row["code"],
            'name' => $row["name"],
            'color' => $row["color"],
            'background' => $row["background"],
            'sel_order' => 1,
        ]);
    }
}
