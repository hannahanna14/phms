<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class StudentSeeder extends Seeder
{
    private $addresses = [
        '123 Rizal St., Brgy. San Antonio, Quezon City',
        '456 Bonifacio Ave., Brgy. Santa Cruz, Manila',
        '789 Mabini St., Brgy. San Jose, Makati',
        '321 Del Pilar St., Brgy. Poblacion, Pasig',
        '654 Luna St., Brgy. San Miguel, Marikina',
        '987 Burgos St., Brgy. Santo Niño, San Juan',
        '111 Aguinaldo St., Brgy. Poblacion, Taguig',
        '222 Lapu-Lapu Ave., Brgy. San Pedro, Muntinlupa',
        '333 Katipunan St., Brgy. Bagong Silang, Caloocan',
        '444 EDSA, Brgy. Cubao, Quezon City'
    ];

    private $birthplaces = [
        'Quezon City',
        'Manila',
        'Makati',
        'Pasig',
        'Marikina',
        'San Juan',
        'Taguig',
        'Muntinlupa',
        'Caloocan',
        'Mandaluyong'
    ];

    public function run()
    {
        // Clear existing students
        Student::truncate();

        // Define school years for progression (most recent first)
        $schoolYears = [
            '2024-2025', // Current year
            '2023-2024', // Previous year
            '2022-2023', // Two years ago
            '2021-2022', // Three years ago
        ];

        // Filipino names
        $maleFirstNames = ['Juan', 'Jose', 'Antonio', 'Francisco', 'Manuel', 'Pedro', 'Luis', 'Carlos', 'Miguel', 'Rafael', 'Daniel', 'Gabriel', 'Andres', 'Fernando', 'Ricardo', 'Mario', 'Roberto', 'Eduardo', 'Alejandro', 'Diego'];
        $femaleFirstNames = ['Maria', 'Ana', 'Carmen', 'Luz', 'Rosa', 'Elena', 'Isabel', 'Teresa', 'Patricia', 'Gloria', 'Esperanza', 'Remedios', 'Cristina', 'Josefa', 'Concepcion', 'Angelica', 'Beatriz', 'Dolores', 'Francisca', 'Guadalupe'];
        $lastNames = ['Santos', 'Reyes', 'Cruz', 'Bautista', 'Ocampo', 'Garcia', 'Mendoza', 'Torres', 'Gonzales', 'Lopez', 'Perez', 'Flores', 'Rivera', 'Ramos', 'Castillo', 'Villanueva', 'Fernandez', 'Pascual', 'Morales', 'Aquino', 'Dela Cruz', 'Hernandez', 'Jimenez', 'Alvarez', 'Romero'];

        $addresses = [
            '123 Rizal St., Brgy. San Antonio, Quezon City',
            '456 Bonifacio Ave., Brgy. Santa Cruz, Manila',
            '789 Mabini St., Brgy. San Jose, Makati',
            '321 Del Pilar St., Brgy. Poblacion, Pasig',
            '654 Luna St., Brgy. San Miguel, Marikina',
            '987 Burgos St., Brgy. Santo Niño, San Juan',
            '111 Aguinaldo St., Brgy. Poblacion, Taguig',
            '222 Lapu-Lapu Ave., Brgy. San Pedro, Muntinlupa',
            '333 Katipunan St., Brgy. Bagong Silang, Caloocan',
            '444 EDSA, Brgy. Cubao, Quezon City'
        ];

        $birthplaces = [
            'Quezon City',
            'Manila',
            'Makati',
            'Pasig',
            'Marikina',
            'San Juan',
            'Taguig',
            'Muntinlupa',
            'Caloocan',
            'Mandaluyong'
        ];

        $allGrades = ['Kinder 2', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6', 'Non-Graded'];

        $sectionsByGrade = [
            'Kinder 2' => [
                'Generous AM',
                'Generous PM',
                'Good AM',
                'Good PM',
                'SNED – Kindergarten (DHH) (SPED)'
            ],
            'Grade 1' => [
                'Admirable',
                'Adorable',
                'Affectionate',
                'Alert',
                'Amazing'
            ],
            'Grade 2' => [
                'Beloved',
                'Beneficent',
                'Benevolent',
                'Blessed',
                'Blissful',
                'Blossom',
                'SNED – Grade 2 (DHH) (SPED)'
            ],
            'Grade 3' => [
                'Calm',
                'Candor',
                'Charitable',
                'Cheerful',
                'Clever',
                'Curious'
            ],
            'Grade 4' => [
                'Dainty',
                'Dedicated',
                'Demure',
                'Devoted',
                'Dynamic',
                'SNED (Graded) (SPED)'
            ],
            'Grade 5' => [
                'Effective',
                'Efficient',
                'Endurance',
                'Energetic',
                'Everlasting'
            ],
            'Grade 6' => [
                'Fair',
                'Faithful',
                'Flexible',
                'Forbearance',
                'Fortitude',
                'Friendly'
            ],
            'Non-Graded' => [
                'Gracious (SPED)',
                'Grateful (SPED)'
            ]
        ];

        $studentId = 1;

        // Compute the current school year dynamically (school year starts in June)
        $now = Carbon::now();
        if ($now->month >= 6) {
            $currentYear = $now->year . '-' . ($now->year + 1);
        } else {
            $currentYear = ($now->year - 1) . '-' . $now->year;
        }
        // Create students for the current year first
        $currentGrades = $allGrades; // All grades available in current year

        // Store current year students for creating historical records
        $currentYearStudents = [];

        foreach ($currentGrades as $grade) {
            $sections = $sectionsByGrade[$grade];
            $studentsPerGrade = ($grade === 'Kinder 2') ? 90 : ($grade === 'Non-Graded' ? 20 : 90);
            $studentsPerSection = ceil($studentsPerGrade / count($sections));

            foreach ($sections as $sectionIndex => $section) {
                $studentsInThisSection = ($sectionIndex === count($sections) - 1)
                    ? ($studentsPerGrade - ($studentsPerSection * $sectionIndex))
                    : $studentsPerSection;

                for ($i = 0; $i < $studentsInThisSection; $i++) {
                    $gender = rand(0, 1) ? 'Male' : 'Female';
                    $firstName = $gender === 'Male'
                        ? $maleFirstNames[array_rand($maleFirstNames)]
                        : $femaleFirstNames[array_rand($femaleFirstNames)];
                    $lastName = $lastNames[array_rand($lastNames)];

                    // Calculate age based on grade level
                    $baseAge = match($grade) {
                        'Kinder 2' => 5,
                        'Grade 1' => 6,
                        'Grade 2' => 7,
                        'Grade 3' => 8,
                        'Grade 4' => 9,
                        'Grade 5' => 10,
                        'Grade 6' => 11,
                        'Non-Graded' => rand(6, 18), // Non-graded can be various ages
                    };

                    $age = $baseAge + rand(0, 1);
                    $birthDate = Carbon::now()->subYears($age)->subMonths(rand(0, 11))->subDays(rand(0, 30));

                    $studentData = [
                        'full_name' => $firstName . ' ' . $lastName,
                        'sex' => $gender,
                        'age' => $age,
                        'date_of_birth' => $birthDate,
                        'birthplace' => $birthplaces[array_rand($birthplaces)],
                        'parent_guardian' => $this->generateParentName($lastName),
                        'address' => $addresses[array_rand($addresses)],
                        'grade_level' => $grade,
                        'section' => $section,
                        'lrn' => '1' . str_pad($studentId, 11, '0', STR_PAD_LEFT),
                        'school_year' => $currentYear,
                        'is_active' => true
                    ];

                    Student::create($studentData);

                    // Store student data for creating historical records
                    $currentYearStudents[] = $studentData;

                    $studentId++;
                }
            }
        }

        // Create some inactive students (graduates/transfers) before historical records
        $this->createInactiveStudents();

        // Create more inactive alumni from various years
        $this->createMoreInactiveStudents();

        // Create historical records for students (past school years)
        foreach ($currentYearStudents as $currentStudent) {
            $studentName = $currentStudent['full_name'];
            $studentGender = $currentStudent['sex'];
            $studentBirthDate = $currentStudent['date_of_birth'];
            $studentBirthplace = $currentStudent['birthplace'];
            $studentParent = $currentStudent['parent_guardian'];
            $studentAddress = $currentStudent['address'];
            $studentLrn = $currentStudent['lrn'];
            $currentGrade = $currentStudent['grade_level'];
            $currentSection = $currentStudent['section'];

            // Determine which past grades this student should have
            $pastRecords = [];

            if ($currentGrade === 'Grade 6') {
                $pastRecords = [
                    ['grade' => 'Grade 5', 'year' => '2023-2024', 'age' => $currentStudent['age'] - 1],
                    ['grade' => 'Grade 4', 'year' => '2022-2023', 'age' => $currentStudent['age'] - 2],
                    ['grade' => 'Grade 3', 'year' => '2021-2022', 'age' => $currentStudent['age'] - 3],
                ];
            } elseif ($currentGrade === 'Grade 5') {
                $pastRecords = [
                    ['grade' => 'Grade 4', 'year' => '2023-2024', 'age' => $currentStudent['age'] - 1],
                    ['grade' => 'Grade 3', 'year' => '2022-2023', 'age' => $currentStudent['age'] - 2],
                ];
            } elseif ($currentGrade === 'Grade 4') {
                $pastRecords = [
                    ['grade' => 'Grade 3', 'year' => '2023-2024', 'age' => $currentStudent['age'] - 1],
                ];
            } elseif ($currentGrade === 'Grade 3') {
                $pastRecords = [
                    ['grade' => 'Grade 2', 'year' => '2023-2024', 'age' => $currentStudent['age'] - 1],
                    ['grade' => 'Grade 1', 'year' => '2022-2023', 'age' => $currentStudent['age'] - 2],
                    ['grade' => 'Kinder 2', 'year' => '2021-2022', 'age' => $currentStudent['age'] - 3],
                ];
            } elseif ($currentGrade === 'Grade 2') {
                $pastRecords = [
                    ['grade' => 'Grade 1', 'year' => '2023-2024', 'age' => $currentStudent['age'] - 1],
                    ['grade' => 'Kinder 2', 'year' => '2022-2023', 'age' => $currentStudent['age'] - 2],
                ];
            } elseif ($currentGrade === 'Grade 1') {
                $pastRecords = [
                    ['grade' => 'Kinder 2', 'year' => '2023-2024', 'age' => $currentStudent['age'] - 1],
                ];
            }
            // Kinder 2 and Non-Graded students don't have past records in our system

            // Create historical records
            foreach ($pastRecords as $record) {
                // Find an appropriate section for the past grade
                $pastSections = $sectionsByGrade[$record['grade']] ?? ['A'];
                $pastSection = $pastSections[array_rand($pastSections)];

                Student::create([
                    'full_name' => $studentName,
                    'sex' => $studentGender,
                    'age' => $record['age'],
                    'date_of_birth' => $studentBirthDate,
                    'birthplace' => $studentBirthplace,
                    'parent_guardian' => $studentParent,
                    'address' => $studentAddress,
                    'grade_level' => $record['grade'],
                    'section' => $pastSection,
                    'lrn' => $studentLrn . '-' . str_replace('-', '', $record['year']), // Unique LRN for historical records
                    'school_year' => $record['year'],
                    'is_active' => false // Historical records are not active
                ]);
            }
        }

        $totalStudents = Student::count();
        $this->command->info("Created {$totalStudents} student records with realistic Filipino names across all grade levels and sections");

        // Ensure seeded students from past school years are marked inactive
        // and that current year students remain active. This is a safety
        // measure in case any historical records were created with the
        // wrong `is_active` flag.
        Student::where('school_year', '!=', $currentYear)->update(['is_active' => false]);
        Student::where('school_year', $currentYear)->update(['is_active' => true]);

        $this->command->info("Ensured students from past years are inactive and current year students are active");
    }

    private function createInactiveStudents()
    {
        // Create inactive students who have graduated or transferred
        $inactiveStudents = [
            // Graduates from 2 years ago (2022-2023 school year, Grade 6)
            [
                'first_name' => 'Jose',
                'last_name' => 'Rodriguez',
                'grade' => 'Grade 6',
                'section' => 'Friendly',
                'school_year' => '2022-2023',
                'age' => 13, // Graduated 2 years ago, would be ~15 now
                'reason' => 'graduated'
            ],
            [
                'first_name' => 'Maria',
                'last_name' => 'Santos',
                'grade' => 'Grade 6',
                'section' => 'Faithful',
                'school_year' => '2022-2023',
                'age' => 13,
                'reason' => 'graduated'
            ],

            // Transfers from last year (2023-2024 school year)
            [
                'first_name' => 'Emily',
                'last_name' => 'Garcia',
                'grade' => 'Grade 3',
                'section' => 'Cheerful',
                'school_year' => '2023-2024',
                'age' => 10, // Transferred last year, would be ~11 now
                'reason' => 'transferred'
            ],
            [
                'first_name' => 'Carlos',
                'last_name' => 'Martinez',
                'grade' => 'Grade 4',
                'section' => 'Dynamic',
                'school_year' => '2023-2024',
                'age' => 11,
                'reason' => 'transferred'
            ],
            [
                'first_name' => 'Sofia',
                'last_name' => 'Lopez',
                'grade' => 'Grade 2',
                'section' => 'Blissful',
                'school_year' => '2023-2024',
                'age' => 9,
                'reason' => 'transferred'
            ],

            // More graduates and transfers
            [
                'first_name' => 'Antonio',
                'last_name' => 'Torres',
                'grade' => 'Grade 6',
                'section' => 'Fortitude',
                'school_year' => '2021-2022',
                'age' => 14, // Graduated 3 years ago
                'reason' => 'graduated'
            ],
            [
                'first_name' => 'Isabella',
                'last_name' => 'Flores',
                'grade' => 'Grade 5',
                'section' => 'Everlasting',
                'school_year' => '2023-2024',
                'age' => 12,
                'reason' => 'transferred'
            ],
            [
                'first_name' => 'Miguel',
                'last_name' => 'Rivera',
                'grade' => 'Grade 1',
                'section' => 'Amazing',
                'school_year' => '2023-2024',
                'age' => 8,
                'reason' => 'transferred'
            ]
        ];

        foreach ($inactiveStudents as $inactiveStudent) {
            // Calculate birth date based on age at the time
            $birthDate = Carbon::now()->subYears($inactiveStudent['age'])->subMonths(rand(0, 11))->subDays(rand(0, 30));

            Student::create([
                'full_name' => $inactiveStudent['first_name'] . ' ' . $inactiveStudent['last_name'],
                'sex' => rand(0, 1) ? 'Male' : 'Female', // Random gender since we have specific names
                'age' => $inactiveStudent['age'],
                'date_of_birth' => $birthDate,
                'birthplace' => $this->birthplaces[array_rand($this->birthplaces)],
                'parent_guardian' => $this->generateParentName($inactiveStudent['last_name']),
                'address' => $this->addresses[array_rand($this->addresses)],
                'grade_level' => $inactiveStudent['grade'],
                'section' => $inactiveStudent['section'],
                'lrn' => '1' . str_pad(rand(10000000, 99999999), 11, '0', STR_PAD_LEFT), // Random LRN for inactive students
                'school_year' => $inactiveStudent['school_year'],
                'is_active' => false // These are inactive students
            ]);
        }

        $inactiveCount = count($inactiveStudents);
        $this->command->info("Created {$inactiveCount} inactive students (graduates and transfers) from past school years");
    }

    private function createMoreInactiveStudents()
    {
        // Create more inactive students from various past years to provide better historical data
        $moreInactiveStudents = [
            // 2020-2021 Graduates
            ['name' => 'Ricardo Morales', 'grade' => 'Grade 6', 'year' => '2020-2021', 'age' => 15],
            ['name' => 'Gabriela Aquino', 'grade' => 'Grade 6', 'year' => '2020-2021', 'age' => 15],
            ['name' => 'Fernando Reyes', 'grade' => 'Grade 6', 'year' => '2020-2021', 'age' => 15],

            // 2019-2020 Graduates
            ['name' => 'Luis Cruz', 'grade' => 'Grade 6', 'year' => '2019-2020', 'age' => 16],
            ['name' => 'Carmen Bautista', 'grade' => 'Grade 6', 'year' => '2019-2020', 'age' => 16],

            // 2022-2023 Transfers (various grades)
            ['name' => 'Diego Hernandez', 'grade' => 'Grade 4', 'year' => '2022-2023', 'age' => 11],
            ['name' => 'Valentina Jimenez', 'grade' => 'Grade 2', 'year' => '2022-2023', 'age' => 9],
            ['name' => 'Nicolas Alvarez', 'grade' => 'Grade 5', 'year' => '2022-2023', 'age' => 12],

            // 2021-2022 Transfers
            ['name' => 'Isabella Romero', 'grade' => 'Grade 3', 'year' => '2021-2022', 'age' => 11],
            ['name' => 'Mateo Castillo', 'grade' => 'Grade 1', 'year' => '2021-2022', 'age' => 8],

            // More alumni from different years
            ['name' => 'Victoria Mendoza', 'grade' => 'Grade 6', 'year' => '2018-2019', 'age' => 18],
            ['name' => 'Adrian Torres', 'grade' => 'Grade 6', 'year' => '2018-2019', 'age' => 18],
            ['name' => 'Luna Rivera', 'grade' => 'Grade 6', 'year' => '2017-2018', 'age' => 19],
            ['name' => 'Ethan Ramos', 'grade' => 'Grade 6', 'year' => '2016-2017', 'age' => 20],
        ];

        foreach ($moreInactiveStudents as $inactiveStudent) {
            // Calculate birth date based on age at the time
            $birthDate = Carbon::now()->subYears($inactiveStudent['age'])->subMonths(rand(0, 11))->subDays(rand(0, 30));

            // Split name into first and last
            $nameParts = explode(' ', $inactiveStudent['name']);
            $firstName = $nameParts[0];
            $lastName = isset($nameParts[1]) ? $nameParts[1] : 'Unknown';

            Student::create([
                'full_name' => $inactiveStudent['name'],
                'sex' => rand(0, 1) ? 'Male' : 'Female',
                'age' => $inactiveStudent['age'],
                'date_of_birth' => $birthDate,
                'birthplace' => $this->birthplaces[array_rand($this->birthplaces)],
                'parent_guardian' => $this->generateParentName($lastName),
                'address' => $this->addresses[array_rand($this->addresses)],
                'grade_level' => $inactiveStudent['grade'],
                'section' => 'N/A', // Alumni don't have current sections
                'lrn' => '1' . str_pad(rand(10000000, 99999999), 11, '0', STR_PAD_LEFT),
                'school_year' => $inactiveStudent['year'],
                'is_active' => false
            ]);
        }

        $moreInactiveCount = count($moreInactiveStudents);
        $this->command->info("Created {$moreInactiveCount} additional inactive students from various past years");
    }

    private function generateParentName($lastName)
    {
        $parentFirstNames = ['Roberto', 'Maria', 'Jose', 'Ana', 'Carlos', 'Elena', 'Miguel', 'Rosa', 'Antonio', 'Carmen'];
        return $parentFirstNames[array_rand($parentFirstNames)] . ' ' . $lastName;
    }
}
