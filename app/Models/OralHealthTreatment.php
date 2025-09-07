<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OralHealthTreatment extends Model
{
    protected $fillable = [
        'student_id',
        'date',
        'title',
        'chief_complaint',
        'treatment',
        'remarks',
        'status',
        'grade_level',
        'school_year'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
