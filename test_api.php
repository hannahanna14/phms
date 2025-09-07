<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Test if table exists
    $tableExists = \Illuminate\Support\Facades\Schema::hasTable('oral_health_examinations');
    echo "Table exists: " . ($tableExists ? 'YES' : 'NO') . "\n";
    
    if ($tableExists) {
        // Check columns
        $columns = \Illuminate\Support\Facades\Schema::getColumnListing('oral_health_examinations');
        echo "Columns: " . implode(', ', $columns) . "\n";
        
        // Check data
        $count = \Illuminate\Support\Facades\DB::table('oral_health_examinations')->count();
        echo "Records count: " . $count . "\n";
        
        if ($count > 0) {
            $records = \Illuminate\Support\Facades\DB::table('oral_health_examinations')->get();
            echo "Sample record:\n";
            print_r($records->first());
        }
    }
    
    // Test the model
    echo "\nTesting OralHealthExamination model:\n";
    $model = new \App\Models\OralHealthExamination();
    echo "Model class: " . get_class($model) . "\n";
    echo "Table name: " . $model->getTable() . "\n";
    echo "Fillable: " . implode(', ', $model->getFillable()) . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
