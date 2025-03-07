<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

/**
 * @property string $full_name
 */
class Student extends Model
{
    use HasFactory;

    const MALE = 1;
    const FEMALE = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'birthday' => 'date',
        'birth_county_id' => 'integer',
        'domicile_county_id' => 'integer',
        'selection_county_id' => 'integer',
        'residence_county_id' => 'integer',
        'practice_county_id' => 'integer',
        'ethnicity_id' => 'integer',
        'sex_id' => 'integer',
        'foreign_language_id' => 'integer',
        'marital_status_id' => 'integer',
        'admission_exam_score' => 'decimal:2',
        'bac_grades_average' => 'decimal:2',
        'bac_romanian_language_grade' => 'decimal:2',
        'bac_main_subject_profile_grade' => 'decimal:2',
        'bac_subject_of_choice_profile_grade' => 'decimal:2',
        'high_school_avg_grade_for_1st_foreign_lang' => 'decimal:2',
        'high_school_avg_grade_for_2nd_foreign_lang' => 'decimal:2',
        'erp_student_class_id' => 'integer',
        'erp_schooling_period_id' => 'integer',
        'erp_id' => 'integer',
        'deleted_at' => 'datetime',
    ];


    public function sanctions(): BelongsToMany
    {
        return $this->belongsToMany(Sanction::class, 'student_sanction', 'student_id', 'sanction_id')
            ->withPivot(['id', 'date', 'user_id'])
            ->using(StudentSanction::class)
            ->withTimestamps();
    }

    public function latestSanctions(): BelongsToMany
    {
        return $this->sanctions()
            ->orderByPivot('date', 'desc');
    }

    public function studentClass(): BelongsTo
    {
        return $this->belongsTo(StudentClass::class);
    }

    public function birthCounty(): BelongsTo
    {
        return $this->belongsTo(County::class);
    }

    public function domicileCounty(): BelongsTo
    {
        return $this->belongsTo(County::class);
    }

    public function selectionCounty(): BelongsTo
    {
        return $this->belongsTo(County::class);
    }

    public function residenceCounty(): BelongsTo
    {
        return $this->belongsTo(County::class);
    }

    public function practiceCounty(): BelongsTo
    {
        return $this->belongsTo(County::class);
    }

    public function ethnicity(): BelongsTo
    {
        return $this->belongsTo(Ethnicity::class);
    }

    public function sex(): BelongsTo
    {
        return $this->belongsTo(Sex::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function maritalStatus(): BelongsTo
    {
        return $this->belongsTo(MaritalStatus::class);
    }


    public function formTeacher(): HasOneThrough
    {
        return $this->hasOneThrough(Teacher::class, StudentClass::class, 'id', 'id', 'student_class_id', 'teacher_id');
    }

    public function schoolingPeriod(): BelongsTo
    {
        return $this->belongsTo(SchoolingPeriod::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
