<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\StudentController;
use Illuminate\Http\Request;
use App\Models\User;

$controller = new StudentController();

// Create a fake admin user for testing
$user = User::first(); // Get first user as admin
if (!$user) {
    $user = User::create([
        'name' => 'Test Admin',
        'email' => 'admin@test.com',
        'password' => bcrypt('password'),
        'role' => 'admin'
    ]);
}

// Mock authentication
auth()->login($user);

// Test skin examination
$request = new Request([
    'type' => 'skinExamination',
    'value' => 'normal',
    'grade_level' => 'All',
    'section' => 'All'
]);

$response = $controller->getStudentsByCriteria($request);
$data = json_decode($response->getContent(), true);

echo "Skin Normal API Test:\n";
echo "Found: " . ($data['total'] ?? 0) . " students\n";

// Test skin abnormal
$request2 = new Request([
    'type' => 'skinExamination',
    'value' => 'abnormal',
    'grade_level' => 'All',
    'section' => 'All'
]);

$response2 = $controller->getStudentsByCriteria($request2);
$data2 = json_decode($response2->getContent(), true);

echo "Skin Abnormal API Test:\n";
echo "Found: " . ($data2['total'] ?? 0) . " students\n";

// Test vision screening normal
$request3 = new Request([
    'type' => 'visionScreening',
    'value' => 'normal',
    'grade_level' => 'All',
    'section' => 'All'
]);

$response3 = $controller->getStudentsByCriteria($request3);
$data3 = json_decode($response3->getContent(), true);

echo "Vision Normal API Test:\n";
echo "Found: " . ($data3['total'] ?? 0) . " students\n";

// Test vision screening abnormal
$request4 = new Request([
    'type' => 'visionScreening',
    'value' => 'abnormal',
    'grade_level' => 'All',
    'section' => 'All'
]);

$response4 = $controller->getStudentsByCriteria($request4);
$data4 = json_decode($response4->getContent(), true);

echo "Vision Abnormal API Test:\n";
echo "Found: " . ($data4['total'] ?? 0) . " students\n";
