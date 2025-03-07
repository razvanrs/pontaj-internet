<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Sanction extends Model
{
    use HasFactory;

    public $fillable = [
        'long_description',
        'short_description',
        'sel_order',
    ];

    public $casts = [
        'sel_order' => 'integer',
    ];

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'student_sanction', 'sanction_id', 'student_id')
            ->withPivot('date')
            ->using(StudentSanction::class)
            ->withTimestamps();
    }
}
