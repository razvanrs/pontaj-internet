<?php

namespace App\Models;

use App\Data\TeacherScheduleData;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\LaravelData\WithData;

class TeacherSchedule extends Model
{
    use HasFactory;
    use SoftDeletes;
    use WithData;

    protected $dataClass = TeacherScheduleData::class;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date_start' => 'datetime:Y-m-d H:i:s',
        'date_finish' => 'datetime:Y-m-d H:i:s',
    ];


    public function learningActivityType(): BelongsTo
    {
        return $this->belongsTo(LearningActivityType::class, 'learning_activity_type_id', 'id');
    }

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

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class, 'module_id', 'id');
    }

    public function ability(): BelongsTo
    {
        return $this->belongsTo(Ability::class, 'ability_id', 'id');
    }

    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class, 'theme_id', 'id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'id');
    }
}
