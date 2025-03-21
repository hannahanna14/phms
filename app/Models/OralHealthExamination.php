<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OralHealthExamination extends Model
{
    protected $fillable = [
        'student_id',
        'examination_date',
        // Add other relevant fields
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}