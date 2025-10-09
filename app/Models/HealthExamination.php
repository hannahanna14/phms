<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthExamination extends Model
{
    protected $fillable = [
        'student_id',
        'grade_level',
        'school_year',
        'examination_date',
        'temperature',
        'heart_rate',
        'height',
        'weight',
        'nutritional_status_bmi',
        'nutritional_status_height',
        'vision_screening',
        'vision_screening_specify',
        'auditory_screening',
        'auditory_screening_specify',
        'skin',
        'skin_specify',
        'scalp',
        'scalp_specify',
        'eye',
        'eye_specify',
        'ear',
        'ear_specify',
        'nose',
        'nose_specify',
        'mouth',
        'mouth_specify',
        'neck',
        'neck_specify',
        'throat',
        'throat_specify',
        'lungs_heart',
        'lungs',
        'lungs_other_specify',
        'lungs_specify',
        'heart',
        'heart_other_specify',
        'heart_specify',
        'abdomen',
        'abdomen_specify',
        'deformities',
        'deformities_specify',
        'deworming_status',
        'iron_supplementation',
        'sbfp_beneficiary',
        'four_ps_beneficiary',
        'immunization',
        'other_specify',
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
