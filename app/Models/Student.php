<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\HealthExamination;

class Student extends Model
{
    protected $fillable = [
        'full_name',
        'age',
        'sex'
    ];

    public function healthExaminations(): HasMany
    {
        return $this->hasMany(HealthExamination::class);
    }
}
