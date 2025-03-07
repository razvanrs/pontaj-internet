<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property date $date
 * @property int $user_id
 * @property int $student_id
 * @property int $sanction_id
 */
class StudentSanction extends Pivot
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date',
    ];


    protected $casts = [
        'date' => 'date',
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sanction(): BelongsTo
    {
        return $this->belongsTo(Sanction::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
