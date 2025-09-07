<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\OralHealthTreatment;

try {
    echo "Testing Oral Health Treatment API...\n\n";
    
    // Get first student
    $student = Student::first();
    if (!$student) {
        echo "No students found in database\n";
        exit;
    }
    
    echo "Student: {$student->full_name} (ID: {$student->id})\n";
    echo "Student Grade: {$student->grade_level}\n\n";
    
    // Check if oral health treatments table exists and has columns
    if (!Schema::hasTable('oral_health_treatments')) {
        echo "❌ oral_health_treatments table does not exist\n";
        exit;
    }
    
    echo "✓ oral_health_treatments table exists\n";
    
    if (!Schema::hasColumn('oral_health_treatments', 'grade_level')) {
        echo "❌ grade_level column missing\n";
    } else {
        echo "✓ grade_level column exists\n";
    }
    
    if (!Schema::hasColumn('oral_health_treatments', 'school_year')) {
        echo "❌ school_year column missing\n";
    } else {
        echo "✓ school_year column exists\n";
    }
    
    // Create test record
    $treatment = OralHealthTreatment::create([
        'student_id' => $student->id,
        'date' => '2024-12-15',
        'title' => 'Test Treatment',
        'chief_complaint' => 'Routine checkup',
        'treatment' => 'Cleaning',
        'remarks' => 'Good condition',
        'status' => 'completed',
        'grade_level' => '6',
        'school_year' => '2024-2025'
    ]);
    
    echo "\n✓ Created test treatment record (ID: {$treatment->id})\n";
    
    // Test API query
    $treatments = OralHealthTreatment::where('student_id', $student->id)
        ->where('grade_level', '6')
        ->get();
    
    echo "Found {$treatments->count()} treatments for Grade 6\n";
    
    foreach ($treatments as $t) {
        echo "  - ID: {$t->id}, Title: {$t->title}, Grade: {$t->grade_level}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
