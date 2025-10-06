<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\HealthExamination;

echo "=== Testing Flexible Queries ===\n";

// Test the new flexible queries
$skinNormal = HealthExamination::whereIn('skin', ['Normal', 'normal', 'Clear', 'clear', 'Healthy', 'healthy'])->count();
$skinAbnormal = HealthExamination::whereNotNull('skin')->whereNotIn('skin', ['Normal', 'normal', 'Clear', 'clear', 'Healthy', 'healthy'])->count();

$scalpNormal = HealthExamination::whereIn('scalp', ['Normal', 'normal', 'Clear', 'clear', 'Healthy', 'healthy'])->count();
$scalpAbnormal = HealthExamination::whereNotNull('scalp')->whereNotIn('scalp', ['Normal', 'normal', 'Clear', 'clear', 'Healthy', 'healthy'])->count();

$eyesNormal = HealthExamination::whereIn('eye', ['Normal', 'normal'])->count();
$eyesAbnormal = HealthExamination::whereNotNull('eye')->whereNotIn('eye', ['Normal', 'normal'])->count();

$mouthNormal = HealthExamination::whereIn('mouth', ['Normal', 'normal', 'Healthy', 'healthy'])->count();
$mouthAbnormal = HealthExamination::whereNotNull('mouth')->whereNotIn('mouth', ['Normal', 'normal', 'Healthy', 'healthy'])->count();

$throatNormal = HealthExamination::whereIn('throat', ['Normal', 'normal', 'Clear', 'clear'])->count();
$throatAbnormal = HealthExamination::whereNotNull('throat')->whereNotIn('throat', ['Normal', 'normal', 'Clear', 'clear'])->count();

$abdomenNormal = HealthExamination::whereIn('abdomen', ['Normal', 'normal', 'Soft', 'soft'])->count();
$abdomenDistended = HealthExamination::whereIn('abdomen', ['Distended', 'distended'])->count();
$abdomenAbnormal = HealthExamination::whereNotNull('abdomen')->whereNotIn('abdomen', ['Normal', 'normal', 'Soft', 'soft', 'Distended', 'distended'])->count();

$deformitiesNone = HealthExamination::whereIn('deformities', ['None', 'none', 'Normal', 'normal'])->count();
$deformitiesCongenital = HealthExamination::whereIn('deformities', ['Congenital', 'congenital'])->count();
$deformitiesAcquired = HealthExamination::whereIn('deformities', ['Acquired', 'acquired'])->count();

// Test lungs/heart with fallback
$lungsNormal = HealthExamination::where(function($q) {
    $q->whereIn('lungs', ['Normal', 'normal'])
      ->orWhere(function($subq) {
          $subq->whereNull('lungs')->whereIn('lungs_heart', ['Normal', 'normal']);
      });
})->count();

$heartNormal = HealthExamination::where(function($q) {
    $q->whereIn('heart', ['Normal', 'normal'])
      ->orWhere(function($subq) {
          $subq->whereNull('heart')->whereIn('lungs_heart', ['Normal', 'normal']);
      });
})->count();

echo "Results with flexible matching:\n";
echo "Skin - Normal: $skinNormal, Abnormal: $skinAbnormal\n";
echo "Scalp - Normal: $scalpNormal, Abnormal: $scalpAbnormal\n";
echo "Eyes - Normal: $eyesNormal, Abnormal: $eyesAbnormal\n";
echo "Mouth - Normal: $mouthNormal, Abnormal: $mouthAbnormal\n";
echo "Throat - Normal: $throatNormal, Abnormal: $throatAbnormal\n";
echo "Abdomen - Normal: $abdomenNormal, Distended: $abdomenDistended, Abnormal: $abdomenAbnormal\n";
echo "Deformities - None: $deformitiesNone, Congenital: $deformitiesCongenital, Acquired: $deformitiesAcquired\n";
echo "Lungs - Normal: $lungsNormal\n";
echo "Heart - Normal: $heartNormal\n";

echo "\nTotal records with data: " . HealthExamination::whereNotNull('skin')->orWhereNotNull('scalp')->orWhereNotNull('eye')->count() . "\n";
