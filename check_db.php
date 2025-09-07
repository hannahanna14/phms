<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(IllwareContractsConsoleKernel::class);

$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    echo "Checking database connection...\n";
    
    $pdo = DB::connection()->getPdo();
    echo "Connected to database: " . DB::connection()->getDatabaseName() . "\n";
    
    $tableExists = Schema::hasTable('oral_health_examinations');
    echo "Table 'oral_health_examinations' exists: " . ($tableExists ? 'Yes' : 'No') . "\n";
    
    if ($tableExists) {
        $columns = Schema::getColumnListing('oral_health_examinations');
        echo "Columns in 'oral_health_examinations': " . implode(', ', $columns) . "\n";
        
        $count = DB::table('oral_health_examinations')->count();
        echo "Number of records: $count\n";
    }
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
