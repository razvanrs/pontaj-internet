<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;

class ExtraHour extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'employee_schedule_id',
        'segment_number',
        'date',
        'start_time',
        'end_time',
        'total_minutes',
        'status',
        'expiry_date',
        'remaining_minutes',
        'is_fully_reconciled',
        'business_unit_id',
        'description'
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'expiry_date' => 'date',
        'is_fully_reconciled' => 'boolean',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (ExtraHour $extraHour) {
            // Calculate total minutes
            $extraHour->total_minutes = Carbon::parse($extraHour->end_time)->diffInMinutes(Carbon::parse($extraHour->start_time));

            // Set initial remaining minutes equal to total minutes
            $extraHour->remaining_minutes = $extraHour->total_minutes;

            // Set expiry date from configuration
            $expiryDays = Config::get('extra_hours.expiry_days', 90);
            $extraHour->expiry_date = Carbon::parse($extraHour->date)->addDays($expiryDays);

            // Set initial status and reconciliation flag
            $extraHour->status = Config::get('extra_hours.default_status', 'available');
            $extraHour->is_fully_reconciled = false;

            // Set description if not provided
            if (empty($extraHour->description)) {
                $start = Carbon::parse($extraHour->start_time);
                $end = Carbon::parse($extraHour->end_time);

                $dayName = $start->translatedFormat('l');
                $startTime = $start->format('H:i');
                $endTime = $end->format('H:i');

                // Check if it's weekend
                $weekendDays = Config::get('extra_hours.weekend_days', [0, 6]);
                $isWeekend = in_array($start->dayOfWeek, $weekendDays);

                if ($isWeekend) {
                    $extraHour->description = "Weekend: {$dayName} {$startTime} - {$endTime}";
                } else {
                    $extraHour->description = "Extra hours: {$dayName} {$startTime} - {$endTime}";
                }
            }
        });
    }

    /**
     * Get the overtime justification from the associated employee schedule
     */
    public function getOvertimeJustificationAttribute()
    {
        if ($this->employeeSchedule) {
            return $this->employeeSchedule->overtime_justification;
        }
        return null;
    }

    /**
     * Get the employee that owns the extra hours
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the original employee schedule that generated these extra hours
     */
    public function employeeSchedule(): BelongsTo
    {
        return $this->belongsTo(EmployeeSchedule::class);
    }

    /**
     * Get the business unit that this extra hour belongs to
     */
    public function businessUnit(): BelongsTo
    {
        return $this->belongsTo(BusinessUnit::class);
    }

    /**
     * Get all reconciliations for this extra hour
     */
    public function reconciliations(): HasMany
    {
        return $this->hasMany(ExtraHourReconciliation::class);
    }

    /**
     * Check if the extra hour has expired
     */
    public function isExpired(): bool
    {
        return $this->expiry_date < Carbon::now()->startOfDay();
    }

    /**
     * Update remaining minutes after a reconciliation
     */
    public function updateRemainingMinutes(int $minutesUsed): void
    {
        $this->remaining_minutes -= $minutesUsed;

        if ($this->remaining_minutes <= 0) {
            $this->is_fully_reconciled = true;
            $this->status = 'reconciled';
        } else {
            $this->status = 'partially_reconciled';
        }

        $this->save();
    }

    /**
     * Get available extra hours for an employee that haven't expired
     */
    public static function getAvailableForEmployee(int $employeeId): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('employee_id', $employeeId)
            ->where('expiry_date', '>=', Carbon::now()->startOfDay())
            ->where('is_fully_reconciled', false)
            ->where('remaining_minutes', '>', 0)
            ->orderBy('date', 'asc')
            ->get();
    }

    /**
     * Format total minutes to hours and minutes
     */
    public function getFormattedTotalTimeAttribute(): string
    {
        $hours = floor($this->total_minutes / 60);
        $minutes = $this->total_minutes % 60;

        return sprintf('%02d:%02d', $hours, $minutes);
    }

    /**
     * Format remaining minutes to hours and minutes
     */
    public function getFormattedRemainingTimeAttribute(): string
    {
        $hours = floor($this->remaining_minutes / 60);
        $minutes = $this->remaining_minutes % 60;

        return sprintf('%02d:%02d', $hours, $minutes);
    }

    /**
     * Get grouped extra hours by day
     */
    public static function getGroupedByDay(int $employeeId, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = self::where('employee_id', $employeeId)
            ->where('is_fully_reconciled', false);

        if ($startDate) {
            $query->where('date', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('date', '<=', $endDate);
        }

        $extraHours = $query->orderBy('date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        $grouped = [];

        foreach ($extraHours as $extraHour) {
            $dateKey = $extraHour->date->format('Y-m-d');

            if (!isset($grouped[$dateKey])) {
                $grouped[$dateKey] = [
                    'date' => $extraHour->date,
                    'formatted_date' => $extraHour->date->format('d.m.Y'),
                    'day_name' => $extraHour->date->translatedFormat('l'),
                    'total_minutes' => 0,
                    'remaining_minutes' => 0,
                    'hours' => []
                ];
            }

            $grouped[$dateKey]['hours'][] = $extraHour;
            $grouped[$dateKey]['total_minutes'] += $extraHour->total_minutes;
            $grouped[$dateKey]['remaining_minutes'] += $extraHour->remaining_minutes;
        }

        // Add formatted times
        foreach ($grouped as &$day) {
            $day['formatted_total_time'] = self::formatMinutes($day['total_minutes']);
            $day['formatted_remaining_time'] = self::formatMinutes($day['remaining_minutes']);
        }

        return $grouped;
    }

    /**
     * Helper method to format minutes as hours:minutes
     */
    protected static function formatMinutes(int $minutes): string
    {
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        return sprintf('%02d:%02d', $hours, $remainingMinutes);
    }
}
