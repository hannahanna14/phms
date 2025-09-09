<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== REFRESHED Health Treatments Database Check ===\n";
echo "Current timestamp: " . date('Y-m-d H:i:s') . "\n\n";

try {
    // Get ALL health treatments to see current state
    $allTreatments = DB::select("SELECT id, student_id, grade_level, chief_complaint, treatment, created_at FROM health_treatments ORDER BY created_at DESC");

    echo "Total treatments in database: " . count($allTreatments) . "\n\n";

    if (count($allTreatments) > 0) {
        echo "=== Latest 10 Treatments ===\n";
        $latest = array_slice($allTreatments, 0, 10);
        foreach ($latest as $t) {
            echo "ID: {$t->id}, Student: {$t->student_id}, Grade: [" . ($t->grade_level ?? 'NULL') . "], Complaint: {$t->chief_complaint}, Created: {$t->created_at}\n";
        }
        echo "\n";

        // Group by grade_level to see distribution
        $gradeGroups = [];
        foreach ($allTreatments as $treatment) {
            $grade = $treatment->grade_level ?? 'NULL';
            if (!isset($gradeGroups[$grade])) {
                $gradeGroups[$grade] = 0;
            }
            $gradeGroups[$grade]++;
        }

        echo "=== Grade Level Distribution ===\n";
        foreach ($gradeGroups as $grade => $count) {
            echo "Grade '{$grade}': {$count} treatments\n";
        }
    } else {
        echo "No treatments found in database.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
