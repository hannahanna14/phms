<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

use Illuminate\Support\Facades\DB;

try {
    // Create a test oral health treatment record
    $studentId = 1; // Assuming student ID 1 exists
    
    $recordId = DB::table('oral_health_treatments')->insertGetId([
        'student_id' => $studentId,
        'date' => '2024-12-15',
        'title' => 'Test Oral Health Treatment',
        'chief_complaint' => 'Routine dental checkup',
        'treatment' => 'Dental cleaning and fluoride treatment',
        'remarks' => 'Patient responded well to treatment',
        'status' => 'completed',
        'grade_level' => '6',
        'school_year' => '2024-2025',
        'created_at' => now(),
        'updated_at' => now()
    ]);
    
    echo "✓ Created test oral health treatment record with ID: $recordId\n";
    echo "  Student ID: $studentId\n";
    echo "  Grade Level: 6\n";
    echo "  School Year: 2024-2025\n";
    
    // Verify the record was created
    $record = DB::table('oral_health_treatments')->where('id', $recordId)->first();
    if ($record) {
        echo "✓ Record verified in database\n";
        echo "  Title: {$record->title}\n";
        echo "  Date: {$record->date}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
