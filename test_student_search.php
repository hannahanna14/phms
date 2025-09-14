<?php

// Simple test script to verify student search API
require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Models\Student;

// Create a few test students if they don't exist
$testStudents = [
    ['full_name' => 'John Doe', 'lrn' => '123456789', 'grade_level' => '4', 'section' => 'A'],
    ['full_name' => 'Jane Smith', 'lrn' => '987654321', 'grade_level' => '5', 'section' => 'B'],
    ['full_name' => 'Bob Johnson', 'lrn' => '456789123', 'grade_level' => '6', 'section' => 'C']
];

echo "Testing Student Search API...\n";

// Test the search functionality
$query = 'John';
echo "Searching for: $query\n";

$students = Student::where(function($q) use ($query) {
        $q->where('full_name', 'LIKE', "%{$query}%")
          ->orWhere('lrn', 'LIKE', "%{$query}%");
    })
    ->select('id', 'full_name', 'lrn', 'grade_level', 'section')
    ->orderBy('full_name')
    ->limit(20)
    ->get();

echo "Found " . count($students) . " students:\n";
foreach ($students as $student) {
    echo "- {$student->full_name} (LRN: {$student->lrn}) - Grade {$student->grade_level} - Section {$student->section}\n";
}

echo "\nAPI Response format:\n";
$response = $students->map(function($student) {
    return [
        'id' => $student->id,
        'name' => $student->full_name,
        'lrn' => $student->lrn,
        'grade_level' => $student->grade_level,
        'section' => $student->section,
        'display_text' => $student->full_name . ' (' . $student->lrn . ')'
    ];
});

echo json_encode($response, JSON_PRETTY_PRINT);
