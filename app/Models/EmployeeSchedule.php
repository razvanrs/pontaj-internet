<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeSchedule extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'year_id',
        'week_id',
        'day_id',
        'employee_id',
        'schedule_status_id',
        'date_start',
        'date_finish',
        'total_minutes',
        'total_hours',
        'display_code',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::created(function (EmployeeSchedule $rec) {
            $rec->total_minutes = $rec->date_finish->diffInMinutes($rec->date_start);
            $rec->total_hours = $rec->date_finish->diffInHours($rec->date_start);
        });

        static::updated(function (EmployeeSchedule $rec) {
            $rec->total_minutes = $rec->date_finish->diffInMinutes($rec->date_start);
            $rec->total_hours = $rec->date_finish->diffInHours($rec->date_start);
        });

        static::saving(function (EmployeeSchedule $rec) {
            $rec->date_start = $rec->date_start->format("Y-m-d H:i:s");
            $rec->date_finish = $rec->date_finish->format("Y-m-d H:i:s");
        });

        static::updating(function (EmployeeSchedule $rec) {
            $rec->date_start = $rec->date_start->format("Y-m-d H:i:s");
            $rec->date_finish = $rec->date_finish->format("Y-m-d H:i:s");
        });
    }

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date_start' => 'datetime:Y-m-d H:i:s',
        'date_finish' => 'datetime:Y-m-d H:i:s',
    ];

    public function year(): BelongsTo
    {
        return $this->belongsTo(Year::class, 'year_id', 'id');
    }

    public function week(): BelongsTo
    {
        return $this->belongsTo(Week::class, 'week_id', 'id');
    }

    public function day(): BelongsTo
    {
        return $this->belongsTo(Day::class, 'day_id', 'id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    public function scheduleStatus(): BelongsTo
    {
        return $this->belongsTo(ScheduleStatus::class, 'schedule_status_id', 'id');
    }
}