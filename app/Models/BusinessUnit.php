<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessUnit extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'sel_order',
        'code'
    ];

    public function employees():HasMany
    {
        return $this->hasMany(BusinessUnitEmployee::class);
    }

    public function group()
    {
        return $this->belongsTo(BusinessUnitGroup::class, 'business_unit_group_id');
    }
}
