<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\HealthExamination;
use App\Models\OralHealthExamination;
use App\Models\Incident;

class Student extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'full_name',
        'age',
        'sex',
        'date_of_birth',
        'birthplace',
        'parent_guardian',
        'address',
        'grade_level',
        'lrn',
        'school_year'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function healthExaminations(): HasMany
    {
        return $this->hasMany(HealthExamination::class);
    }

    public function oralHealthExaminations()
    {
        return $this->hasMany(OralHealthExamination::class);
    }

    public function assignedTeachers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'student_teacher_assignments', 'student_id', 'teacher_id')
                    ->withPivot('grade_level', 'section', 'school_year')
                    ->withTimestamps();
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['full_name', 'age', 'sex', 'date_of_birth', 'birthplace', 'parent_guardian', 'address', 'grade_level', 'lrn', 'school_year'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
