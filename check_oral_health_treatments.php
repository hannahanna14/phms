<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

try {
    // Check if oral_health_treatments table exists
    $pdo = DB::connection()->getPdo();
    $stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' AND table_name = 'oral_health_treatments'");
    $result = $stmt->fetch();
    
    if ($result) {
        echo "✓ oral_health_treatments table exists\n";
        
        // Check table structure
        $stmt = $pdo->query("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'oral_health_treatments' ORDER BY ordinal_position");
        $columns = $stmt->fetchAll();
        
        echo "Table columns:\n";
        foreach ($columns as $column) {
            echo "  - {$column['column_name']} ({$column['data_type']})\n";
        }
        
        // Check if there are any records
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM oral_health_treatments");
        $count = $stmt->fetch();
        echo "Records in table: {$count['count']}\n";
        
        // Show recent records
        if ($count['count'] > 0) {
            $stmt = $pdo->query("SELECT id, student_id, date, title, grade_level, school_year, created_at FROM oral_health_treatments ORDER BY created_at DESC LIMIT 5");
            $records = $stmt->fetchAll();
            
            echo "\nRecent records:\n";
            foreach ($records as $record) {
                echo "  ID: {$record['id']}, Student: {$record['student_id']}, Date: {$record['date']}, Title: {$record['title']}, Grade: {$record['grade_level']}, Year: {$record['school_year']}\n";
            }
        }
        
    } else {
        echo "✗ oral_health_treatments table does not exist\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
