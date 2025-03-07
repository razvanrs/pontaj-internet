<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Week extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * Get the year
     */
    public function year(): BelongsTo
    {
        return $this->belongsTo(Year::class, 'year_id', 'id');
    }

    /**
     * Get the month
     */
    public function month(): BelongsTo
    {
        return $this->belongsTo(Month::class, 'month_id', 'id');
    }

    /**
     * Get the day
     */
    public function day(): HasMany
    {
        return $this->hasMany(Day::class);
    }
}
