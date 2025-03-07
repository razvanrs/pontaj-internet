<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Month extends Model
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
     * Get the weeks
     */
    public function week(): HasMany
    {
        return $this->hasMany(Week::class);
    }
}
