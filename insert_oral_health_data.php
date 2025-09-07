<?php

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

// Database configuration
$capsule = new Capsule;

$capsule->addConnection([
    'driver' => 'sqlite',
    'database' => 'database/database.sqlite',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

try {
    // Insert sample oral health data
    $data = [
        [
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
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ],
        [
            'student_id' => 1,
            'grade_level' => '5',
            'school_year' => '2023-2024',
            'permanent_index_dft' => 2,
            'permanent_teeth_decayed' => 1,
            'permanent_teeth_filled' => 1,
            'permanent_total_dft' => 12,
            'permanent_for_extraction' => 0,
            'permanent_for_filling' => 2,
            'temporary_index_dft' => 3,
            'temporary_teeth_decayed' => 2,
            'temporary_teeth_filled' => 1,
            'temporary_total_dft' => 15,
            'temporary_for_extraction' => 2,
            'temporary_for_filling' => 0,
            'created_at' => date('Y-m-d H:i:s', strtotime('-1 year')),
            'updated_at' => date('Y-m-d H:i:s', strtotime('-1 year')),
        ]
    ];

    foreach ($data as $record) {
        Capsule::table('oral_health_examinations')->insert($record);
    }

    echo "Oral health data inserted successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
