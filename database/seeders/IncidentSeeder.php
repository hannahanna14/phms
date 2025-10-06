
<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Incident;
use App\Models\Student;
use Carbon\Carbon;

class IncidentSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Get random students for incidents
        $students = Student::inRandomOrder()->limit(5)->get();
        
        $incidents = [
            [
                'title' => 'Minor cut while playing',
                'description' => 'Student got a small cut on the finger while playing with scissors during art class. Wound was cleaned and bandaged.',
                'incident_type' => 'injury',
                'severity' => 'minor',
                'location' => 'Art Room',
                'action_taken' => 'Cleaned wound with antiseptic, applied bandage, contacted parent',
                'timer_status' => 'completed'
            ],
            [
                'title' => 'Fever during class',
                'description' => 'Student complained of headache and felt warm. Temperature taken was 38.2°C. Parent was contacted to pick up student.',
                'incident_type' => 'illness',
                'severity' => 'moderate',
                'location' => 'Grade 3 Classroom',
                'action_taken' => 'Temperature monitored, student rested in clinic, parent contacted',
                'timer_status' => 'completed'
            ],
            [
                'title' => 'Dental pain complaint',
                'description' => 'Student complained of severe toothache during lunch break. Unable to eat properly.',
                'incident_type' => 'dental',
                'severity' => 'moderate',
                'location' => 'Cafeteria',
                'action_taken' => 'Provided pain relief, advised parent to see dentist',
                'timer_status' => 'active'
            ],
            [
                'title' => 'Allergic reaction to food',
                'description' => 'Student developed rashes and itching after eating peanuts during snack time. Known allergy to nuts.',
                'incident_type' => 'allergy',
                'severity' => 'moderate',
                'location' => 'Classroom',
                'action_taken' => 'Administered antihistamine, monitored breathing, contacted parent',
                'timer_status' => 'completed'
            ],
            [
                'title' => 'Fall from playground equipment',
                'description' => 'Student slipped and fell from monkey bars during recess. Complained of pain in left wrist.',
                'incident_type' => 'injury',
                'severity' => 'moderate',
                'location' => 'Playground',
                'action_taken' => 'Applied ice pack, immobilized wrist, parent contacted for medical check-up',
                'timer_status' => 'in_progress'
            ]
        ];

        foreach ($incidents as $index => $incidentData) {
            $student = $students[$index];
            
            Incident::create([
                'student_id' => $student->id,
                'title' => $incidentData['title'],
                'description' => $incidentData['description'],
                'incident_type' => $incidentData['incident_type'],
                'severity' => $incidentData['severity'],
                'location' => $incidentData['location'],
                'date_time' => Carbon::now()->subDays(rand(1, 30))->subHours(rand(1, 8)),
                'reported_by' => 'School Nurse',
                'action_taken' => $incidentData['action_taken'],
                'follow_up_required' => in_array($incidentData['timer_status'], ['active', 'in_progress']),
                'timer_status' => $incidentData['timer_status'],
                'grade_level' => $student->grade_level,
                'school_year' => $student->school_year,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $this->command->info('Created 5 realistic incident reports');
    }
}
