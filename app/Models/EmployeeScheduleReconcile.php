<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeScheduleReconcile extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function employeeSchedule(): BelongsTo
    {
        return $this->belongsTo(EmployeeSchedule::class, 'employee_shedule_id', 'id');
    }
}
