<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

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
}
