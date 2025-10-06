<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\HealthExamination;

echo "=== Health Examinations Individual Fields Check ===\n";
echo "Total records: " . HealthExamination::count() . "\n\n";

// Check for records with actual examination data
$recordsWithData = HealthExamination::where(function($query) {
    $query->whereNotNull('skin')
          ->orWhereNotNull('scalp')
          ->orWhereNotNull('eye')
          ->orWhereNotNull('ear')
          ->orWhereNotNull('nose')
          ->orWhereNotNull('mouth')
          ->orWhereNotNull('throat')
          ->orWhereNotNull('neck')
          ->orWhereNotNull('lungs')
          ->orWhereNotNull('heart')
          ->orWhereNotNull('lungs_heart')
          ->orWhereNotNull('abdomen')
          ->orWhereNotNull('deformities');
})->get();

echo "Records with examination data: " . $recordsWithData->count() . "\n\n";

if ($recordsWithData->count() > 0) {
    foreach ($recordsWithData as $record) {
        echo "Record ID: {$record->id}, Student ID: {$record->student_id}\n";
        echo "  skin: " . ($record->skin ?? 'NULL') . "\n";
        echo "  scalp: " . ($record->scalp ?? 'NULL') . "\n";
        echo "  eye: " . ($record->eye ?? 'NULL') . "\n";
        echo "  ear: " . ($record->ear ?? 'NULL') . "\n";
        echo "  nose: " . ($record->nose ?? 'NULL') . "\n";
        echo "  mouth: " . ($record->mouth ?? 'NULL') . "\n";
        echo "  throat: " . ($record->throat ?? 'NULL') . "\n";
        echo "  neck: " . ($record->neck ?? 'NULL') . "\n";
        echo "  lungs: " . ($record->lungs ?? 'NULL') . "\n";
        echo "  heart: " . ($record->heart ?? 'NULL') . "\n";
        echo "  lungs_heart: " . ($record->lungs_heart ?? 'NULL') . "\n";
        echo "  abdomen: " . ($record->abdomen ?? 'NULL') . "\n";
        echo "  deformities: " . ($record->deformities ?? 'NULL') . "\n";
        echo "---\n";
    }
} else {
    echo "No records found with examination data.\n";
    echo "Let's check what data exists:\n\n";
    
    $allRecords = HealthExamination::take(3)->get();
    foreach ($allRecords as $record) {
        echo "Record ID: {$record->id}\n";
        echo "  All examination fields are NULL\n";
        echo "  Created: {$record->created_at}\n";
        echo "---\n";
    }
}

// Check specific field counts
echo "\nField-specific counts:\n";
echo "skin normal: " . HealthExamination::where('skin', 'normal')->count() . "\n";
echo "skin abnormal: " . HealthExamination::where('skin', 'abnormal')->count() . "\n";
echo "scalp normal: " . HealthExamination::where('scalp', 'normal')->count() . "\n";
echo "scalp abnormal: " . HealthExamination::where('scalp', 'abnormal')->count() . "\n";
echo "eye normal: " . HealthExamination::where('eye', 'normal')->count() . "\n";
echo "eye abnormal: " . HealthExamination::where('eye', 'abnormal')->count() . "\n";
echo "lungs normal: " . HealthExamination::where('lungs', 'normal')->count() . "\n";
echo "lungs abnormal: " . HealthExamination::where('lungs', 'abnormal')->count() . "\n";
echo "heart normal: " . HealthExamination::where('heart', 'normal')->count() . "\n";
echo "heart abnormal: " . HealthExamination::where('heart', 'abnormal')->count() . "\n";
