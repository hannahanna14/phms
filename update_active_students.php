<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Update all existing students to be active
$updated = DB::table('students')->update(['is_active' => true]);

echo "Updated {$updated} students to active status.\n";
