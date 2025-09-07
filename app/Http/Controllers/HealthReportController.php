<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\HealthExamination;
use App\Models\HealthTreatment;
use App\Models\OralHealthTreatment;
use App\Models\Incident;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use PDF;

class HealthReportController extends Controller
{
    public function index(Request $request)
    {
        // Get all distinct grade levels from the database, plus standard options
        $dbGradeLevels = Student::distinct('grade_level')
            ->whereNotNull('grade_level')
            ->pluck('grade_level')
            ->toArray();
        
        // Standard grade levels that should always be available
        $standardGradeLevels = [
            'Kinder 1', 'Kinder 2', 
            'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6',
            '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'
        ];
        
        // Merge and remove duplicates, then sort
        $allGradeLevels = array_unique(array_merge($standardGradeLevels, $dbGradeLevels));
        
        // Custom sort to put them in logical order
        usort($allGradeLevels, function($a, $b) {
            // Define order priority
            $order = [
                'Kinder 1' => 1, 'Kinder 2' => 2,
                'Grade 1' => 3, 'Grade 2' => 4, 'Grade 3' => 5, 'Grade 4' => 6, 'Grade 5' => 7, 'Grade 6' => 8,
                '1' => 9, '2' => 10, '3' => 11, '4' => 12, '5' => 13, '6' => 14,
                '7' => 15, '8' => 16, '9' => 17, '10' => 18, '11' => 19, '12' => 20
            ];
            
            $aOrder = $order[$a] ?? 999;
            $bOrder = $order[$b] ?? 999;
            
            if ($aOrder === $bOrder) {
                return strcmp($a, $b);
            }
            
            return $aOrder - $bOrder;
        });
        
        // Add "All" option at the beginning
        array_unshift($allGradeLevels, 'All');
        
        return Inertia::render('HealthReport/Index', [
            'gradeLevels' => array_values($allGradeLevels)
        ]);
    }

    public function generate(Request $request)
    {
        $request->validate([
            'grade_level' => 'required|string',
            'school_year' => 'required|string',
            'section' => 'nullable|string',
            'fields' => 'required|array',
            'health_exam_fields' => 'nullable|array',
            'gender_filter' => 'nullable|string',
            'min_age' => 'nullable|integer',
            'max_age' => 'nullable|integer',
            'sort_by' => 'nullable|string',
        ]);

        $gradeLevel = $request->grade_level;
        $schoolYear = $request->school_year;
        $selectedFields = $request->fields;

        // Get students for the selected grade
        $user = auth()->user();
        
        // Filter students based on user role
        if ($user->role === 'teacher') {
            // Teachers can only see their assigned students
            $assignedStudentIds = $user->assignedStudents()->pluck('student_id');
            $studentsQuery = Student::whereIn('id', $assignedStudentIds);
        } else {
            // Admins can see all students
            $studentsQuery = Student::query();
        }
        
        // Apply grade level filter (skip if "All" is selected)
        if ($gradeLevel !== 'All') {
            $studentsQuery->where('grade_level', $gradeLevel);
        }
        
        if ($request->section) {
            $studentsQuery->where('section', $request->section);
        }

        // Apply gender filter
        if ($request->gender_filter && $request->gender_filter !== 'All') {
            $studentsQuery->where('sex', $request->gender_filter);
        }

        // Apply age range filter
        if ($request->min_age) {
            $studentsQuery->where('age', '>=', $request->min_age);
        }
        if ($request->max_age) {
            $studentsQuery->where('age', '<=', $request->max_age);
        }
        
        // Apply sorting based on user selection
        $sortBy = $request->sort_by ?? 'Name (A-Z)';
        
        switch ($sortBy) {
            case 'Name (A-Z)':
                $studentsQuery->orderBy('full_name', 'asc');
                break;
            case 'Name (Z-A)':
                $studentsQuery->orderBy('full_name', 'desc');
                break;
            case 'Age (Youngest First)':
                $studentsQuery->orderBy('age', 'asc');
                break;
            case 'Age (Oldest First)':
                $studentsQuery->orderBy('age', 'desc');
                break;
            default:
                $studentsQuery->orderBy('full_name', 'asc');
        }
        
        $students = $studentsQuery->get();

        $reportData = [];

        foreach ($students as $student) {
            $studentData = [];
            
            // Basic student info
            if (in_array('name', $selectedFields)) {
                $studentData['name'] = $student->full_name;
            }
            if (in_array('lrn', $selectedFields)) {
                $studentData['lrn'] = $student->lrn;
            }
            if (in_array('grade_level', $selectedFields)) {
                $studentData['grade_level'] = $student->grade_level;
            }
            if (in_array('section', $selectedFields)) {
                $studentData['section'] = $student->section;
            }
            if (in_array('gender', $selectedFields)) {
                $studentData['gender'] = $student->sex;
            }
            if (in_array('age', $selectedFields)) {
                $studentData['age'] = $student->age;
            }
            if (in_array('birthdate', $selectedFields)) {
                $studentData['birthdate'] = $student->birthdate;
            }

            // Health examination data with selected fields
            if ($request->health_exam_fields && count($request->health_exam_fields) > 0) {
                $healthExam = HealthExamination::where('student_id', $student->id)
                    ->latest()
                    ->first();
                
                $studentData['health_exam'] = $healthExam;
            }

            $reportData[] = $studentData;
        }

        return Inertia::render('HealthReport/Results', [
            'reportData' => $reportData,
            'grade_level' => 'Grade ' . $gradeLevel,
            'school_year' => $schoolYear,
            'section' => $request->section,
            'fields' => $selectedFields,
            'health_exam_fields' => $request->health_exam_fields ?? [],
            'gender_filter' => $request->gender_filter,
            'min_age' => $request->min_age,
            'max_age' => $request->max_age,
            'sort_by' => $request->sort_by
        ]);
    }

