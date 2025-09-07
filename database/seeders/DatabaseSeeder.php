<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\StudentSeeder;
use Database\Seeders\HealthExaminationSeeder;
use Database\Seeders\OralHealthExaminationSeeder;
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
            'full_name' => 'Tnurse',
            'email' => 'test@example.com', // Added email field
            'password' => Hash::make('password'),
            'role' => 'nurse',
            'email_verified_at' => now() // Optional, but can help
        ]);

        // Create teacher user
        User::create([
            'username' => 'teacher',
            'full_name' => 'Teacher User',
            'email' => 'teacher@example.com',
            'password' => Hash::make('teacher'),
            'role' => 'teacher',
            'email_verified_at' => now()
        ]);

        $this->call([
            StudentSeeder::class,
            HealthExaminationSeeder::class,
            OralHealthExaminationSeeder::class,
            StudentTeacherAssignmentSeeder::class,
        ]);
    }
}
