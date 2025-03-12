<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run()
    {
        $currentYear = date('Y');
        $students = [
            [
                'full_name' => 'John Smith',
                'sex' => 'Male',
                'age' => 15,
                'lrn' => '123456789001',
                'grade_level' => '6',
                'school_year' => ($currentYear - 1) . '-' . $currentYear
            ],
            [
                'full_name' => 'Sarah Johnson',
                'sex' => 'Female',
                'age' => 14,
                'lrn' => '123456789002',
                'grade_level' => '5',
                'school_year' => ($currentYear - 1) . '-' . $currentYear
            ],
            [
                'full_name' => 'Michael Brown',
                'sex' => 'Male',
                'age' => 16,
                'lrn' => '123456789003',
                'grade_level' => '6',
                'school_year' => ($currentYear - 1) . '-' . $currentYear
            ],
            [
                'full_name' => 'Emily Davis',
                'sex' => 'Female',
                'age' => 15,
                'lrn' => '123456789004',
                'grade_level' => '5',
                'school_year' => ($currentYear - 1) . '-' . $currentYear
            ],
            [
                'full_name' => 'David Wilson',
                'sex' => 'Male',
                'age' => 14,
                'lrn' => '123456789005',
                'grade_level' => '4',
                'school_year' => ($currentYear - 1) . '-' . $currentYear
            ]
        ];

        foreach ($students as $student) {
            Student::create($student);
        }
    }
}