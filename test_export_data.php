<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get a sample health examination record with student
$exam = \App\Models\HealthExamination::with('student')->first();

if ($exam) {
    echo "=== SAMPLE HEALTH EXAMINATION DATA ===\n\n";
    
    echo "Student Info:\n";
    echo "  Name: " . ($exam->student->full_name ?? 'NULL') . "\n";
    echo "  LRN: " . ($exam->student->lrn ?? 'NULL') . "\n";
    echo "  Grade: " . ($exam->student->grade_level ?? 'NULL') . "\n";
    echo "  Section: " . ($exam->student->section ?? 'NULL') . "\n\n";
    
    echo "Examination Data:\n";
    echo "  Examination Date: " . ($exam->examination_date ? $exam->examination_date->format('Y-m-d') : 'NULL') . "\n";
    echo "  School Year: " . ($exam->school_year ?? 'NULL') . "\n";
    echo "  Weight: " . ($exam->weight ?? 'NULL') . "\n";
    echo "  Height: " . ($exam->height ?? 'NULL') . "\n";
    echo "  Nutritional Status BMI: " . ($exam->nutritional_status_bmi ?? 'NULL') . "\n";
    echo "  Nutritional Status Height: " . ($exam->nutritional_status_height ?? 'NULL') . "\n";
    echo "  Temperature: " . ($exam->temperature ?? 'NULL') . "\n";
    echo "  Heart Rate: " . ($exam->heart_rate ?? 'NULL') . "\n";
    echo "  Vision Screening: " . ($exam->vision_screening ?? 'NULL') . "\n";
    echo "  Vision Screening Specify: " . ($exam->vision_screening_specify ?? 'NULL') . "\n";
    echo "  Skin: " . ($exam->skin ?? 'NULL') . "\n";
    echo "  Eye: " . ($exam->eye ?? 'NULL') . "\n";
    echo "  Ear: " . ($exam->ear ?? 'NULL') . "\n";
    echo "  Lungs: " . ($exam->lungs ?? 'NULL') . "\n";
    echo "  Heart: " . ($exam->heart ?? 'NULL') . "\n";
    echo "  Deworming Status: " . ($exam->deworming_status ?? 'NULL') . "\n";
    echo "  Iron Supplementation: " . ($exam->iron_supplementation ?? 'NULL') . "\n";
    echo "  SBFP Beneficiary: " . ($exam->sbfp_beneficiary ? 'Yes' : 'No') . "\n";
    echo "  4Ps Beneficiary: " . ($exam->four_ps_beneficiary ? 'Yes' : 'No') . "\n";
    
    echo "\n=== ALL ATTRIBUTES ===\n";
    foreach ($exam->getAttributes() as $key => $value) {
        if (!is_null($value) && $value !== '') {
            echo "  $key: $value\n";
        }
    }
    
    echo "\n=== TOTAL RECORDS ===\n";
    echo "Total Health Examinations: " . \App\Models\HealthExamination::count() . "\n";
    
} else {
    echo "No health examination records found in database.\n";
}
