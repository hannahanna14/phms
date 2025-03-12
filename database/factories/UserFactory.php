<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\StudentSeeder;
use Database\Seeders\HealthExaminationSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Remove any existing factory or create method calls
        User::create([
            'username' => 'testuser',
            'full_name' => 'Test User',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);

        $this->call([
            StudentSeeder::class,
            HealthExaminationSeeder::class,
        ]);
    }
}