<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\HealthExamination;
use App\Models\OralHealthExamination;
use App\Models\Incident;

class Student extends Model
{
    protected $fillable = [
        'full_name',
        'age',
        'sex',
        'grade_level',
        'lrn',
        'school_year'
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
}
