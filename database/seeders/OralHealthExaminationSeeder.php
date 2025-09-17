<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OralHealthExamination;
use App\Models\Student;

class OralHealthExaminationSeeder extends Seeder
{
    public function run()
    {
        // Focus on teacher's assigned students (John Smith and Sarah Johnson - IDs 1 and 2)
        $assignedStudentIds = [1, 2];
        $students = Student::whereIn('id', $assignedStudentIds)->get();

        foreach ($students as $student) {
            // Create oral health examinations for key grade levels
            $gradeLevels = ['Grade 4', 'Grade 5', 'Grade 6'];
            
            foreach ($gradeLevels as $gradeLevel) {
                // Skip if record already exists
                $exists = OralHealthExamination::where('student_id', $student->id)
                    ->where('grade_level', $gradeLevel)
                    ->exists();
                    
                if ($exists) continue;
                
                // Get grade number for calculations
                $gradeNum = (int) str_replace('Grade ', '', $gradeLevel);
                
                // More permanent teeth as grade increases, fewer temporary teeth
                $permanentTeethFactor = ($gradeNum - 3) / 3; // 0.33 for Grade 4, 0.67 for Grade 5, 1.0 for Grade 6
                $temporaryTeethFactor = max(0.1, 1 - ($gradeNum / 8)); // Decreases as grade increases
                
                try {
                    OralHealthExamination::create([
                        'student_id' => $student->id,
                        'grade_level' => $gradeLevel,
                        'school_year' => '2024-2025',
                        'examination_date' => now()->subDays(rand(1, 30)),
                        
                        // Permanent Teeth - realistic values based on grade
                        'permanent_index_dft' => round(rand(1, 3) * $permanentTeethFactor, 1),
                        'permanent_teeth_decayed' => rand(0, 3),
                        'permanent_teeth_filled' => rand(0, 2),
                        'permanent_total_dft' => rand(12, 24),
                        'permanent_for_extraction' => rand(0, 1),
                        'permanent_for_filling' => rand(0, 2),
                        
                        // Temporary Teeth - fewer as grade increases
                        'temporary_index_dft' => round(rand(1, 4) * $temporaryTeethFactor, 1),
                        'temporary_teeth_decayed' => round(rand(0, 2) * $temporaryTeethFactor),
                        'temporary_teeth_filled' => round(rand(0, 1) * $temporaryTeethFactor),
                        'temporary_total_dft' => round(rand(8, 16) * $temporaryTeethFactor),
                        'temporary_for_extraction' => round(rand(0, 1) * $temporaryTeethFactor),
                        'temporary_for_filling' => round(rand(0, 1) * $temporaryTeethFactor),
                    ]);
                    
                    echo "Created oral health record for {$student->full_name} - {$gradeLevel}\n";
                } catch (\Exception $e) {
                    echo "Error creating record for {$student->full_name} - {$gradeLevel}: " . $e->getMessage() . "\n";
                }
            }
        }
    }
}
