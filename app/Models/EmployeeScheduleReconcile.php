<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeScheduleReconcile extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'employee_schedule_id',
        'employee_id',
        'reconcile_date',
        'date_start',
        'date_finish', // Add this
        'total_minutes',
        'total_hours', // Add this
        'status',
        'approved_by_user_id',
        'approved_at',
        'notes'
    ];

    protected $casts = [
        'reconcile_date' => 'date',
        'date_start' => 'datetime',
        'date_finish' => 'datetime', // Add cast for date_finish
        'approved_at' => 'datetime',
        'total_hours' => 'integer',
        'total_minutes' => 'integer',
    ];

    /**
     * Get the employee schedule this reconcile record refers to
     */
    public function employeeSchedule(): BelongsTo
    {
        return $this->belongsTo(EmployeeSchedule::class, 'employee_schedule_id', 'id');
    }

    /**
     * Get the employee this reconcile record belongs to
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the user who approved this reconcile
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    /**
     * Get all extra hours reconciliations associated with this reconcile
     */
    public function extraHourReconciliations(): HasMany
    {
        return $this->hasMany(ExtraHourReconciliation::class);
    }
}
