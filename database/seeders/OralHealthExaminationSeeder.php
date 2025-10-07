<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OralHealthExamination;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OralHealthExaminationSeeder extends Seeder
{
    public function run()
    {
        // Don't truncate - preserve existing data

        $students = Student::all();
        
        // All grade levels from Kinder 2 to Grade 6
        $allGradeLevels = ['Kinder 2', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6'];
        $currentYear = date('Y');
        
        // Oral health conditions for realistic data
        $oralConditions = [
            'gingivitis', 'periodontal_disease', 'malocclusion', 'supernumerary_teeth',
            'retained_deciduous_teeth', 'decubital_ulcer', 'calculus', 'cleft_lip_palate',
            'root_fragment', 'fluorosis', 'others_specify'
        ];

        $studentCount = 0;
        $examCount = 0;
        
        foreach ($students as $student) {
            $studentCount++;
            $this->command->info("Processing student {$studentCount}/{$students->count()}: {$student->full_name}");
            
            // Create oral health examinations for each grade level (complete dental journey)
            foreach ($allGradeLevels as $gradeIndex => $gradeLevel) {
                // Calculate school year for this grade (going backwards from current)
                $yearsBack = count($allGradeLevels) - 1 - $gradeIndex;
                $schoolYearStart = $currentYear - 1 - $yearsBack;
                $schoolYear = $schoolYearStart . '-' . ($schoolYearStart + 1);
                
                // Create 1-2 examinations per grade level
                $numExaminations = rand(1, 2);
                
                for ($i = 0; $i < $numExaminations; $i++) {
                    // Generate examination date within the school year
                    $schoolYearStartDate = Carbon::createFromDate($schoolYearStart, 6, 1); // June start
                    $schoolYearEndDate = Carbon::createFromDate($schoolYearStart + 1, 5, 31); // May end
                    $examDate = Carbon::createFromTimestamp(rand($schoolYearStartDate->timestamp, $schoolYearEndDate->timestamp));
                    
                    // Calculate age-appropriate dental data
                    $baseAge = $this->getBaseAge($gradeLevel);
                    $dentalData = $this->generateAgeAppropriateDentalData($baseAge, $gradeLevel);
                    
                    // Generate age-appropriate oral health conditions with better visibility
                    $conditions = $this->generateOralHealthConditions($baseAge, $examDate, $oralConditions);
                    
                    // Generate tooth symbols in ARRAY format for frontend compatibility
                    $toothSymbols = $this->generateToothSymbolsArray($baseAge);
                    
                    try {
                        OralHealthExamination::create([
                            'student_id' => $student->id,
                            'grade_level' => $gradeLevel,
                            'school_year' => $schoolYear,
                            'examination_date' => $examDate,
                            
                            // Permanent Teeth Data
                            'permanent_index_dft' => $dentalData['permanent']['index_dft'],
                            'permanent_teeth_decayed' => $dentalData['permanent']['decayed'],
                            'permanent_teeth_filled' => $dentalData['permanent']['filled'],
                            'permanent_teeth_missing' => $dentalData['permanent']['missing'],
                            'permanent_total_dft' => $dentalData['permanent']['total_dft'],
                            'permanent_for_extraction' => $dentalData['permanent']['for_extraction'],
                            'permanent_for_filling' => $dentalData['permanent']['for_filling'],
                            
                            // Temporary Teeth Data
                            'temporary_index_dft' => $dentalData['temporary']['index_dft'],
                            'temporary_teeth_decayed' => $dentalData['temporary']['decayed'],
                            'temporary_teeth_filled' => $dentalData['temporary']['filled'],
                            'temporary_teeth_missing' => $dentalData['temporary']['missing'],
                            'temporary_total_dft' => $dentalData['temporary']['total_dft'],
                            'temporary_for_extraction' => $dentalData['temporary']['for_extraction'],
                            'temporary_for_filling' => $dentalData['temporary']['for_filling'],
                            
                            // Additional data
                            'tooth_symbols' => $toothSymbols,
                            'conditions' => $conditions,
                        ]);
                        
                        $examCount++;
                    } catch (\Exception $e) {
                        $this->command->error("Error creating oral health exam for student {$student->id}, grade {$gradeLevel}: " . $e->getMessage());
                        continue;
                    }
                }
            }
        }
        
        $this->command->info("Created {$examCount} oral health examination records for {$studentCount} students across all grade levels");
    }
    
    private function getBaseAge($gradeLevel)
    {
        return match($gradeLevel) {
            'Kinder 2' => 5,
            'Grade 1' => 6,
            'Grade 2' => 7,
            'Grade 3' => 8,
            'Grade 4' => 9,
            'Grade 5' => 10,
            'Grade 6' => 11,
            default => 8
        };
    }
    
    private function generateAgeAppropriateDentalData($age, $gradeLevel)
    {
        // Permanent teeth emergence and temporary teeth loss patterns
        $permanentTeethFactor = match($age) {
            5 => 0.1,  // Kinder 2: Very few permanent teeth
            6 => 0.2,  // Grade 1: First molars starting
            7 => 0.4,  // Grade 2: Incisors emerging
            8 => 0.6,  // Grade 3: More permanent teeth
            9 => 0.8,  // Grade 4: Mixed dentition
            10 => 0.9, // Grade 5: Mostly permanent
            11 => 1.0, // Grade 6: Full permanent dentition
            default => 0.5
        };
        
        $temporaryTeethFactor = match($age) {
            5 => 1.0,  // Kinder 2: Full temporary teeth
            6 => 0.9,  // Grade 1: Starting to lose some
            7 => 0.7,  // Grade 2: Losing front teeth
            8 => 0.5,  // Grade 3: Mixed dentition
            9 => 0.3,  // Grade 4: Few temporary left
            10 => 0.1, // Grade 5: Very few temporary
            11 => 0.05, // Grade 6: Almost no temporary
            default => 0.5
        };
        
        return [
            'permanent' => [
                'index_dft' => (int)(rand(0, 3) * $permanentTeethFactor),
                'decayed' => rand(0, (int)(3 * $permanentTeethFactor)),
                'filled' => rand(0, (int)(2 * $permanentTeethFactor)),
                'missing' => $this->generateMissingPermanentTeeth($baseAge, $permanentTeethFactor), // Age-appropriate missing teeth
                'total_dft' => (int)(rand(0, 28) * $permanentTeethFactor),
                'for_extraction' => rand(0, 1) * ($permanentTeethFactor > 0.5 ? 1 : 0),
                'for_filling' => rand(0, (int)(2 * $permanentTeethFactor)),
            ],
            'temporary' => [
                'index_dft' => (int)(rand(0, 4) * $temporaryTeethFactor),
                'decayed' => rand(0, (int)(3 * $temporaryTeethFactor)),
                'filled' => rand(0, (int)(2 * $temporaryTeethFactor)),
                'missing' => rand(0, (int)(1 * $temporaryTeethFactor)), // Add missing teeth count
                'total_dft' => (int)(rand(0, 20) * $temporaryTeethFactor),
                'for_extraction' => rand(0, (int)(2 * $temporaryTeethFactor)),
                'for_filling' => rand(0, (int)(1 * $temporaryTeethFactor)),
            ]
        ];
    }
    
    private function generateToothSymbolsArray($baseAge)
    {
        $toothConditions = ['D', 'F', 'X', 'RF', 'M'];
        $toothSymbols = [];
        
        // Increase probability of having tooth symbols for better visibility (80% chance)
        if (rand(1, 100) > 80) {
            return null; // 20% chance of no symbols
        }
        
        // Age-appropriate tooth selection with guaranteed permanent and temporary mix for older children
        if ($baseAge <= 6) {
            // Younger children (Kinder 2 - Grade 1): Only temporary teeth
            $temporaryTeeth = ['55', '54', '61', '62', '75', '74', '81', '82', '51', '71'];
            
            // Add 1-2 temporary teeth
            $numTeeth = rand(1, 2);
            for ($i = 0; $i < $numTeeth; $i++) {
                $toothNumber = $temporaryTeeth[array_rand($temporaryTeeth)];
                $condition = ['D', 'F'][array_rand(['D', 'F'])]; // Simple conditions for young children
                
                if (!isset($toothSymbols[$toothNumber])) {
                    $toothSymbols[$toothNumber] = [$condition];
                }
            }
            
        } else {
            // Older children (Grade 2+): Mix of permanent and temporary teeth
            $permanentTeeth = ['16', '21', '26', '36', '41', '46', '11', '31']; // Common permanent teeth
            $temporaryTeeth = ['55', '54', '61', '62', '75', '74', '81', '82']; // Common temporary teeth
            
            // Add 1-2 permanent teeth
            $numPermanent = rand(1, 2);
            for ($i = 0; $i < $numPermanent; $i++) {
                $toothNumber = $permanentTeeth[array_rand($permanentTeeth)];
                $condition = $toothConditions[array_rand($toothConditions)];
                
                if (!isset($toothSymbols[$toothNumber])) {
                    $toothSymbols[$toothNumber] = [$condition];
                }
            }
            
            // Add 1-2 temporary teeth (if age appropriate)
            if ($baseAge <= 10) { // Temporary teeth still present up to Grade 5
                $numTemporary = rand(1, 2);
                for ($i = 0; $i < $numTemporary; $i++) {
                    $toothNumber = $temporaryTeeth[array_rand($temporaryTeeth)];
                    $condition = ['D', 'F', 'X'][array_rand(['D', 'F', 'X'])]; // Common conditions for temporary teeth
                    
                    if (!isset($toothSymbols[$toothNumber])) {
                        $toothSymbols[$toothNumber] = [$condition];
                    }
                }
            }
        }
        
        return !empty($toothSymbols) ? json_encode($toothSymbols) : null;
    }
    
    private function generateOralHealthConditions($baseAge, $examDate, $oralConditions)
    {
        // Age-appropriate condition probability and types
        $conditionProbability = match($baseAge) {
            5 => 25,  // Kinder 2: 25% chance (common early childhood issues)
            6 => 30,  // Grade 1: 30% chance
            7 => 35,  // Grade 2: 35% chance
            8 => 40,  // Grade 3: 40% chance (peak mixed dentition issues)
            9 => 35,  // Grade 4: 35% chance
            10 => 30, // Grade 5: 30% chance
            11 => 25, // Grade 6: 25% chance (better oral hygiene habits)
            default => 30
        };
        
        // Skip conditions for some examinations
        if (rand(1, 100) > $conditionProbability) {
            return null;
        }
        
        // Age-appropriate condition selection
        $ageAppropriateConditions = match($baseAge) {
            5, 6 => [
                'gingivitis', 'calculus', 'malocclusion', 'retained_deciduous_teeth'
            ],
            7, 8 => [
                'gingivitis', 'calculus', 'malocclusion', 'retained_deciduous_teeth', 
                'supernumerary_teeth', 'decubital_ulcer'
            ],
            9, 10, 11 => [
                'gingivitis', 'periodontal_disease', 'malocclusion', 'calculus',
                'supernumerary_teeth', 'fluorosis'
            ],
            default => ['gingivitis', 'calculus', 'malocclusion']
        };
        
        // Select 1-2 conditions (weighted toward single condition)
        $numConditions = rand(1, 100) <= 80 ? 1 : 2; // 80% single, 20% multiple
        $selectedConditions = [];
        
        for ($i = 0; $i < $numConditions; $i++) {
            $condition = $ageAppropriateConditions[array_rand($ageAppropriateConditions)];
            
            // Avoid duplicates
            if (!isset($selectedConditions[$condition])) {
                $selectedConditions[$condition] = $examDate->format('m/d/y');
            }
        }
        
        return !empty($selectedConditions) ? json_encode($selectedConditions) : null;
    }
    
    private function generateMissingPermanentTeeth($baseAge, $permanentTeethFactor)
    {
        // Age-appropriate logic for missing permanent teeth
        // For demonstration purposes, make it more likely to show data
        $missingProbability = match($baseAge) {
            5, 6 => 10,   // Kinder 2, Grade 1: Rare but possible (10% chance)
            7 => 20,      // Grade 2: Low chance (20% chance)
            8 => 30,      // Grade 3: Moderate chance (30% chance)
            9 => 40,      // Grade 4: Higher chance (40% chance)
            10 => 50,     // Grade 5: Common (50% chance)
            11 => 60,     // Grade 6: Most common (60% chance)
            default => 30
        };
        
        // Generate missing teeth if random chance hits
        if (rand(1, 100) <= $missingProbability) {
            return rand(0, 1); // 0 or 1 missing tooth maximum
        }
        
        return 0; // No missing teeth
    }
}
