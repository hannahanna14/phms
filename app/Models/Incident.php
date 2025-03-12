<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Incident extends Model
{
    protected $fillable = [
        'student_id',
        'incident_date',
        'description',
        // Add other relevant fields
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}