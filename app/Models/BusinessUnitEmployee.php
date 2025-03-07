<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessUnitEmployee extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'business_unit_id',
        'employee_id'
    ];

    /**
     * The business unit that belongs to
     * @return BelongsTo
     */
    public function businessUnit(): BelongsTo
    {
        return $this->belongsTo(BusinessUnit::class);
    }

    /**
     * The employee unit that belongs to
     * @return BelongsTo
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
