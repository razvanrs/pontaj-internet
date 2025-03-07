<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Module extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['code', 'name', 'sel_order'];

    /**
     * Get the comments for the blog post.
     */
    public function ability(): HasMany
    {
        return $this->hasMany(Ability::class);
    }

    public function theme(): HasMany
    {
        return $this->hasMany(Theme::class);
    }
}
