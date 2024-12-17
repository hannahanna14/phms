<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthExamination extends Model
{
    protected $fillable = [
        'student_id',
        'examination_date',
        'temperature',
        'heart_rate',
        'height',
        'weight',
        'nutritional_status_bmi',
        'nutritional_status_height',
        'vision_screening',
        'auditory_screening',
        'skin',
        'scalp',
        'eye',
        'ear',
        'nose',
        'mouth',
        'neck',
        'throat',
        'lungs_heart',
        'abdomen',
        'deformities',
        'remarks'
    ];

    protected $casts = [
        'examination_date' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
