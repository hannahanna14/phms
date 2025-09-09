<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Health Treatments Debug - All Grade Levels ===\n";

// Get student ID 5 (from the URL in the image)
$studentId = 5;

echo "Student ID: {$studentId}\n";

// Get all treatments for this student
$treatments = \App\Models\HealthTreatment::where('student_id', $studentId)->orderBy('created_at', 'desc')->get();

echo "Total treatments: " . $treatments->count() . "\n\n";

foreach ($treatments as $treatment) {
    echo "Treatment ID: {$treatment->id}\n";
    echo "Grade Level: '{$treatment->grade_level}' (length: " . strlen($treatment->grade_level ?? '') . ")\n";
    echo "Chief Complaint: {$treatment->chief_complaint}\n";
    echo "Treatment: {$treatment->treatment}\n";
    echo "Date: {$treatment->date}\n";
    echo "Created: {$treatment->created_at}\n";
    echo "---\n";
}

// Test filtering by different grades
echo "\n=== Testing Grade Filtering ===\n";

foreach (['4', '5', '6'] as $grade) {
    $gradeResults = \App\Models\HealthTreatment::where('student_id', $studentId)
        ->where('grade_level', $grade)
        ->get();
    echo "Grade {$grade} treatments: " . $gradeResults->count() . "\n";
}

// Check for null/empty grades
$nullGrades = \App\Models\HealthTreatment::where('student_id', $studentId)
    ->whereNull('grade_level')
    ->get();
echo "NULL grade treatments: " . $nullGrades->count() . "\n";

$emptyGrades = \App\Models\HealthTreatment::where('student_id', $studentId)
    ->where('grade_level', '')
    ->get();
echo "Empty string grade treatments: " . $emptyGrades->count() . "\n";
