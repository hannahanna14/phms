<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use App\Models\Student;
use App\Models\StudentTeacherAssignment;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Find teacher
    $teacher = User::where('username', 'teacher')->first();
    
    if (!$teacher) {
        echo "Teacher not found!\n";
        exit(1);
    }
    
    echo "Teacher found: " . $teacher->full_name . "\n";
    
    // Clear existing assignments
    StudentTeacherAssignment::where('teacher_id', $teacher->id)->delete();
    echo "Cleared existing assignments\n";
    
    // Get first 2 students
    $students = Student::take(2)->get();
    
    foreach ($students as $student) {
        $assignment = new StudentTeacherAssignment();
        $assignment->student_id = $student->id;
        $assignment->teacher_id = $teacher->id;
        $assignment->grade_level = $student->grade_level;
        $assignment->section = $student->section;
        $assignment->school_year = $student->school_year ?? '2024-2025';
        $assignment->save();
        
        echo "Assigned: " . $student->full_name . " (ID: " . $student->id . ") to teacher\n";
    }
    
    echo "Assignment complete!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
