<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class StudentSeeder extends Seeder
{
    public function run()
    {
        // Clear existing students
        Student::truncate();
        
        $currentYear = date('Y');
        $schoolYear = ($currentYear - 1) . '-' . $currentYear;
        
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
        
        $grades = ['Kinder 2', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6'];
        $studentId = 1;
        
        foreach ($grades as $grade) {
            $studentsPerGrade = ($grade === 'Kinder 2') ? 14 : 15;
            
            for ($i = 0; $i < $studentsPerGrade; $i++) {
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
                };
                
                $age = $baseAge + rand(0, 1);
                $birthDate = Carbon::now()->subYears($age)->subMonths(rand(0, 11))->subDays(rand(0, 30));
                
                Student::create([
                    'full_name' => $firstName . ' ' . $lastName,
                    'sex' => $gender,
                    'age' => $age,
                    'date_of_birth' => $birthDate,
                    'birthplace' => $birthplaces[array_rand($birthplaces)],
                    'parent_guardian' => $this->generateParentName($lastName),
                    'address' => $addresses[array_rand($addresses)],
                    'grade_level' => $grade,
                    'lrn' => '1' . str_pad($studentId, 11, '0', STR_PAD_LEFT),
                    'school_year' => $schoolYear
                ]);
                
                $studentId++;
            }
        }
        
        $this->command->info('Created 100 students with realistic Filipino names across all grade levels');
    }
    
    private function generateParentName($lastName)
    {
        $parentFirstNames = ['Roberto', 'Maria', 'Jose', 'Ana', 'Carlos', 'Elena', 'Miguel', 'Rosa', 'Antonio', 'Carmen'];
        return $parentFirstNames[array_rand($parentFirstNames)] . ' ' . $lastName;
    }
}