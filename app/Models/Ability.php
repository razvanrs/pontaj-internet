<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ability extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['code', 'name', 'sel_order', 'module_id'];

    /**
     * Get the comments for the blog post.
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function theme(): HasMany
    {
        return $this->hasMany(theme::class);
    }
}
