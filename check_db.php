<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\HealthExamination;

echo "Checking BMI values in database...\n";

try {
    $bmiValues = HealthExamination::selectRaw('DISTINCT nutritional_status_bmi, COUNT(*) as count')
        ->groupBy('nutritional_status_bmi')
        ->orderBy('count', 'desc')
        ->get();

    echo "BMI Distribution:\n";
    foreach ($bmiValues as $value) {
        echo "- '{$value->nutritional_status_bmi}': {$value->count} records\n";
    }

    echo "\nTotal records: " . HealthExamination::count() . "\n";

    // Check normal values specifically
    $normalCount = HealthExamination::where('nutritional_status_bmi', 'Normal')->count();
    echo "Records with exactly 'Normal': $normalCount\n";

    $normalLikeCount = HealthExamination::where('nutritional_status_bmi', 'like', '%Normal%')->count();
    echo "Records containing 'Normal': $normalLikeCount\n";

    // Check latest examinations
    echo "\nChecking latest examinations per student:\n";

    $latestExamIds = HealthExamination::selectRaw('MAX(id) as id')
        ->groupBy('student_id')
        ->pluck('id');

    echo "Total latest exams: " . $latestExamIds->count() . "\n";

    $latestNormalCount = HealthExamination::whereIn('id', $latestExamIds)
        ->where('nutritional_status_bmi', 'Normal')
        ->count();

    echo "Latest exams with 'Normal' BMI: $latestNormalCount\n";

    $latestWithBMI = HealthExamination::whereIn('id', $latestExamIds)
        ->whereNotNull('nutritional_status_bmi')
        ->count();

    echo "Latest exams with BMI data: $latestWithBMI\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
