<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Grade Filtering Debug ===\n";

$studentId = 5;

// Get all treatments for this student
$treatments = \App\Models\HealthTreatment::where('student_id', $studentId)->get();

echo "Student ID: {$studentId}\n";
echo "Total treatments: " . $treatments->count() . "\n\n";

echo "All grade_level values stored:\n";
foreach ($treatments as $treatment) {
    echo "- ID {$treatment->id}: grade_level = '{$treatment->grade_level}' (length: " . strlen($treatment->grade_level ?? '') . ")\n";
}

echo "\n=== Testing Filtering ===\n";

// Test filtering by "4" (what we convert "Grade 4" to)
$filtered4 = \App\Models\HealthTreatment::where('student_id', $studentId)
    ->where('grade_level', '4')
    ->get();
echo "Filtering by '4': " . $filtered4->count() . " results\n";

// Test filtering by "Grade 4" (original format)
$filteredGrade4 = \App\Models\HealthTreatment::where('student_id', $studentId)
    ->where('grade_level', 'Grade 4')
    ->get();
echo "Filtering by 'Grade 4': " . $filteredGrade4->count() . " results\n";

// Test filtering by null/empty
$filteredEmpty = \App\Models\HealthTreatment::where('student_id', $studentId)
    ->whereNull('grade_level')
    ->get();
echo "Filtering by NULL: " . $filteredEmpty->count() . " results\n";

$filteredEmptyString = \App\Models\HealthTreatment::where('student_id', $studentId)
    ->where('grade_level', '')
    ->get();
echo "Filtering by empty string: " . $filteredEmptyString->count() . " results\n";
