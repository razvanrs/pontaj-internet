<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExtraHourReconciliation extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'extra_hour_id',
        'reconciliation_date',
        'minutes_reconciled',
        'status',
        'notes',
        'approved_by_user_id',
        'approved_at',
        'business_unit_id'
    ];

    protected $casts = [
        'reconciliation_date' => 'date',
        'approved_at' => 'datetime',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::created(function (ExtraHourReconciliation $reconciliation) {
            // Update the remaining minutes in the associated extra hour
            $extraHour = $reconciliation->extraHour;
            $extraHour->updateRemainingMinutes($reconciliation->minutes_reconciled);
        });

        static::deleted(function (ExtraHourReconciliation $reconciliation) {
            // If a reconciliation is deleted, restore the remaining minutes in the extra hour
            $extraHour = $reconciliation->extraHour;

            // Add the minutes back
            $extraHour->remaining_minutes += $reconciliation->minutes_reconciled;

            // Update the status
            if ($extraHour->remaining_minutes >= $extraHour->total_minutes) {
                $extraHour->status = 'available';
                $extraHour->is_fully_reconciled = false;
            } else {
                $extraHour->status = 'partially_reconciled';
                $extraHour->is_fully_reconciled = false;
            }

            $extraHour->save();
        });
    }

    /**
     * Get the employee that owns the reconciliation
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the extra hour being reconciled
     */
    public function extraHour(): BelongsTo
    {
        return $this->belongsTo(ExtraHour::class);
    }

    /**
     * Get the user who approved the reconciliation
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    /**
     * Get the business unit that this reconciliation belongs to
     */
    public function businessUnit(): BelongsTo
    {
        return $this->belongsTo(BusinessUnit::class);
    }

    /**
     * Format minutes reconciled to hours and minutes
     */
    public function getFormattedMinutesReconciledAttribute(): string
    {
        $hours = floor($this->minutes_reconciled / 60);
        $minutes = $this->minutes_reconciled % 60;

        return sprintf('%02d:%02d', $hours, $minutes);
    }

    /**
     * Approve the reconciliation
     */
    public function approve(int $userId): void
    {
        $this->approved_by_user_id = $userId;
        $this->approved_at = Carbon::now();
        $this->status = 'approved';
        $this->save();
    }
}
