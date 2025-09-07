<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\OralHealthTreatment;
use App\Models\OralHealthExamination;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class OralHealthReportController extends Controller
{
    public function index()
    {
        // Get dynamic grade levels from database
        $dbGradeLevels = Student::distinct('grade_level')
            ->whereNotNull('grade_level')
            ->pluck('grade_level')
            ->toArray();
        
        $standardGradeLevels = [
            'Kinder 1', 'Kinder 2', 
            'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6'
        ];
        
        $allGradeLevels = array_unique(array_merge($standardGradeLevels, $dbGradeLevels));
        
        // Sort grade levels
        usort($allGradeLevels, function($a, $b) {
            $order = [
                'Kinder 1' => 1, 'Kinder 2' => 2,
                'Grade 1' => 3, 'Grade 2' => 4, 'Grade 3' => 5, 'Grade 4' => 6, 'Grade 5' => 7, 'Grade 6' => 8
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
        
        return Inertia::render('OralHealthReport/Index', [
            'gradeLevels' => array_values($allGradeLevels)
        ]);
    }

    public function generate(Request $request)
    {
        $request->validate([
            'grade_level' => 'required|string',
            'section' => 'nullable|string',
            'gender_filter' => 'nullable|string',
            'min_age' => 'nullable|integer',
            'max_age' => 'nullable|integer',
            'sort_by' => 'nullable|string',
            'fields' => 'nullable|array',
            'oral_exam_fields' => 'nullable|array',
            'minValues' => 'nullable|array',
            'maxValues' => 'nullable|array'
        ]);

        $gradeLevel = $request->grade_level;
        $selectedFields = $request->fields ?? ['name', 'lrn', 'grade_level', 'section', 'gender', 'age', 'birthdate'];
        
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
            
            // Basic student info - only include selected fields
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
                $studentData['birthdate'] = $student->birthdate ? $student->birthdate->format('Y-m-d') : null;
            }

            // Get oral health examination data
            $oralHealth = OralHealthExamination::where('student_id', $student->id)
                ->latest()
                ->first();

            // Add oral health examination data if fields are selected
            $oralExamFields = $request->oral_exam_fields ?? [];
            if (!empty($oralExamFields)) {
                $fieldMapping = [
                    'decayed_teeth' => 'permanent_teeth_decayed',
                    'missing_teeth' => 'permanent_for_extraction', 
                    'filled_teeth' => 'permanent_teeth_filled',
                    'total_dmft' => 'permanent_total_dft',
                    'sealant' => 'permanent_index_dft',
                    'fluoride_application' => 'temporary_total_dft'
                ];
                
                foreach ($oralExamFields as $field) {
                    $dbField = $fieldMapping[$field] ?? $field;
                    $studentData[$field] = $oralHealth ? ($oralHealth->$dbField ?? 0) : 0;
                }
            }
            
            $reportData[] = $studentData;
        }

        return Inertia::render('OralHealthReport/Results', [
            'reportData' => $reportData,
            'grade_level' => $gradeLevel,
            'section' => $request->section,
            'fields' => $selectedFields,
            'oral_exam_fields' => $request->oral_exam_fields ?? [],
            'gender_filter' => $request->gender_filter,
            'min_age' => $request->min_age,
            'max_age' => $request->max_age,
            'sort_by' => $request->sort_by,
            'minValues' => $request->minValues ?? [],
            'maxValues' => $request->maxValues ?? []
        ]);
    }

    public function exportPdf(Request $request)
    {
        $request->validate([
            'grade_level' => 'required|string',
            'section' => 'nullable|string',
            'gender_filter' => 'nullable|string',
            'min_age' => 'nullable|integer',
            'max_age' => 'nullable|integer',
            'sort_by' => 'nullable|string',
            'minValues' => 'nullable|array',
            'maxValues' => 'nullable|array'
        ]);

        $gradeLevel = $request->grade_level;
        $minValues = $request->minValues ?? [];
        $maxValues = $request->maxValues ?? [];
        
        // Default student fields to include
        $selectedFields = ['name', 'lrn', 'grade_level', 'section', 'gender', 'age', 'birthdate'];
        
        // Get all oral exam fields that have ranges set
        $oralExamFields = [];
        if (!empty($minValues) || !empty($maxValues)) {
            $oralExamFields = array_unique(array_merge(array_keys($minValues), array_keys($maxValues)));
        }

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
            $studentData = [
                'name' => $student->full_name,
                'lrn' => $student->lrn,
                'grade_level' => $student->grade_level,
                'section' => $student->section,
                'gender' => $student->sex,
                'age' => $student->age,
                'birthdate' => $student->birthdate ? $student->birthdate->format('Y-m-d') : null,
            ];

            // Get oral health examination data
            $oralHealth = OralHealthExamination::where('student_id', $student->id)
                ->latest()
                ->first();
            
            // Apply min/max filters if specified
            $includeStudent = true;
            
            // Only apply filtering if there are actual non-null values
            $hasFilters = false;
            foreach ($minValues as $value) {
                if ($value !== null) {
                    $hasFilters = true;
                    break;
                }
            }
            if (!$hasFilters) {
                foreach ($maxValues as $value) {
                    if ($value !== null) {
                        $hasFilters = true;
                        break;
                    }
                }
            }
            
            if ($hasFilters) {
                // Map frontend field names to database column names
                $fieldMapping = [
                    'decayed_teeth' => 'permanent_teeth_decayed',
                    'missing_teeth' => 'permanent_for_extraction', 
                    'filled_teeth' => 'permanent_teeth_filled',
                    'total_dmft' => 'permanent_total_dft',
                    'sealant' => 'permanent_index_dft',
                    'fluoride_application' => 'temporary_total_dft'
                ];
                
                foreach ($oralExamFields as $field) {
                    $min = $minValues[$field] ?? null;
                    $max = $maxValues[$field] ?? null;
                    
                    // Skip if both min and max are null
                    if ($min === null && $max === null) {
                        continue;
                    }
                    
                    // Get the actual database column name
                    $dbField = $fieldMapping[$field] ?? $field;
                    
                    // If no oral health data, treat as 0 for all fields
                    $value = $oralHealth ? ($oralHealth->$dbField ?? 0) : 0;
                    
                    // Convert to integer for comparison
                    $value = (int) $value;
                    
                    // Check min value
                    if ($min !== null && $value < $min) {
                        $includeStudent = false;
                        break;
                    }
                    
                    // Check max value
                    if ($max !== null && $value > $max) {
                        $includeStudent = false;
                        break;
                    }
                }
            }
            
            if ($includeStudent) {
                $studentData['oral_health'] = $oralHealth;
                $reportData[] = $studentData;
            }
        }

        $pdf = PDF::loadView('oral-health-report-pdf', [
            'reportData' => $reportData,
            'grade_level' => 'Grade ' . $gradeLevel,
            'section' => $request->section,
            'fields' => $selectedFields,
            'oral_exam_fields' => $request->oral_exam_fields ?? [],
            'minValues' => $minValues,
            'maxValues' => $maxValues
        ]);

        $filename = 'oral-health-report-' . strtolower(str_replace(' ', '-', $gradeLevel)) . '.pdf';
        
        return $pdf->download($filename);
    }
}
