<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Incident extends Model
{
    protected $fillable = [
        'student_id',
        'date',
        'complaint',
        'actions_taken',
        'status',
        'grade_level',
        'school_year'
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}