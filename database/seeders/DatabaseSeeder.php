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
        // Create Admin User
        User::create([
            'username' => 'admin',
            'full_name' => 'Administrator',
            'email' => 'admin@phms.edu.ph',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'email_verified_at' => now()
        ]);

        // Create Teachers (7 teachers for each grade level)
        $teachers = [
            ['name' => 'Maria Santos Rodriguez', 'grade' => 'Kinder 2'],
            ['name' => 'Jose Miguel Dela Cruz', 'grade' => 'Grade 1'],
            ['name' => 'Ana Luz Villanueva', 'grade' => 'Grade 2'],
            ['name' => 'Roberto Carlos Mendoza', 'grade' => 'Grade 3'],
            ['name' => 'Carmen Rosa Fernandez', 'grade' => 'Grade 4'],
            ['name' => 'Eduardo Ramos Pascual', 'grade' => 'Grade 5'],
            ['name' => 'Luz Marina Gonzales', 'grade' => 'Grade 6'],
        ];

        foreach ($teachers as $teacher) {
            User::create([
                'username' => strtolower(str_replace(' ', '', explode(' ', $teacher['name'])[0] . explode(' ', $teacher['name'])[1])),
                'full_name' => $teacher['name'],
                'email' => strtolower(str_replace(' ', '.', $teacher['name'])) . '@phms.edu.ph',
                'password' => Hash::make('teacher123'),
                'role' => 'teacher',
                'email_verified_at' => now()
            ]);
        }

        // Create Nurses (3 nurses)
        $nurses = [
            ['name' => 'Liza Dela Cruz', 'contact' => '09171234567'],
            ['name' => 'Ramon Santos', 'contact' => '09182345678'],
            ['name' => 'Grace Mendoza', 'contact' => '09193456789'],
        ];

        foreach ($nurses as $nurse) {
            User::create([
                'username' => strtolower(str_replace(' ', '', $nurse['name'])),
                'full_name' => 'Nurse ' . $nurse['name'],
                'email' => strtolower(str_replace(' ', '.', $nurse['name'])) . '@phms.edu.ph',
                'password' => Hash::make('nurse123'),
                'role' => 'nurse',
                'email_verified_at' => now()
            ]);
        }

        $this->call([
            StudentSeeder::class,
            HealthExaminationSeeder::class,
            OralHealthExaminationSeeder::class,
            StudentTeacherAssignmentSeeder::class,
        ]);
    }
}
