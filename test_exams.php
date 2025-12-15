<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\HealthExamination;

echo "Testing examination data...\n";

// Get latest exam IDs
$latestExamIds = HealthExamination::selectRaw('MAX(id) as id')
    ->groupBy('student_id')
    ->pluck('id');

$baseQuery = HealthExamination::whereIn('id', $latestExamIds);

echo "Total latest exams: " . $latestExamIds->count() . "\n";

// Test vision screening
$visionNormal = (clone $baseQuery)->where('vision_screening', 'Normal')->count();
$visionAbnormal = (clone $baseQuery)->whereNotNull('vision_screening')->where('vision_screening', '!=', 'Normal')->count();
echo "Vision - Normal: $visionNormal, Abnormal: $visionAbnormal\n";

// Test skin
$skinNormal = (clone $baseQuery)->where('skin', 'Normal')->count();
$skinAbnormal = (clone $baseQuery)->whereNotNull('skin')->where('skin', '!=', 'Normal')->count();
echo "Skin - Normal: $skinNormal, Abnormal: $skinAbnormal\n";

// Test eyes
$eyesNormal = (clone $baseQuery)->where('eye', 'Normal')->count();
$eyesAbnormal = (clone $baseQuery)->whereNotNull('eye')->where('eye', '!=', 'Normal')->count();
echo "Eyes - Normal: $eyesNormal, Abnormal: $eyesAbnormal\n";

// Test deformities
$deformitiesNone = (clone $baseQuery)->whereIn('deformities', ['None', 'none', 'Normal', 'normal'])->count();
$deformitiesHas = (clone $baseQuery)->whereNotNull('deformities')->whereNotIn('deformities', ['None', 'none', 'Normal', 'normal'])->count();
echo "Deformities - None: $deformitiesNone, Has: $deformitiesHas\n";

echo "All examination metrics should have data!\n";
