<?php

namespace App\Imports;

use App\Models\Language;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LanguageImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Language([
            'id' => $row['id'],
            'short_description' => $row['short_description'],
            'long_description' => $row['long_description'],
            'sel_order' => $row['sel_order'],
        ]);
    }
}
