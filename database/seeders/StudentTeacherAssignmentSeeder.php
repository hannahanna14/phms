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
        // Get the teacher user
        $teacher = User::where('username', 'teacher')->first();
        
        if (!$teacher) {
            $this->command->info('Teacher user not found. Please create teacher user first.');
            return;
        }

        // Clear existing assignments for this teacher
        StudentTeacherAssignment::where('teacher_id', $teacher->id)->delete();

        // Get first 2 students to assign to the teacher
        $students = Student::take(2)->get();
        
        if ($students->isEmpty()) {
            $this->command->info('No students found in database.');
            return;
        }
        
        foreach ($students as $student) {
            StudentTeacherAssignment::create([
                'student_id' => $student->id,
                'teacher_id' => $teacher->id,
                'grade_level' => $student->grade_level,
                'section' => $student->section,
                'school_year' => $student->school_year ?? '2024-2025'
            ]);
        }
        
        $this->command->info('Assigned ' . $students->count() . ' students to teacher: ' . $students->pluck('full_name')->join(', '));
    }
}
