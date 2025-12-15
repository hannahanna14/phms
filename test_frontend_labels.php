<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\HealthExamination;

// Get dashboard data similar to frontend
$latestExamIds = HealthExamination::selectRaw('MAX(id) as id')
    ->groupBy('student_id')
    ->pluck('id');

$baseQuery = HealthExamination::whereIn('id', $latestExamIds);

// Test vision screening values
$visionValues = (clone $baseQuery)
    ->whereNotNull('vision_screening')
    ->selectRaw('vision_screening, COUNT(*) as count')
    ->groupBy('vision_screening')
    ->orderBy('count', 'desc')
    ->get();

echo "Vision Screening labels:\n";
foreach ($visionValues as $value) {
    echo "- '{$value->vision_screening}' ({$value->count})\n";
}

// Test skin values
$skinValues = (clone $baseQuery)
    ->whereNotNull('skin')
    ->selectRaw('skin, COUNT(*) as count')
    ->groupBy('skin')
    ->orderBy('count', 'desc')
    ->get();

echo "\nSkin Examination labels:\n";
foreach ($skinValues as $value) {
    echo "- '{$value->skin}' ({$value->count})\n";
}

// Test eyes values
$eyeValues = (clone $baseQuery)
    ->whereNotNull('eye')
    ->selectRaw('eye, COUNT(*) as count')
    ->groupBy('eye')
    ->orderBy('count', 'desc')
    ->get();

echo "\nEye Examination labels:\n";
foreach ($eyeValues as $value) {
    echo "- '{$value->eye}' ({$value->count})\n";
}

// Test height values
$heightValues = (clone $baseQuery)
    ->whereNotNull('nutritional_status_height')
    ->selectRaw('nutritional_status_height, COUNT(*) as count')
    ->groupBy('nutritional_status_height')
    ->orderBy('count', 'desc')
    ->get();

echo "\nHeight for Age labels:\n";
foreach ($heightValues as $value) {
    echo "- '{$value->nutritional_status_height}' ({$value->count})\n";
}
