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

        // Define grade level mapping for teachers
        $gradeTeacherMapping = [
            'Kinder 2' => 'mariasantos',
            'Grade 1' => 'josemiguel', 
            'Grade 2' => 'analuz',
            'Grade 3' => 'robertocarlos',
            'Grade 4' => 'carmenrosa',
            'Grade 5' => 'eduardoramos',
            'Grade 6' => 'luzmarina'
        ];

        $totalAssignments = 0;

        foreach ($gradeTeacherMapping as $gradeLevel => $teacherUsername) {
            // Find the teacher
            $teacher = User::where('username', $teacherUsername)->where('role', 'teacher')->first();
            
            if (!$teacher) {
                $this->command->info("Teacher {$teacherUsername} not found, skipping {$gradeLevel}");
                continue;
            }

            // Get all students for this grade level
            $students = Student::where('grade_level', $gradeLevel)->get();
            
            if ($students->isEmpty()) {
                $this->command->info("No students found for {$gradeLevel}");
                continue;
            }

            // Assign all students of this grade to their teacher
            foreach ($students as $student) {
                StudentTeacherAssignment::create([
                    'student_id' => $student->id,
                    'teacher_id' => $teacher->id,
                    'grade_level' => $student->grade_level,
                    'section' => $student->section ?? 'A',
                    'school_year' => $student->school_year ?? '2024-2025'
                ]);
                $totalAssignments++;
            }
            
            $this->command->info("Assigned {$students->count()} {$gradeLevel} students to {$teacher->full_name}");
        }
        
        $this->command->info("Total assignments created: {$totalAssignments}");
    }
}
