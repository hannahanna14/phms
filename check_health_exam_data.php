<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

use App\Models\Student;
use App\Models\HealthExamination;

try {
    echo "Checking health examination data...\n";
    
    // Find John Smith or any student with health examination data
    $students = Student::whereHas('healthExaminations')->take(3)->get();
    
    foreach ($students as $student) {
        echo "\n=== Student: {$student->full_name} ===\n";
        echo "Student Grade Level: {$student->grade_level}\n";
        
        $exams = HealthExamination::where('student_id', $student->id)->get();
        
        foreach ($exams as $exam) {
            echo "Exam ID: {$exam->id} | Grade Level: '{$exam->grade_level}' | Date: {$exam->examination_date}\n";
        }
    }
    
    // Check all unique grade levels in health examinations
    echo "\n=== All Grade Levels in Health Examinations ===\n";
    $gradeLevels = HealthExamination::distinct('grade_level')->pluck('grade_level');
    foreach ($gradeLevels as $grade) {
        echo "Grade: '{$grade}'\n";
    }
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
