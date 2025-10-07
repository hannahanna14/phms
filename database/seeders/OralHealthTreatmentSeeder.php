<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\OralHealthTreatment;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OralHealthTreatmentSeeder extends Seeder
{
    public function run()
    {
        // Truncate oral health treatments table
        DB::table('oral_health_treatments')->truncate();

        $students = Student::all();
        
        // Dental treatment categories and related data
        $treatmentTitles = [
            'Dental Cleaning',
            'Cavity Filling',
            'Tooth Extraction',
            'Fluoride Treatment',
            'Dental Sealant Application',
            'Orthodontic Consultation',
            'Gum Treatment',
            'Root Canal Treatment',
            'Dental Crown Placement',
            'Preventive Dental Care',
            'Emergency Dental Care',
            'Dental Health Education',
            'Oral Hygiene Instruction',
            'Tooth Restoration',
            'Dental Check-up'
        ];
        
        $chiefComplaints = [
            'Student complains of toothache',
            'Visible cavity on molar tooth',
            'Gum bleeding during brushing',
            'Tooth sensitivity to cold',
            'Broken tooth from accident',
            'Routine dental check-up',
            'Bad breath complaint',
            'Loose tooth needs attention',
            'Dental pain during eating',
            'Swollen gums observed',
            'Tooth discoloration noted',
            'Difficulty chewing food',
            'Dental trauma from sports',
            'Preventive care visit',
            'Follow-up dental treatment'
        ];
        
        $treatments = [
            'Professional dental cleaning performed, plaque removal completed',
            'Composite filling applied to decayed tooth, patient comfortable',
            'Tooth extraction completed under local anesthesia, post-care instructions given',
            'Fluoride varnish applied to all teeth for cavity prevention',
            'Dental sealants placed on posterior teeth, excellent protection achieved',
            'Orthodontic evaluation completed, referral to specialist recommended',
            'Gum scaling and root planing performed, oral hygiene reinforced',
            'Root canal therapy initiated, temporary filling placed',
            'Dental crown preparation completed, impression taken for lab',
            'Comprehensive oral examination performed, no issues found',
            'Emergency dental care provided, pain relief achieved',
            'Dental health education session conducted, proper brushing demonstrated',
            'Oral hygiene instruction provided, flossing technique taught',
            'Tooth restoration completed with composite material',
            'Routine dental check-up completed, next visit scheduled'
        ];
        
        $statuses = ['pending', 'in_progress', 'completed', 'cancelled'];
        $timerStatuses = ['not_started', 'active', 'paused', 'completed', 'expired'];
        
        $remarks = [
            'Patient cooperative during treatment',
            'Excellent oral hygiene maintained',
            'Follow-up appointment scheduled',
            'Parent education provided',
            'Treatment completed successfully',
            'Monitor healing progress',
            'Recommend regular dental visits',
            'Good treatment compliance',
            'No complications observed',
            'Continue preventive care'
        ];

        // All grade levels from Kinder 2 to Grade 6
        $allGradeLevels = ['Kinder 2', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6'];
        $currentYear = date('Y');
        
        foreach ($students as $student) {
            // Create oral health treatments for each grade level (complete dental journey)
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
                    $title = $this->getAgeAppropriateDentalTitle($treatmentTitles, $baseAge);
                    $complaint = $this->getAgeAppropriateDentalComplaint($chiefComplaints, $baseAge);
                    $treatment = $this->getAgeAppropriateDentalTreatment($treatments, $baseAge);
                    $status = $statuses[array_rand($statuses)];
                    $timerStatus = $timerStatuses[array_rand($timerStatuses)];
                    $remark = $this->getAgeAppropriateDentalRemark($remarks, $baseAge, $gradeLevel);
                    
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
                    
                    OralHealthTreatment::create([
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
        
        $this->command->info('Created comprehensive oral health treatment records for all 100 students across all grade levels');
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
    
    private function getAgeAppropriateDentalTitle($titles, $age)
    {
        // Younger children more likely to have basic dental care
        $youngerTitles = [
            'Dental Cleaning',
            'Fluoride Treatment',
            'Dental Health Education',
            'Preventive Dental Care',
            'Oral Hygiene Instruction'
        ];
        
        $olderTitles = [
            'Cavity Filling',
            'Tooth Extraction',
            'Orthodontic Consultation',
            'Dental Sealant Application',
            'Root Canal Treatment'
        ];
        
        if ($age <= 7) {
            return rand(1, 100) <= 70 ? $youngerTitles[array_rand($youngerTitles)] : $titles[array_rand($titles)];
        } else {
            return rand(1, 100) <= 60 ? $olderTitles[array_rand($olderTitles)] : $titles[array_rand($titles)];
        }
    }
    
    private function getAgeAppropriateDentalComplaint($complaints, $age)
    {
        $youngerComplaints = [
            'Routine dental check-up',
            'Loose tooth needs attention',
            'Tooth sensitivity to cold',
            'Bad breath complaint',
            'Preventive care visit'
        ];
        
        $olderComplaints = [
            'Student complains of toothache',
            'Visible cavity on molar tooth',
            'Gum bleeding during brushing',
            'Broken tooth from accident',
            'Difficulty chewing food'
        ];
        
        if ($age <= 7) {
            return rand(1, 100) <= 70 ? $youngerComplaints[array_rand($youngerComplaints)] : $complaints[array_rand($complaints)];
        } else {
            return rand(1, 100) <= 60 ? $olderComplaints[array_rand($olderComplaints)] : $complaints[array_rand($complaints)];
        }
    }
    
    private function getAgeAppropriateDentalTreatment($treatments, $age)
    {
        $youngerTreatments = [
            'Professional dental cleaning performed, plaque removal completed',
            'Fluoride varnish applied to all teeth for cavity prevention',
            'Dental health education session conducted, proper brushing demonstrated',
            'Comprehensive oral examination performed, no issues found',
            'Oral hygiene instruction provided, flossing technique taught'
        ];
        
        $olderTreatments = [
            'Composite filling applied to decayed tooth, patient comfortable',
            'Tooth extraction completed under local anesthesia, post-care instructions given',
            'Orthodontic evaluation completed, referral to specialist recommended',
            'Dental sealants placed on posterior teeth, excellent protection achieved',
            'Root canal therapy initiated, temporary filling placed'
        ];
        
        if ($age <= 7) {
            return rand(1, 100) <= 70 ? $youngerTreatments[array_rand($youngerTreatments)] : $treatments[array_rand($treatments)];
        } else {
            return rand(1, 100) <= 60 ? $olderTreatments[array_rand($olderTreatments)] : $treatments[array_rand($treatments)];
        }
    }
    
    private function getAgeAppropriateDentalRemark($remarks, $age, $gradeLevel)
    {
        $gradeSpecificRemarks = [
            'Kinder 2' => [
                'First dental visit experience positive',
                'Parent present during treatment',
                'Child cooperative with simple procedures'
            ],
            'Grade 1' => [
                'Losing baby teeth naturally',
                'Good oral hygiene habits developing',
                'Fluoride treatment well tolerated'
            ],
            'Grade 2' => [
                'Mixed dentition phase monitored',
                'Sealants recommended for new molars',
                'Excellent brushing technique demonstrated'
            ],
            'Grade 3' => [
                'Permanent teeth eruption monitored',
                'Orthodontic screening completed',
                'Cavity prevention education reinforced'
            ],
            'Grade 4' => [
                'Pre-teen dental care discussed',
                'Dietary counseling for dental health',
                'Independent oral care encouraged'
            ],
            'Grade 5' => [
                'Adolescent dental changes explained',
                'Orthodontic treatment planning initiated',
                'Advanced oral hygiene techniques taught'
            ],
            'Grade 6' => [
                'Transition to adult dental care discussed',
                'Comprehensive treatment planning completed',
                'Excellent dental health maintenance'
            ]
        ];
        
        $baseRemarks = $remarks;
        
        if (isset($gradeSpecificRemarks[$gradeLevel])) {
            $baseRemarks = array_merge($baseRemarks, $gradeSpecificRemarks[$gradeLevel]);
        }
        
        return $baseRemarks[array_rand($baseRemarks)];
    }
}