    public function exportPdf(Request $request)
    {
        $request->validate([
            'grade_level' => 'required|string',
            'school_year' => 'required|string',
            'section' => 'nullable|string',
            'fields' => 'required|array',
            'health_exam_fields' => 'nullable|array',
            'gender_filter' => 'nullable|string',
            'min_age' => 'nullable|integer',
            'max_age' => 'nullable|integer',
            'sort_by' => 'nullable|string',
        ]);

        $gradeLevel = $request->grade_level;
        $schoolYear = $request->school_year;
        $selectedFields = $request->fields;

        // Get students for the selected grade (same logic as generate method)
        $user = auth()->user();
        
        // Filter students based on user role
        if ($user->role === 'teacher') {
            // Teachers can only see their assigned students
            $assignedStudentIds = $user->assignedStudents()->pluck('student_id');
            $studentsQuery = Student::whereIn('id', $assignedStudentIds);
        } else {
            // Admins can see all students
            $studentsQuery = Student::query();
        }
        
        // Apply grade level filter (skip if "All" is selected)
        if ($gradeLevel !== 'All') {
            $studentsQuery->where('grade_level', $gradeLevel);
        }
        
        if ($request->section) {
            $studentsQuery->where('section', $request->section);
        }

        // Apply gender filter
        if ($request->gender_filter && $request->gender_filter !== 'All') {
            $studentsQuery->where('sex', $request->gender_filter);
        }

        // Apply age range filter
        if ($request->min_age) {
            $studentsQuery->where('age', '>=', $request->min_age);
        }
        if ($request->max_age) {
            $studentsQuery->where('age', '<=', $request->max_age);
        }
        
        // Apply sorting
        $sortBy = $request->sort_by ?? 'Name (A-Z)';
        
        switch ($sortBy) {
            case 'Name (A-Z)':
                $studentsQuery->orderBy('full_name', 'asc');
                break;
            case 'Name (Z-A)':
                $studentsQuery->orderBy('full_name', 'desc');
                break;
            case 'Age (Youngest First)':
                $studentsQuery->orderBy('age', 'asc');
                break;
            case 'Age (Oldest First)':
                $studentsQuery->orderBy('age', 'desc');
                break;
            default:
                $studentsQuery->orderBy('full_name', 'asc');
        }
        
        $students = $studentsQuery->get();

        $reportData = [];

        foreach ($students as $student) {
            $studentData = [];
            
            // Basic student info
            if (in_array('name', $selectedFields)) {
                $studentData['name'] = $student->full_name;
            }
            if (in_array('lrn', $selectedFields)) {
                $studentData['lrn'] = $student->lrn;
            }
            if (in_array('grade_level', $selectedFields)) {
                $studentData['grade_level'] = $student->grade_level;
            }
            if (in_array('section', $selectedFields)) {
                $studentData['section'] = $student->section;
            }
            if (in_array('gender', $selectedFields)) {
                $studentData['gender'] = $student->sex;
            }
            if (in_array('age', $selectedFields)) {
                $studentData['age'] = $student->age;
            }
            if (in_array('birthdate', $selectedFields)) {
                $studentData['birthdate'] = $student->birthdate;
            }

            // Health examination data with selected fields
            if ($request->health_exam_fields && count($request->health_exam_fields) > 0) {
                $healthExam = HealthExamination::where('student_id', $student->id)
                    ->latest()
                    ->first();
                
                $studentData['health_exam'] = $healthExam;
            }

            $reportData[] = $studentData;
        }

        $pdf = PDF::loadView('health-report-pdf', [
            'reportData' => $reportData,
            'grade_level' => 'Grade ' . $gradeLevel,
            'school_year' => $schoolYear,
            'section' => $request->section,
            'fields' => $selectedFields,
            'health_exam_fields' => $request->health_exam_fields ?? [],
            'filters' => [
                'gender' => $request->gender_filter,
                'min_age' => $request->min_age,
                'max_age' => $request->max_age,
                'sort_by' => $request->sort_by
            ]
        ]);

        $filename = 'health-report-' . strtolower(str_replace(' ', '-', $gradeLevel)) . '-' . $schoolYear . '.pdf';
        
        return $pdf->download($filename);
    }

    private function getSchoolYearForGrade($grade)
    {
        $gradeToYear = [
            'Kinder 1' => '2024-2025',
            'Kinder 2' => '2024-2025',
            'Grade 1' => '2023-2024',
            'Grade 2' => '2022-2023',
            'Grade 3' => '2021-2022',
            'Grade 4' => '2020-2021',
            'Grade 5' => '2019-2020',
            'Grade 6' => '2018-2019'
        ];
        
        return $gradeToYear[$grade] ?? '2024-2025';
    }
}
