<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run()
    {
        $students = [
            [
                'full_name' => 'John Smith',
                'sex' => 'Male',
                'age' => 15
            ],
            [
                'full_name' => 'Sarah Johnson',
                'sex' => 'Female',
                'age' => 14
            ],
            [
                'full_name' => 'Michael Brown',
                'sex' => 'Male',
                'age' => 16
            ],
            [
                'full_name' => 'Emily Davis',
                'sex' => 'Female',
                'age' => 15
            ],
            [
                'full_name' => 'David Wilson',
                'sex' => 'Male',
                'age' => 14
            ]
        ];

        foreach ($students as $student) {
            Student::create($student);
        }
    }
}
