<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\HealthTreatment;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HealthTreatmentSeeder extends Seeder
{
    public function run()
    {
        // Truncate health treatments table
        DB::table('health_treatments')->truncate();

        $students = Student::all();
        
        // Treatment categories and related data
        $treatmentTitles = [
            'Routine Health Check',
            'Fever Management',
            'Wound Care',
            'Headache Treatment',
            'Stomach Ache Care',
            'Minor Injury Treatment',
            'Skin Condition Care',
            'Eye Irritation Treatment',
            'Ear Cleaning',
            'Nutritional Counseling',
            'Vaccination Follow-up',
            'Growth Monitoring',
            'Vision Screening Follow-up',
            'Dental Health Education',
            'Hygiene Education'
        ];
        
        $chiefComplaints = [
            'Student complains of headache during class',
            'Fever of 37.8°C reported by teacher',
            'Minor cut on finger from playground accident',
            'Stomach pain after lunch',
            'Dizziness during PE class',
            'Skin rash on arms',
            'Red, itchy eyes',
            'Earache and discomfort',
            'Nausea and vomiting',
            'Fatigue and weakness',
            'Sore throat and cough',
            'Bruise from fall',
            'Allergic reaction to food',
            'Difficulty seeing blackboard',
            'Toothache complaint'
        ];
        
        $treatments = [
            'Administered paracetamol 250mg, rest in clinic for 30 minutes',
            'Applied antiseptic and bandage to wound, tetanus shot given',
            'Provided oral rehydration solution, monitored for 1 hour',
            'Applied cold compress, advised rest and hydration',
            'Cleaned affected area with saline solution, applied topical cream',
            'Administered first aid, documented incident, parent contacted',
            'Provided eye drops, advised to avoid rubbing eyes',
            'Ear irrigation performed, antibiotic drops prescribed',
            'Nutritional counseling provided, meal plan discussed',
            'Vision test conducted, referral to optometrist recommended',
            'Dental hygiene demonstration, toothbrush and paste provided',
            'Health education session on proper handwashing',
            'Blood pressure and vital signs monitoring',
            'Allergy management plan discussed with student',
            'Growth measurements taken and recorded'
        ];
        
        $statuses = ['pending', 'in_progress', 'completed', 'cancelled'];
        $timerStatuses = ['not_started', 'active', 'paused', 'completed', 'expired'];
        
        $remarks = [
            'Student responded well to treatment',
            'Follow-up required in 3 days',
            'Parent notification sent home',
            'Referred to family physician',
            'Complete recovery observed',
            'Monitor for recurring symptoms',
            'Health education provided to student',
            'Preventive measures discussed',
            'No adverse reactions noted',
            'Treatment completed successfully'
        ];

        // All grade levels from Kinder 2 to Grade 6
        $allGradeLevels = ['Kinder 2', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6'];
        $currentYear = date('Y');
        
        foreach ($students as $student) {
            // Create health treatments for each grade level (complete school journey)
            foreach ($allGradeLevels as $gradeIndex => $gradeLevel) {
                // Calculate school year for this grade (going backwards from current)
                $yearsBack = count($allGradeLevels) - 1 - $gradeIndex;
                $schoolYearStart = $currentYear - 1 - $yearsBack;
                $schoolYear = $schoolYearStart . '-' . ($schoolYearStart + 1);
                
                // Create 0-3 treatments per grade level (some grades may have no treatments)
                $numTreatments = rand(0, 3);
                
                for ($i = 0; $i < $numTreatments; $i++) {
                    // Generate treatment date within the school year
                    $schoolYearStartDate = Carbon::createFromDate($schoolYearStart, 6, 1); // June start
                    $schoolYearEndDate = Carbon::createFromDate($schoolYearStart + 1, 5, 31); // May end
                    $treatmentDate = Carbon::createFromTimestamp(rand($schoolYearStartDate->timestamp, $schoolYearEndDate->timestamp));
                    
                    // Select age-appropriate treatment data
                    $baseAge = $this->getBaseAge($gradeLevel);
                    $title = $this->getAgeAppropriateTitle($treatmentTitles, $baseAge);
                    $complaint = $this->getAgeAppropriateComplaint($chiefComplaints, $baseAge);
                    $treatment = $this->getAgeAppropriateTreatment($treatments, $baseAge);
                    $status = $statuses[array_rand($statuses)];
                    $timerStatus = $timerStatuses[array_rand($timerStatuses)];
                    $remark = $this->getAgeAppropriateRemark($remarks, $baseAge, $gradeLevel);
                    
                    // Generate timer data based on status
                    $startedAt = null;
                    $expiresAt = null;
                    $isExpired = false;
                    
                    if (in_array($timerStatus, ['active', 'paused', 'completed', 'expired'])) {
                        $startedAt = $treatmentDate->copy()->addMinutes(rand(5, 30));
                        $expiresAt = $startedAt->copy()->addHours(2); // Standard 2-hour timer
                        
                        if ($timerStatus === 'expired') {
                            $isExpired = true;
                            $status = 'completed';
                        } elseif ($timerStatus === 'completed') {
                            $isExpired = false;
                            $status = 'completed';
                        }
                    }
                    
                    HealthTreatment::create([
                        'student_id' => $student->id,
                        'date' => $treatmentDate,
                        'title' => $title,
                        'chief_complaint' => $complaint,
                        'treatment' => $treatment,
                        'status' => $status,
                        'remarks' => $remark,
                        'grade_level' => $gradeLevel,
                        'school_year' => $schoolYear,
                        'started_at' => $startedAt,
                        'expires_at' => $expiresAt,
                        'is_expired' => $isExpired,
                        'timer_status' => $timerStatus
                    ]);
                }
            }
        }
        
        $this->command->info('Created comprehensive health treatment records for all 100 students across all grade levels');
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
    
    private function getAgeAppropriateTitle($titles, $age)
    {
        // Younger children more likely to have basic health issues
        $youngerTitles = [
            'Routine Health Check',
            'Minor Injury Treatment',
            'Fever Management',
            'Stomach Ache Care',
            'Hygiene Education'
        ];
        
        $olderTitles = [
            'Growth Monitoring',
            'Vision Screening Follow-up',
            'Nutritional Counseling',
            'Dental Health Education',
            'Vaccination Follow-up'
        ];
        
        if ($age <= 7) {
            return rand(1, 100) <= 70 ? $youngerTitles[array_rand($youngerTitles)] : $titles[array_rand($titles)];
        } else {
            return rand(1, 100) <= 60 ? $olderTitles[array_rand($olderTitles)] : $titles[array_rand($titles)];
        }
    }
    
    private function getAgeAppropriateComplaint($complaints, $age)
    {
        $youngerComplaints = [
            'Student complains of stomach pain after lunch',
            'Minor cut on finger from playground accident',
            'Fever reported by teacher',
            'Student feels dizzy during play time',
            'Bruise from fall during recess'
        ];
        
        $olderComplaints = [
            'Headache during class activities',
            'Difficulty seeing blackboard clearly',
            'Fatigue during physical education',
            'Skin rash on arms and legs',
            'Toothache complaint during class'
        ];
        
        if ($age <= 7) {
            return rand(1, 100) <= 70 ? $youngerComplaints[array_rand($youngerComplaints)] : $complaints[array_rand($complaints)];
        } else {
            return rand(1, 100) <= 60 ? $olderComplaints[array_rand($olderComplaints)] : $complaints[array_rand($complaints)];
        }
    }
    
    private function getAgeAppropriateTreatment($treatments, $age)
    {
        $youngerTreatments = [
            'Applied antiseptic and bandage to wound, comfort provided',
            'Administered age-appropriate paracetamol, rest in clinic',
            'Provided oral rehydration solution, monitored closely',
            'Applied cold compress, contacted parent for pickup',
            'Basic first aid administered, incident documented'
        ];
        
        $olderTreatments = [
            'Comprehensive health assessment conducted',
            'Vision screening performed, referral recommended',
            'Nutritional counseling session provided',
            'Growth measurements taken and analyzed',
            'Health education on adolescent changes'
        ];
        
        if ($age <= 7) {
            return rand(1, 100) <= 70 ? $youngerTreatments[array_rand($youngerTreatments)] : $treatments[array_rand($treatments)];
        } else {
            return rand(1, 100) <= 60 ? $olderTreatments[array_rand($olderTreatments)] : $treatments[array_rand($treatments)];
        }
    }
    
    private function getAgeAppropriateRemark($remarks, $age, $gradeLevel)
    {
        $gradeSpecificRemarks = [
            'Kinder 2' => [
                'Parent contacted for early pickup',
                'Comfort measures effective for young child',
                'Simple explanation provided to student'
            ],
            'Grade 1' => [
                'Student responded well to treatment',
                'Basic health education provided',
                'Encouraged to drink more water'
            ],
            'Grade 2' => [
                'Follow-up scheduled with school nurse',
                'Preventive measures discussed',
                'Good cooperation during treatment'
            ],
            'Grade 3' => [
                'Health habits reinforcement provided',
                'Student educated on symptom recognition',
                'Excellent recovery progress noted'
            ],
            'Grade 4' => [
                'Pre-adolescent health concerns addressed',
                'Nutritional guidance provided to student',
                'Growth patterns discussed with student'
            ],
            'Grade 5' => [
                'Adolescent health education initiated',
                'Body changes discussion appropriate for age',
                'Self-care practices reinforced'
            ],
            'Grade 6' => [
                'Transition to adolescence health topics covered',
                'Student demonstrates good health awareness',
                'Preparation for secondary school health needs'
            ]
        ];
        
        $baseRemarks = $remarks;
        
        if (isset($gradeSpecificRemarks[$gradeLevel])) {
            $baseRemarks = array_merge($baseRemarks, $gradeSpecificRemarks[$gradeLevel]);
        }
        
        return $baseRemarks[array_rand($baseRemarks)];
    }
}
