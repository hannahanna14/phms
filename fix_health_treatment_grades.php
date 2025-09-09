<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Fixing Health Treatment Grade Levels ===\n";

// Get all health treatments with empty grade_level
$emptyGradeTreatments = \App\Models\HealthTreatment::whereNull('grade_level')
    ->orWhere('grade_level', '')
    ->get();

echo "Found {$emptyGradeTreatments->count()} treatments with empty grade levels\n";

foreach ($emptyGradeTreatments as $treatment) {
    // Get the student's current grade level
    $student = \App\Models\Student::find($treatment->student_id);
    if ($student) {
        $gradeLevel = $student->grade_level;
        
        // Update the treatment record
        $treatment->update([
            'grade_level' => $gradeLevel,
            'school_year' => $student->school_year ?? '2024-2025'
        ]);
        
        echo "Updated treatment ID {$treatment->id} for student {$student->full_name}: grade_level = '{$gradeLevel}'\n";
    }
}

echo "\n=== Updated Grade Levels ===\n";
$healthTreatments = \App\Models\HealthTreatment::select('id', 'student_id', 'grade_level')->get();
foreach ($healthTreatments as $treatment) {
    echo "ID: {$treatment->id}, Student: {$treatment->student_id}, Grade: '{$treatment->grade_level}'\n";
}
