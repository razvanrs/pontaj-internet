<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Year extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * Get the week
     */
    public function week(): HasMany
    {
        return $this->hasMany(Week::class);
    }

    /**
     * Get the module
     */
    public function day(): HasMany
    {
        return $this->hasMany(Day::class);
    }
}
