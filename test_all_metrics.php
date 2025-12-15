<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\StudentController;
use Illuminate\Http\Request;
use App\Models\User;

// Create a fake admin user for testing
$user = User::first();
if (!$user) {
    $user = User::create([
        'name' => 'Test Admin',
        'email' => 'admin@test.com',
        'password' => bcrypt('password'),
        'role' => 'admin'
    ]);
}

auth()->login($user);

$controller = new StudentController();

$testCases = [
    // Deworming
    ['type' => 'deworming', 'value' => 'dewormed', 'name' => 'Dewormed'],
    ['type' => 'deworming', 'value' => 'not_dewormed', 'name' => 'Not Dewormed'],

    // BMI
    ['type' => 'bmi', 'value' => 'normal', 'name' => 'Normal BMI'],
    ['type' => 'bmi', 'value' => 'underweight', 'name' => 'Underweight BMI'],
    ['type' => 'bmi', 'value' => 'overweight_obese', 'name' => 'Overweight/Obese BMI'],

    // Height for Age
    ['type' => 'nutritionalHeight', 'value' => 'normal', 'name' => 'Normal Height'],
    ['type' => 'nutritionalHeight', 'value' => 'mild_stunting', 'name' => 'Mild Stunting'],
    ['type' => 'nutritionalHeight', 'value' => 'severe_stunting', 'name' => 'Severe Stunting'],

    // Iron Supplementation
    ['type' => 'ironSupplement', 'value' => 'positive', 'name' => 'Iron Positive'],
    ['type' => 'ironSupplement', 'value' => 'negative', 'name' => 'Iron Negative'],

    // Vision Screening
    ['type' => 'visionScreening', 'value' => 'normal', 'name' => 'Vision Normal (Passed)'],
    ['type' => 'visionScreening', 'value' => 'abnormal', 'name' => 'Vision Abnormal (Failed)'],

    // Skin Examination
    ['type' => 'skinExamination', 'value' => 'normal', 'name' => 'Skin Normal'],
    ['type' => 'skinExamination', 'value' => 'abnormal', 'name' => 'Skin Abnormal'],

    // Eyes Examination
    ['type' => 'eyesExamination', 'value' => 'normal', 'name' => 'Eyes Normal'],
    ['type' => 'eyesExamination', 'value' => 'abnormal', 'name' => 'Eyes Abnormal'],

    // Deformities
    ['type' => 'deformities', 'value' => 'none', 'name' => 'No Deformities'],
    ['type' => 'deformities', 'value' => 'has_deformities', 'name' => 'Has Deformities'],
];

echo "Testing all health metrics API endpoints...\n\n";

foreach ($testCases as $testCase) {
    $request = new Request([
        'type' => $testCase['type'],
        'value' => $testCase['value'],
        'grade_level' => 'All',
        'section' => 'All'
    ]);

    $response = $controller->getStudentsByCriteria($request);
    $data = json_decode($response->getContent(), true);

    $count = $data['total'] ?? 0;
    echo "{$testCase['name']}: {$count} students\n";
}

echo "\nAll tests completed!\n";
