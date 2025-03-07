<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolingPeriod extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'short_description',
        'started_at',
        'finished_at',
        'sel_order',
        'erp_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'started_at' => 'date',
        'finished_at' => 'date',
        'sel_order' => 'integer',
        'erp_id' => 'integer',
    ];

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }
}
