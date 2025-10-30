<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get a sample health examination record
$exam = \App\Models\HealthExamination::first();

if ($exam) {
    echo "Sample Health Examination Record:\n";
    echo "ID: " . $exam->id . "\n";
    echo "Examination Date: " . ($exam->examination_date ? $exam->examination_date->format('Y-m-d') : 'NULL') . "\n";
    echo "Created At: " . ($exam->created_at ? $exam->created_at->format('Y-m-d H:i:s') : 'NULL') . "\n";
    echo "Updated At: " . ($exam->updated_at ? $exam->updated_at->format('Y-m-d H:i:s') : 'NULL') . "\n";
    echo "\nRaw attributes:\n";
    print_r($exam->getAttributes());
} else {
    echo "No health examination records found in database.\n";
}
