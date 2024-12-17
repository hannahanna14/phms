<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OralHealthExamination extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'index_dft',
        'number_of_teeth_decayed',
        'number_of_teeth_filled',
        'total_dft',
        'for_extraction',
        'for_filling',
        'examination_date'
    ];

    protected $casts = [
        'examination_date' => 'date'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
