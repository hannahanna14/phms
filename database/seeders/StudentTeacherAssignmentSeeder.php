<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\StudentTeacherAssignment;
use App\Models\Student;
use App\Models\User;

class StudentTeacherAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Clear all existing assignments
        StudentTeacherAssignment::truncate();

        // Get all teachers
        $teachers = User::where('role', 'teacher')->get();
        
        if ($teachers->isEmpty()) {
            $this->command->info('No teachers found in database.');
            return;
        }

        // Define grade level and section mapping for teachers
        // Teachers can handle multiple sections across different grade levels
        $gradeTeacherMapping = [
            // Kinder 2 teachers
            ['grade' => 'Kinder 2', 'section' => 'Generous AM', 'username' => 'mariasantos'],
            ['grade' => 'Kinder 2', 'section' => 'Generous PM', 'username' => 'josemiguel'],
            ['grade' => 'Kinder 2', 'section' => 'Good AM', 'username' => 'analuz'],
            ['grade' => 'Kinder 2', 'section' => 'Good PM', 'username' => 'robertocarlos'],
            ['grade' => 'Kinder 2', 'section' => 'SNED – Kindergarten (DHH) (SPED)', 'username' => 'carmenrosa'],

            // Grade 1 teachers
            ['grade' => 'Grade 1', 'section' => 'Admirable', 'username' => 'eduardoramos'],
            ['grade' => 'Grade 1', 'section' => 'Adorable', 'username' => 'luzmarina'],
            ['grade' => 'Grade 1', 'section' => 'Affectionate', 'username' => 'mariasantos'],
            ['grade' => 'Grade 1', 'section' => 'Alert', 'username' => 'josemiguel'],
            ['grade' => 'Grade 1', 'section' => 'Amazing', 'username' => 'analuz'],

            // Grade 2 teachers
            ['grade' => 'Grade 2', 'section' => 'Beloved', 'username' => 'robertocarlos'],
            ['grade' => 'Grade 2', 'section' => 'Beneficent', 'username' => 'carmenrosa'],
            ['grade' => 'Grade 2', 'section' => 'Benevolent', 'username' => 'eduardoramos'],
            ['grade' => 'Grade 2', 'section' => 'Blessed', 'username' => 'luzmarina'],
            ['grade' => 'Grade 2', 'section' => 'Blissful', 'username' => 'mariasantos'],
            ['grade' => 'Grade 2', 'section' => 'Blossom', 'username' => 'josemiguel'],
            ['grade' => 'Grade 2', 'section' => 'SNED – Grade 2 (DHH) (SPED)', 'username' => 'analuz'],

            // Grade 3 teachers
            ['grade' => 'Grade 3', 'section' => 'Calm', 'username' => 'robertocarlos'],
            ['grade' => 'Grade 3', 'section' => 'Candor', 'username' => 'carmenrosa'],
            ['grade' => 'Grade 3', 'section' => 'Charitable', 'username' => 'eduardoramos'],
            ['grade' => 'Grade 3', 'section' => 'Cheerful', 'username' => 'luzmarina'],
            ['grade' => 'Grade 3', 'section' => 'Clever', 'username' => 'mariasantos'],
            ['grade' => 'Grade 3', 'section' => 'Curious', 'username' => 'josemiguel'],

            // Grade 4 teachers
            ['grade' => 'Grade 4', 'section' => 'Dainty', 'username' => 'analuz'],
            ['grade' => 'Grade 4', 'section' => 'Dedicated', 'username' => 'robertocarlos'],
            ['grade' => 'Grade 4', 'section' => 'Demure', 'username' => 'carmenrosa'],
            ['grade' => 'Grade 4', 'section' => 'Devoted', 'username' => 'eduardoramos'],
            ['grade' => 'Grade 4', 'section' => 'Dynamic', 'username' => 'luzmarina'],
            ['grade' => 'Grade 4', 'section' => 'SNED (Graded) (SPED)', 'username' => 'mariasantos'],

            // Grade 5 teachers
            ['grade' => 'Grade 5', 'section' => 'Effective', 'username' => 'josemiguel'],
            ['grade' => 'Grade 5', 'section' => 'Efficient', 'username' => 'analuz'],
            ['grade' => 'Grade 5', 'section' => 'Endurance', 'username' => 'robertocarlos'],
            ['grade' => 'Grade 5', 'section' => 'Energetic', 'username' => 'carmenrosa'],
            ['grade' => 'Grade 5', 'section' => 'Everlasting', 'username' => 'eduardoramos'],

            // Grade 6 teachers
            ['grade' => 'Grade 6', 'section' => 'Fair', 'username' => 'luzmarina'],
            ['grade' => 'Grade 6', 'section' => 'Faithful', 'username' => 'mariasantos'],
            ['grade' => 'Grade 6', 'section' => 'Flexible', 'username' => 'josemiguel'],
            ['grade' => 'Grade 6', 'section' => 'Forbearance', 'username' => 'analuz'],
            ['grade' => 'Grade 6', 'section' => 'Fortitude', 'username' => 'robertocarlos'],
            ['grade' => 'Grade 6', 'section' => 'Friendly', 'username' => 'carmenrosa'],

            // Non-Graded teachers
            ['grade' => 'Non-Graded', 'section' => 'Gracious (SPED)', 'username' => 'eduardoramos'],
            ['grade' => 'Non-Graded', 'section' => 'Grateful (SPED)', 'username' => 'luzmarina']
        ];

        $totalAssignments = 0;

        foreach ($gradeTeacherMapping as $mapping) {
            $gradeLevel = $mapping['grade'];
            $section = $mapping['section'];
            $teacherUsername = $mapping['username'];
            
            // Find the teacher
            $teacher = User::where('username', $teacherUsername)->where('role', 'teacher')->first();
            
            if (!$teacher) {
                $this->command->info("Teacher {$teacherUsername} not found, skipping {$gradeLevel} - Section {$section}");
                continue;
            }

            // Get students for this specific grade level AND section
            $students = Student::where('grade_level', $gradeLevel)
                              ->where('section', $section)
                              ->get();
            
            if ($students->isEmpty()) {
                $this->command->info("No students found for {$gradeLevel} - Section {$section}");
                continue;
            }

            // Assign students of this grade and section to their teacher
            foreach ($students as $student) {
                StudentTeacherAssignment::create([
                    'student_id' => $student->id,
                    'teacher_id' => $teacher->id,
                    'grade_level' => $student->grade_level,
                    'section' => $student->section,
                    'school_year' => $student->school_year ?? '2024-2025'
                ]);
                $totalAssignments++;
            }
            
            $this->command->info("Assigned {$students->count()} {$gradeLevel} - Section {$section} students to {$teacher->full_name}");
        }
        
        $this->command->info("Total assignments created: {$totalAssignments}");
    }
}
