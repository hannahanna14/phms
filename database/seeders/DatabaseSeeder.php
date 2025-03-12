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
        // Create a test user with all required fields
        User::create([
            'username' => 'testuser',
            'full_name' => 'Test User',
            'email' => 'test@example.com', // Added email field
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now() // Optional, but can help
        ]);

        $this->call([
            StudentSeeder::class,
            HealthExaminationSeeder::class,
        ]);
    }
}