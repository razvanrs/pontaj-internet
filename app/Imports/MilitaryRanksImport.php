<?php

namespace App\Imports;

use App\Models\MilitaryRank;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MilitaryRanksImport implements ToModel, WithHeadingRow
{
	/**
	 * @param array $row
	 *
	 * @return \Illuminate\Database\Eloquent\Model|null
	 */
	public function model(array $row)
	{
		return new MilitaryRank([
			'name' => $row['name'],
            'abbreviation' => $row['abbreviation'],
			'military_rank_type_id' => $row['military_rank_type_id'],
		]);
	}
}
