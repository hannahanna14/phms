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
                    // Upper permanent teeth - fewer markers for cleaner look
                    '18' => ['X'], // Decayed wisdom tooth
                    '16' => ['D'], // Decayed molar
                    '11' => ['X'], // Decayed central incisor
                    '24' => ['RF'], // Root fragment premolar
                    '28' => ['M'], // Missing wisdom tooth
                    
                    // Lower permanent teeth - fewer markers
                    '48' => ['M'], // Missing wisdom tooth
                    '46' => ['X'], // Decayed molar
                    '31' => ['D'], // Decayed central incisor
                    '37' => ['X'], // Decayed molar
                    '38' => ['M'], // Missing wisdom tooth
                    
                    // Temporary teeth (baby teeth) for Grade 6
                    '75' => ['D'], // Decayed temporary molar
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
                    // Upper permanent teeth - cleaner pattern
                    '17' => ['F2'], // Treatment molar
                    '16' => ['D'], // Decayed molar
                    '12' => ['X'], // Decayed incisor
                    '25' => ['D'], // Decayed premolar
                    '28' => ['M'], // Missing wisdom tooth
                    
                    // Lower permanent teeth - cleaner pattern
                    '48' => ['M'], // Missing wisdom tooth
                    '47' => ['X'], // Decayed molar
                    '32' => ['D'], // Decayed incisor
                    '36' => ['F2'], // Treatment molar
                    '38' => ['M'], // Missing wisdom tooth
                    
                    // Temporary teeth
                    '74' => ['D'], // Decayed temporary molar
                ]
            ]);
            echo "Updated dental chart for Sarah Johnson - Grade 6\n";
        } else {
            echo "No existing record found for Sarah Johnson - Grade 6\n";
        }
    }
}
