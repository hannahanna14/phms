<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Health Treatments Grade Levels ===\n";
$healthTreatments = \App\Models\HealthTreatment::select('id', 'student_id', 'grade_level')->get();
foreach ($healthTreatments as $treatment) {
    echo "ID: {$treatment->id}, Student: {$treatment->student_id}, Grade: '{$treatment->grade_level}'\n";
}

echo "\n=== Oral Health Treatments Grade Levels ===\n";
$oralTreatments = \App\Models\OralHealthTreatment::select('id', 'student_id', 'grade_level')->get();
foreach ($oralTreatments as $treatment) {
    echo "ID: {$treatment->id}, Student: {$treatment->student_id}, Grade: '{$treatment->grade_level}'\n";
}

echo "\n=== Distinct Grade Levels ===\n";
echo "Health Treatments: " . \App\Models\HealthTreatment::distinct()->pluck('grade_level')->implode(', ') . "\n";
echo "Oral Health Treatments: " . \App\Models\OralHealthTreatment::distinct()->pluck('grade_level')->implode(', ') . "\n";
