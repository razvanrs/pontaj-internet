<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class Employee extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    // Define the casts property directly instead of using the casts method
    protected $casts = [
        'birthday' => 'date',
        'phone_numbers' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function militaryRank(): BelongsTo
    {
        return $this->belongsTo(MilitaryRank::class);
    }

    public function militaryRankType(): BelongsTo
    {
        return $this->belongsTo(MilitaryRankType::class);
    }

    public function sex(): BelongsTo
    {
        return $this->belongsTo(Sex::class);
    }

    /**
     * Get the teacher
     */
    public function teacher(): hasOne
    {
        return $this->hasOne(Teacher::class);
    }

    public function businessUnitEmployees()
    {
        return $this->hasMany(BusinessUnitEmployee::class);
    }

    public function businessUnits()
    {
        return $this->belongsToMany(BusinessUnit::class, 'business_unit_employees')
            ->withTimestamps();
    }

    /**
     * Get the user's avatar url
     *
     * @return string
     */
    public function getNovaIdAttribute()
    {
        return $this->full_name;
    }

    public function getPhoneNumbersAttribute($value)
    {
        $data = json_decode($value);

        if (!$data) {
            return [
                'fix' => '',
                'int' => '',
                'mobile' => [
                    'mobile_1' => '',
                    'mobile_2' => '',
                    'mobile_3' => '',
                ]

            ];
        }

        return $data;
    }

    public function setPhoneNumbersAttribute($value)
    {
        $this->attributes['phone_numbers'] = json_encode($value);
    }
}
