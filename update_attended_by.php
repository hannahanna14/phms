<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\HealthTreatment;

// Get all treatments with null attended_by
$treatments = HealthTreatment::whereNull('attended_by')->get();

echo "Found {$treatments->count()} treatments with null attended_by\n";

// Update them
foreach ($treatments as $treatment) {
    $treatment->attended_by = 'School Nurse';
    $treatment->save();
    echo "Updated treatment ID: {$treatment->id}\n";
}

echo "\nDone! All treatments updated.\n";

// Show sample of updated records
echo "\nSample of treatments:\n";
$sample = HealthTreatment::select('id', 'attended_by', 'date')->take(5)->get();
foreach ($sample as $t) {
    echo "ID: {$t->id}, Attended By: {$t->attended_by}, Date: {$t->date}\n";
}
