<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\OralHealthExamination;

class DentalChartSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Add dental chart data for John Smith (student_id = 1) - Grade 6
        $johnExam = OralHealthExamination::where('student_id', 1)->where('grade_level', '6')->first();
        
        if ($johnExam) {
            $johnExam->update([
                'tooth_symbols' => [
                    // Only 1-2 permanent teeth with issues
                    '16' => ['X'], // Decayed molar
                    '46' => ['F'], // Filled molar
                    
                    // 1 temporary tooth with issue
                    '75' => ['X'], // Decayed temporary molar (using same symbol as permanent)
                ]
            ]);
            echo "Updated dental chart for John Smith - Grade 6\n";
        } else {
            echo "No existing record found for John Smith - Grade 6\n";
        }
        
        // Add dental chart data for Sarah Johnson (student_id = 2) - Grade 6
        $sarahExam = OralHealthExamination::where('student_id', 2)->where('grade_level', '6')->first();
        
        if (!$sarahExam) {
            // Create a new record for Sarah Johnson
            $sarahExam = OralHealthExamination::create([
                'student_id' => 2,
                'grade_level' => '6',
                'school_year' => '2024-2025',
                'examination_date' => now(),
                'permanent_teeth_decayed' => 2,
                'permanent_teeth_filled' => 1,
                'temporary_teeth_decayed' => 1,
                'temporary_teeth_filled' => 0,
            ]);
        }
        
        if ($sarahExam) {
            $sarahExam->update([
                'tooth_symbols' => [
                    // Only 1 permanent tooth with multiple issues
                    '12' => ['X', 'F'], // Decayed and filled incisor (2 conditions)
                    
                    // 1 temporary tooth with issue  
                    '64' => ['X'], // Decayed temporary molar
                ]
            ]);
            echo "Updated dental chart for Sarah Johnson - Grade 6\n";
        } else {
            echo "No existing record found for Sarah Johnson - Grade 6\n";
        }
    }
}
