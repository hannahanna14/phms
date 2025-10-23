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
        // Each teacher handles ONE grade level and ONE section
        $gradeTeacherMapping = [
            ['grade' => 'Kinder 2', 'section' => 'A', 'username' => 'mariasantos'],
            ['grade' => 'Grade 1', 'section' => 'A', 'username' => 'josemiguel'],
            ['grade' => 'Grade 2', 'section' => 'B', 'username' => 'analuz'],
            ['grade' => 'Grade 3', 'section' => 'A', 'username' => 'robertocarlos'],
            ['grade' => 'Grade 4', 'section' => 'B', 'username' => 'carmenrosa'],
            ['grade' => 'Grade 5', 'section' => 'C', 'username' => 'eduardoramos'],
            ['grade' => 'Grade 6', 'section' => 'A', 'username' => 'luzmarina']
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
