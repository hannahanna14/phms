<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking Health Examination Fields...\n\n";

$exam = \App\Models\HealthExamination::with('student')->first();

if (!$exam) {
    echo "No records found!\n";
    exit;
}

echo "Fields with data:\n";
foreach ($exam->getAttributes() as $key => $value) {
    if (!is_null($value) && $value !== '') {
        echo "✓ $key: " . (strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value) . "\n";
    }
}

echo "\nFields that are NULL or empty:\n";
foreach ($exam->getAttributes() as $key => $value) {
    if (is_null($value) || $value === '') {
        echo "✗ $key\n";
    }
}

echo "\nStudent data:\n";
if ($exam->student) {
    echo "✓ Student Name: " . $exam->student->full_name . "\n";
    echo "✓ LRN: " . $exam->student->lrn . "\n";
}
