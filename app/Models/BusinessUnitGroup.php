<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusinessUnitGroup extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'name',
        'sel_order',
        'description'
    ];

    /**
     * Get the business units that belong to this group.
     */
    public function businessUnits(): HasMany
    {
        return $this->hasMany(BusinessUnit::class, 'business_unit_group_id');
    }
}