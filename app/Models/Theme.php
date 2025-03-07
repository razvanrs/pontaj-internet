<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Theme extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['module_id', 'ability_id', 'code', 'name', 'sel_order'];

    /**
     * Get the module
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class, 'module_id', 'id');
    }

    /**
     * Get the ability
     */
    public function ability(): BelongsTo
    {
        return $this->belongsTo(Ability::class, 'ability_id', 'id');
    }

}
