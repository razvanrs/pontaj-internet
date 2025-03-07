<?php

namespace App\Models;

use App\Data\DayLimitData;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\LaravelData\WithData;

class DayLimit extends Model
{
    use HasFactory;
    use SoftDeletes;
    use WithData;

    protected $dataClass = DayLimitData::class;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'start' => 'datetime:Y-m-d',
        'finish' => 'datetime:Y-m-d',
    ];

    protected $fillable = [
        'name',
        'start',
        'finish',
    ];


}
