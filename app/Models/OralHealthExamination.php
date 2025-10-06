<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OralHealthExamination extends Model
{
    protected $fillable = [
        'student_id',
        'grade_level',
        'school_year',
        'examination_date',
        'permanent_index_dft',
        'permanent_teeth_decayed',
        'permanent_teeth_filled',
        'permanent_total_dft',
        'permanent_for_extraction',
        'permanent_for_filling',
        'temporary_index_dft',
        'temporary_teeth_decayed',
        'temporary_teeth_filled',
        'temporary_total_dft',
        'temporary_for_extraction',
        'temporary_for_filling',
        'tooth_symbols',
        'conditions',
    ];

    protected $casts = [
        'tooth_symbols' => 'array',
        'conditions' => 'array',
        'examination_date' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}