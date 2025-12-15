<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\HealthExamination;
use App\Models\OralHealthExamination;
use App\Models\Incident;
use App\Models\User;
use App\Models\StudentTeacherAssignment;

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
        'section',
        'lrn',
        'school_year',
        'is_active'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'is_active' => 'boolean',
    ];

    public function healthExaminations(): HasMany
    {
        return $this->hasMany(HealthExamination::class);
    }

    public function oralHealthExaminations()
    {
        return $this->hasMany(OralHealthExamination::class);
    }

    public function teacherAssignments(): HasMany
    {
        return $this->hasMany(StudentTeacherAssignment::class);
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

    /**
     * Accessor to determine if student is active for the current school year
     */
    public function getIsCurrentlyActiveAttribute()
    {
        // If no school_year is set, fall back to is_active flag
        if (empty($this->school_year)) {
            return (bool) $this->is_active;
        }

        $currentYear = date('Y');
        $currentMonth = date('n');
        if ($currentMonth >= 6) {
            $currentSchoolYear = $currentYear . '-' . ($currentYear + 1);
        } else {
            $currentSchoolYear = ($currentYear - 1) . '-' . $currentYear;
        }

        return (bool) ($this->is_active && $this->school_year === $currentSchoolYear);
    }
}
