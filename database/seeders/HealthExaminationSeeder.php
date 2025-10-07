<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\HealthExamination;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HealthExaminationSeeder extends Seeder
{
    public function run()
    {
        // Truncate health examinations table
        DB::table('health_examinations')->truncate();

        $students = Student::all();
        
        // Health condition options
        $normalConditions = ['Normal', 'Healthy', 'Clear', 'Good'];
        $minorConditions = ['Mild irritation', 'Slight redness', 'Minor concern', 'Needs monitoring'];
        $skinConditions = ['Clear', 'Dry skin', 'Minor rash', 'Healthy', 'Slight irritation'];
        $visionConditions = ['20/20', '20/25', '20/30', 'Normal', 'Needs glasses'];
        $auditoryConditions = ['Normal', 'Clear', 'Good hearing', 'Slight wax buildup'];
        $nutritionalStatuses = ['Normal', 'Underweight', 'Overweight', 'Severely Underweight', 'Obese'];
        $dewormingStatuses = ['dewormed', 'not_dewormed', 'pending'];
        $ironSupplementation = ['positive', 'negative', 'pending'];
        $beneficiaryStatuses = ['yes', 'no'];
        $immunizationStatuses = ['complete', 'incomplete', 'up_to_date', 'needs_update'];

        // All grade levels from Kinder 2 to Grade 6
        $allGradeLevels = ['Kinder 2', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6'];
        $currentYear = date('Y');
        
        foreach ($students as $student) {
            // Create health examinations for each grade level (complete school journey)
            foreach ($allGradeLevels as $gradeIndex => $gradeLevel) {
                // Calculate school year for this grade (going backwards from current)
                $yearsBack = count($allGradeLevels) - 1 - $gradeIndex;
                $schoolYearStart = $currentYear - 1 - $yearsBack;
                $schoolYear = $schoolYearStart . '-' . ($schoolYearStart + 1);
                
                // Create 1-3 examinations per grade level
                $numExaminations = rand(1, 3);
                
                for ($i = 0; $i < $numExaminations; $i++) {
                    // Generate realistic vital signs based on grade level age
                    $baseAge = $this->getBaseAge($gradeLevel);
                    $temperature = round(36.0 + (rand(0, 15) / 10), 1); // 36.0 - 37.5°C
                    $heartRate = $this->getAgeAppropriateHeartRate($baseAge);
                    $height = $this->getAgeAppropriateHeight($baseAge, $student->sex);
                    $weight = $this->getAgeAppropriateWeight($baseAge, $student->sex);
                    
                    // Calculate BMI status
                    $heightInMeters = floatval(str_replace(' cm', '', $height)) / 100;
                    $weightInKg = floatval(str_replace(' kg', '', $weight));
                    $bmi = $weightInKg / ($heightInMeters * $heightInMeters);
                    $bmiStatus = $this->getBMIStatus($bmi);
                    
                    // Generate examination date within the school year
                    $schoolYearStartDate = Carbon::createFromDate($schoolYearStart, 6, 1); // June start
                    $schoolYearEndDate = Carbon::createFromDate($schoolYearStart + 1, 5, 31); // May end
                    $examDate = Carbon::createFromTimestamp(rand($schoolYearStartDate->timestamp, $schoolYearEndDate->timestamp));
                    
                    // Randomly assign some health issues (85% normal, 15% minor issues for younger grades)
                    $issueChance = $baseAge <= 7 ? 10 : 20; // Younger kids have fewer issues
                    $hasMinorIssues = rand(1, 100) <= $issueChance;
                    
                    HealthExamination::create([
                        'student_id' => $student->id,
                        'grade_level' => $gradeLevel,
                        'school_year' => $schoolYear,
                        'examination_date' => $examDate,
                        'temperature' => $temperature,
                        'heart_rate' => $heartRate . ' bpm',
                        'height' => $height,
                        'weight' => $weight,
                        'nutritional_status_bmi' => $bmiStatus,
                        'nutritional_status_height' => $this->getHeightStatus($baseAge, floatval(str_replace(' cm', '', $height))),
                        'vision_screening' => $visionConditions[array_rand($visionConditions)],
                        'auditory_screening' => $auditoryConditions[array_rand($auditoryConditions)],
                        'skin' => $hasMinorIssues && rand(1, 100) <= 30 ? $minorConditions[array_rand($minorConditions)] : $skinConditions[array_rand($skinConditions)],
                        'scalp' => $hasMinorIssues && rand(1, 100) <= 20 ? 'Dandruff' : 'Healthy',
                        'eye' => $hasMinorIssues && rand(1, 100) <= 25 ? 'Slight redness' : 'Normal',
                        'ear' => $hasMinorIssues && rand(1, 100) <= 15 ? 'Wax buildup' : 'Normal',
                        'nose' => $hasMinorIssues && rand(1, 100) <= 20 ? 'Congestion' : 'Normal',
                        'mouth' => $hasMinorIssues && rand(1, 100) <= 25 ? 'Needs dental care' : 'Healthy',
                        'neck' => $normalConditions[array_rand($normalConditions)],
                        'throat' => $hasMinorIssues && rand(1, 100) <= 15 ? 'Slightly red' : 'Clear',
                        'lungs_heart' => 'Normal',
                        'lungs' => 'Clear',
                        'heart' => 'Regular rhythm',
                        'abdomen' => 'Soft, non-tender',
                        'deformities' => rand(1, 100) <= 5 ? 'Minor postural issue' : 'None',
                        'deworming_status' => $dewormingStatuses[array_rand($dewormingStatuses)],
                        'iron_supplementation' => $ironSupplementation[array_rand($ironSupplementation)],
                        'sbfp_beneficiary' => $beneficiaryStatuses[array_rand($beneficiaryStatuses)],
                        'four_ps_beneficiary' => $beneficiaryStatuses[array_rand($beneficiaryStatuses)],
                        'immunization' => $immunizationStatuses[array_rand($immunizationStatuses)],
                        'other_specify' => rand(1, 100) <= 10 ? 'Allergic to peanuts' : null,
                        'remarks' => $this->generateRemarks($hasMinorIssues, $examDate, $gradeLevel)
                    ]);
                }
            }
        }
        
        $this->command->info('Created comprehensive health examination records for all 100 students across all grade levels');
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
    
    private function getAgeAppropriateHeartRate($age)
    {
        // Age-appropriate heart rates for children
        $heartRates = [
            5 => [80, 120],
            6 => [75, 115],
            7 => [70, 110],
            8 => [70, 110],
            9 => [70, 110],
            10 => [70, 110],
            11 => [60, 105]
        ];
        
        $range = $heartRates[$age] ?? [70, 110];
        return rand($range[0], $range[1]);
    }
    
    private function getAgeAppropriateHeight($age, $sex)
    {
        // Average heights for Filipino children (in cm)
        $heights = [
            5 => ['Male' => [105, 115], 'Female' => [104, 114]],
            6 => ['Male' => [110, 120], 'Female' => [109, 119]],
            7 => ['Male' => [115, 125], 'Female' => [114, 124]],
            8 => ['Male' => [120, 130], 'Female' => [119, 129]],
            9 => ['Male' => [125, 135], 'Female' => [124, 134]],
            10 => ['Male' => [130, 140], 'Female' => [129, 139]],
            11 => ['Male' => [135, 145], 'Female' => [134, 144]]
        ];
        
        $range = $heights[$age][$sex] ?? [120, 130];
        return rand($range[0], $range[1]) . ' cm';
    }
    
    private function getAgeAppropriateWeight($age, $sex)
    {
        // Average weights for Filipino children (in kg)
        $weights = [
            5 => ['Male' => [16, 22], 'Female' => [15, 21]],
            6 => ['Male' => [18, 25], 'Female' => [17, 24]],
            7 => ['Male' => [20, 28], 'Female' => [19, 27]],
            8 => ['Male' => [22, 32], 'Female' => [21, 31]],
            9 => ['Male' => [25, 36], 'Female' => [24, 35]],
            10 => ['Male' => [28, 40], 'Female' => [27, 39]],
            11 => ['Male' => [31, 45], 'Female' => [30, 44]]
        ];
        
        $range = $weights[$age][$sex] ?? [25, 35];
        return rand($range[0], $range[1]) . ' kg';
    }
    
    private function getBMIStatus($bmi)
    {
        if ($bmi < 16) return 'Severely Underweight';
        if ($bmi < 18.5) return 'Underweight';
        if ($bmi < 25) return 'Normal';
        if ($bmi < 30) return 'Overweight';
        return 'Obese';
    }
    
    private function getHeightStatus($age, $heightCm)
    {
        // Simplified height status based on age-appropriate ranges
        $normalRanges = [
            5 => [104, 115],
            6 => [109, 120],
            7 => [114, 125],
            8 => [119, 130],
            9 => [124, 135],
            10 => [129, 140],
            11 => [134, 145]
        ];
        
        $range = $normalRanges[$age] ?? [120, 135];
        
        if ($heightCm < $range[0] - 5) return 'Short for age';
        if ($heightCm > $range[1] + 5) return 'Tall for age';
        return 'Normal for age';
    }
    
    private function generateRemarks($hasMinorIssues, $examDate, $gradeLevel = null)
    {
        $remarks = [
            'Routine health examination completed',
            'Student appears healthy and active',
            'Regular check-up, no concerns noted',
            'Follow-up recommended in 6 months',
            'Encourage healthy diet and exercise',
            'Good overall health status',
            'Continue current health practices'
        ];
        
        // Add grade-specific remarks
        if ($gradeLevel) {
            $gradeSpecificRemarks = [
                'Kinder 2' => ['Developmental milestones appropriate for age', 'Good adaptation to school environment'],
                'Grade 1' => ['Growth patterns normal for grade level', 'Encourage continued physical activity'],
                'Grade 2' => ['Height and weight within normal range', 'Vision screening shows good results'],
                'Grade 3' => ['Physical development progressing well', 'Dental hygiene education provided'],
                'Grade 4' => ['Pre-adolescent health indicators normal', 'Nutritional counseling discussed'],
                'Grade 5' => ['Growth spurt patterns monitored', 'Health education on body changes'],
                'Grade 6' => ['Pre-teen health assessment complete', 'Transition to adolescence discussed']
            ];
            
            if (isset($gradeSpecificRemarks[$gradeLevel])) {
                $remarks = array_merge($remarks, $gradeSpecificRemarks[$gradeLevel]);
            }
        }
        
        if ($hasMinorIssues) {
            $concernRemarks = [
                'Minor health concerns noted, monitor closely',
                'Recommend follow-up with school nurse',
                'Parent notification sent regarding findings',
                'Schedule re-examination in 3 months',
                'Refer to family physician if symptoms persist'
            ];
            return $concernRemarks[array_rand($concernRemarks)];
        }
        
        return $remarks[array_rand($remarks)];
    }
}
