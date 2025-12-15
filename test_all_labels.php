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

$examTypes = [
    'vision_screening' => 'Vision Screening',
    'skin' => 'Skin',
    'eye' => 'Eyes',
    'ear' => 'Ears',
    'nose' => 'Nose',
    'mouth' => 'Mouth',
    'throat' => 'Throat',
    'neck' => 'Neck',
    'lungs' => 'Lungs',
    'heart' => 'Heart',
    'abdomen' => 'Abdomen',
    'deformities' => 'Deformities'
];

foreach ($examTypes as $field => $name) {
    $values = (clone $baseQuery)
        ->whereNotNull($field)
        ->selectRaw("$field as value, COUNT(*) as count")
        ->groupBy($field)
        ->orderBy('count', 'desc')
        ->get();

    if ($values->count() > 0) {
        echo "\n$name labels:\n";
        foreach ($values as $value) {
            echo "- '{$value->value}' ({$value->count})\n";
        }
    }
}
