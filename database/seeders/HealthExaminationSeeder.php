<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\HealthExamination;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class HealthExaminationSeeder extends Seeder
{
    public function run()
    {
        $students = Student::all();

        foreach ($students as $student) {
            // Truncate health-related tables
        DB::table('health_examinations')->truncate();
        DB::table('oral_healths')->truncate();
        DB::table('incidents')->truncate();
            HealthExamination::create([
                'student_id' => $student->id,
                'examination_date' => Carbon::now(),
                'temperature' => 36.5,
                'heart_rate' => '72 bpm',
                'height' => $student->sex === 'Male' ? '170 cm' : '160 cm',
                'weight' => $student->sex === 'Male' ? '65 kg' : '55 kg',
                'nutritional_status_bmi' => 'Normal',
                'nutritional_status_height' => 'Normal',
                'vision_screening' => 'Normal',
                'auditory_screening' => 'Normal',
                'skin' => 'Clear',
                'scalp' => 'Healthy',
                'eye' => 'Normal',
                'ear' => 'Normal',
                'nose' => 'Normal',
                'mouth' => 'Healthy',
                'neck' => 'Normal',
                'throat' => 'Clear',
                'lungs_heart' => 'Normal',
                'abdomen' => 'Soft',
                'deformities' => 'None',
                'remarks' => 'Routine health check',
                
                // New columns
                'deworming_status' => $student->id % 2 === 0 ? 'dewormed' : 'not_dewormed',
                'iron_supplementation' => $student->id % 3 === 0 ? 'positive' : 'negative'
            ]);
        }
    }
}