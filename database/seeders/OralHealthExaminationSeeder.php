<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OralHealthExamination;
use App\Models\Student;

class OralHealthExaminationSeeder extends Seeder
{
    public function run()
    {
        $students = Student::all();

        foreach ($students as $student) {
            // Create oral health examination only for student's current grade level
            OralHealthExamination::create([
                'student_id' => $student->id,
                'grade_level' => $student->grade_level,
                'school_year' => $student->school_year,
                
                // Permanent Teeth - sample data for current grade only
                'permanent_index_dft' => rand(1, 5),
                'permanent_teeth_decayed' => rand(0, 3),
                'permanent_teeth_filled' => rand(0, 2),
                'permanent_total_dft' => rand(10, 20),
                'permanent_for_extraction' => rand(0, 2),
                'permanent_for_filling' => rand(0, 2),
                
                // Temporary Teeth - sample data for current grade only
                'temporary_index_dft' => rand(1, 4),
                'temporary_teeth_decayed' => rand(0, 2),
                'temporary_teeth_filled' => rand(0, 2),
                'temporary_total_dft' => rand(8, 15),
                'temporary_for_extraction' => rand(0, 2),
                'temporary_for_filling' => rand(0, 2),
                
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
