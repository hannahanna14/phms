<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    use Notifiable, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'full_name',
        'password',
        'role',
        'last_read_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_read_at' => 'datetime',
    ];

    public function healthExaminations()
    {
        return $this->hasMany(HealthExamination::class);
    }

    public function assignedStudents()
    {
        return $this->hasMany(StudentTeacherAssignment::class, 'teacher_id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_teacher_assignments', 'teacher_id', 'student_id')
                    ->withPivot('grade_level', 'section', 'school_year')
                    ->withTimestamps();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['username', 'full_name', 'role'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
