<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthTreatment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'date',
        'title',
        'chief_complaint',
        'treatment',
        'status',
        'remarks',
        'grade_level',
        'school_year',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
