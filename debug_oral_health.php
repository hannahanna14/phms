<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "=== Debugging Oral Health System ===\n\n";
    
    // 1. Check if table exists
    $tableExists = \Illuminate\Support\Facades\Schema::hasTable('oral_health_examinations');
    echo "1. Table 'oral_health_examinations' exists: " . ($tableExists ? 'YES' : 'NO') . "\n";
    
    if (!$tableExists) {
        echo "ERROR: Table does not exist! Need to run migrations.\n";
        exit;
    }
    
    // 2. Check table structure
    $columns = \Illuminate\Support\Facades\Schema::getColumnListing('oral_health_examinations');
    echo "2. Table columns: " . implode(', ', $columns) . "\n\n";
    
    // 3. Check if new columns exist
    $requiredColumns = [
        'permanent_index_dft', 'permanent_teeth_decayed', 'permanent_teeth_filled',
        'temporary_index_dft', 'temporary_teeth_decayed', 'temporary_teeth_filled'
    ];
    
    foreach ($requiredColumns as $col) {
        $exists = in_array($col, $columns);
        echo "   - $col: " . ($exists ? 'EXISTS' : 'MISSING') . "\n";
    }
    
    // 4. Check existing data
    $count = \Illuminate\Support\Facades\DB::table('oral_health_examinations')->count();
    echo "\n3. Records in table: $count\n";
    
    if ($count > 0) {
        $records = \Illuminate\Support\Facades\DB::table('oral_health_examinations')->get();
        echo "4. Sample record:\n";
        foreach ($records->first() as $key => $value) {
            echo "   $key: $value\n";
        }
    }
    
    // 5. Test model
    echo "\n5. Testing OralHealthExamination model:\n";
    $model = new \App\Models\OralHealthExamination();
    echo "   Fillable fields: " . implode(', ', $model->getFillable()) . "\n";
    
    // 6. Try to create a record
    echo "\n6. Attempting to create test record...\n";
    $testRecord = \App\Models\OralHealthExamination::create([
        'student_id' => 1,
        'grade_level' => '6',
        'school_year' => '2024-2025',
        'permanent_index_dft' => 3,
        'permanent_teeth_decayed' => 2,
        'permanent_teeth_filled' => 2,
        'permanent_total_dft' => 15,
        'permanent_for_extraction' => 1,
        'permanent_for_filling' => 1,
        'temporary_index_dft' => 2,
        'temporary_teeth_decayed' => 1,
        'temporary_teeth_filled' => 2,
        'temporary_total_dft' => 10,
        'temporary_for_extraction' => 1,
        'temporary_for_filling' => 1,
    ]);
    
    echo "   Test record created with ID: " . $testRecord->id . "\n";
    
    // 7. Test API endpoint
    echo "\n7. Testing controller method...\n";
    $controller = new \App\Http\Controllers\PupilHealthController();
    $request = new \Illuminate\Http\Request(['grade_level' => '6']);
    
    $result = $controller->getOralHealthByGrade(1, $request);
    echo "   API response: " . $result->getContent() . "\n";
    
    echo "\n=== Debug Complete ===\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
