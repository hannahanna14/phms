<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\HealthExamination;
use App\Models\OralHealthExamination;
use App\Models\Incident;

class Student extends Model
{
    protected $fillable = [
        'full_name',
        'age',
        'sex',
        'grade_level',
        'lrn',
        'school_year'
    ];

    public function healthExaminations(): HasMany
    {
        return $this->hasMany(HealthExamination::class);
    }

    public function oralHealthExaminations(): HasMany
    {
        return $this->hasMany(OralHealthExamination::class);
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class);
    }
}
