<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

try {
    // Check if health_treatments table exists
    $pdo = DB::connection()->getPdo();
    $stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' AND table_name = 'health_treatments'");
    $result = $stmt->fetch();
    
    if ($result) {
        echo "✓ health_treatments table exists\n";
        
        // Check table structure
        $stmt = $pdo->query("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'health_treatments' ORDER BY ordinal_position");
        $columns = $stmt->fetchAll();
        
        echo "Table columns:\n";
        foreach ($columns as $column) {
            echo "  - {$column['column_name']} ({$column['data_type']})\n";
        }
        
        // Check if there are any records
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM health_treatments");
        $count = $stmt->fetch();
        echo "Records in table: {$count['count']}\n";
        
    } else {
        echo "✗ health_treatments table does not exist\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
