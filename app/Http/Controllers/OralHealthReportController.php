<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\OralHealthExamination;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class OralHealthReportController extends Controller
{
    public function index()
    {
        // Get dynamic grade levels from database and normalize them
        $dbGradeLevels = Student::distinct('grade_level')
            ->whereNotNull('grade_level')
            ->where('grade_level', '!=', '')
            ->pluck('grade_level')
            ->map(function($grade) {
                // Normalize grade levels - convert "4" to "Grade 4"
                if (is_numeric($grade)) {
                    return "Grade " . $grade;
                }
                return $grade;
            })
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
            'grade_level' => 'nullable|string',
            'section' => 'nullable|string',
            'gender_filter' => 'nullable|string',
            'min_age' => 'nullable|integer',
            'max_age' => 'nullable|integer',
            'sort_by' => 'nullable|string',
            'fields' => 'nullable|array',
            'oral_exam_fields' => 'nullable|array',
            'minValues' => 'nullable|array',
            'maxValues' => 'nullable|array',
            'selected_students' => 'nullable|array'
        ]);

        $gradeLevel = $request->grade_level;
        $selectedFields = $request->fields ?? ['name', 'lrn', 'grade_level', 'section', 'gender', 'age', 'birthdate'];
        
        // Check if specific students are selected
        if ($request->selected_students && count($request->selected_students) > 0) {
            // Extract student IDs from the student objects
            $studentIds = collect($request->selected_students)
                ->filter(function($student) {
                    return isset($student['id']) && is_numeric($student['id']);
                })
                ->pluck('id')
                ->toArray();
            
            // Use selected students
            $user = auth()->user();
            
            if ($user->role === 'teacher') {
                // Teachers can only see their assigned students
                $assignedStudentIds = $user->assignedStudents()->pluck('student_id');
                $studentsQuery = Student::whereIn('id', $studentIds)
                    ->whereIn('id', $assignedStudentIds);
            } else {
                // Admins can see all students
                $studentsQuery = Student::whereIn('id', $studentIds);
            }
        } else {
            // Use filter-based approach
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
            if ($gradeLevel && $gradeLevel !== 'All') {
                // Handle both "Grade 6" and "6" formats
                $studentsQuery->where(function($query) use ($gradeLevel) {
                    $query->where('grade_level', $gradeLevel);
                    
                    // If grade level is in "Grade X" format, also check for numeric format
                    if (preg_match('/^Grade (\d+)$/', $gradeLevel, $matches)) {
                        $numericGrade = $matches[1];
                        $query->orWhere('grade_level', $numericGrade);
                    }
                    
                    // If grade level is numeric, also check for "Grade X" format
                    if (is_numeric($gradeLevel)) {
                        $query->orWhere('grade_level', 'Grade ' . $gradeLevel);
                    }
                });
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

            // Add oral health examination data based on range filters
            $minValues = $request->minValues ?? [];
            $maxValues = $request->maxValues ?? [];
            $selectedStudents = $request->selected_students ?? [];
            
            // If specific students are selected, skip range filtering
            $includeStudent = true;
            
            if (empty($selectedStudents)) {
                // Get oral exam fields that have ranges set
                $oralExamFields = $request->oral_exam_fields ?? [];
                
                // Only apply filtering if there are oral exam fields specified
                if (!empty($oralExamFields)) {
                    foreach ($oralExamFields as $field) {
                        $minVal = $minValues[$field] ?? null;
                        $maxVal = $maxValues[$field] ?? null;
                        
                        $actualValue = $oralHealth ? ($oralHealth->$field ?? 0) : 0;
                        
                        // Check if value falls within range
                        // For minimum: if min is set and > 0, actual value must be >= min
                        if ($minVal !== null && $minVal > 0 && $actualValue < $minVal) {
                            $includeStudent = false;
                            break;
                        }
                        
                        // For maximum: if max is set and > 0, actual value must be <= max
                        if ($maxVal !== null && $maxVal > 0 && $actualValue > $maxVal) {
                            $includeStudent = false;
                            break;
                        }
                    }
                }
            }
            
            // Only include student if they pass the oral health filters (or if specific students selected)
            if (!$includeStudent) {
                continue;
            }
            
            // Add oral health data for display
            if ($oralHealth) {
                $studentData['permanent_teeth_filled'] = $oralHealth->permanent_teeth_filled ?? 0;
                $studentData['permanent_teeth_decayed'] = $oralHealth->permanent_teeth_decayed ?? 0;
                $studentData['permanent_for_extraction'] = $oralHealth->permanent_for_extraction ?? 0;
                $studentData['permanent_for_filling'] = $oralHealth->permanent_for_filling ?? 0;
                $studentData['permanent_index_dft'] = $oralHealth->permanent_index_dft ?? 0;
                $studentData['temporary_teeth_filled'] = $oralHealth->temporary_teeth_filled ?? 0;
                $studentData['temporary_teeth_decayed'] = $oralHealth->temporary_teeth_decayed ?? 0;
                $studentData['temporary_for_extraction'] = $oralHealth->temporary_for_extraction ?? 0;
                $studentData['temporary_for_filling'] = $oralHealth->temporary_for_filling ?? 0;
                $studentData['temporary_index_dft'] = $oralHealth->temporary_index_dft ?? 0;
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
            'maxValues' => $request->maxValues ?? [],
            'selected_students' => $request->selected_students ?? []
        ]);
    }

    public function exportPdf(Request $request)
    {
        try {
            $request->validate([
                'grade_level' => 'nullable|string',
                'section' => 'nullable|string',
                'gender_filter' => 'nullable|string',
                'min_age' => 'nullable|integer',
                'max_age' => 'nullable|integer',
                'sort_by' => 'nullable|string',
                'minValues' => 'nullable|array',
                'maxValues' => 'nullable|array',
                'selected_students' => 'nullable|array'
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

        // Check if specific students are selected
        if ($request->selected_students && count($request->selected_students) > 0) {
            // Extract student IDs from the student objects
            $studentIds = collect($request->selected_students)
                ->filter(function($student) {
                    return isset($student['id']) && is_numeric($student['id']);
                })
                ->pluck('id')
                ->toArray();
            
            // Use selected students
            $user = auth()->user();
            
            if ($user->role === 'teacher') {
                // Teachers can only see their assigned students
                $assignedStudentIds = $user->assignedStudents()->pluck('student_id');
                $studentsQuery = Student::whereIn('id', $studentIds)
                    ->whereIn('id', $assignedStudentIds);
            } else {
                // Admins can see all students
                $studentsQuery = Student::whereIn('id', $studentIds);
            }
        } else {
            // Use filter-based approach (same logic as generate method)
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
            if ($gradeLevel && $gradeLevel !== 'All') {
                // Handle both "Grade 6" and "6" formats
                $studentsQuery->where(function($query) use ($gradeLevel) {
                    $query->where('grade_level', $gradeLevel);
                    
                    // If grade level is in "Grade X" format, also check for numeric format
                    if (preg_match('/^Grade (\d+)$/', $gradeLevel, $matches)) {
                        $numericGrade = $matches[1];
                        $query->orWhere('grade_level', $numericGrade);
                    }
                    
                    // If grade level is numeric, also check for "Grade X" format
                    if (is_numeric($gradeLevel)) {
                        $query->orWhere('grade_level', 'Grade ' . $gradeLevel);
                    }
                });
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
                // Add oral health data directly to student data
                if ($oralHealth) {
                    $studentData['permanent_teeth_decayed'] = $oralHealth->permanent_teeth_decayed ?? 0;
                    $studentData['permanent_teeth_filled'] = $oralHealth->permanent_teeth_filled ?? 0;
                    $studentData['permanent_for_extraction'] = $oralHealth->permanent_for_extraction ?? 0;
                    $studentData['permanent_for_filling'] = $oralHealth->permanent_for_filling ?? 0;
                    $studentData['temporary_teeth_decayed'] = $oralHealth->temporary_teeth_decayed ?? 0;
                    $studentData['temporary_teeth_filled'] = $oralHealth->temporary_teeth_filled ?? 0;
                    $studentData['temporary_for_extraction'] = $oralHealth->temporary_for_extraction ?? 0;
                    $studentData['temporary_for_filling'] = $oralHealth->temporary_for_filling ?? 0;
                } else {
                    $studentData['permanent_teeth_decayed'] = 0;
                    $studentData['permanent_teeth_filled'] = 0;
                    $studentData['permanent_for_extraction'] = 0;
                    $studentData['permanent_for_filling'] = 0;
                    $studentData['temporary_teeth_decayed'] = 0;
                    $studentData['temporary_teeth_filled'] = 0;
                    $studentData['temporary_for_extraction'] = 0;
                    $studentData['temporary_for_filling'] = 0;
                }
                
                $reportData[] = $studentData;
            }
        }

        // Generate PDF using blade template
        $pdf = PDF::loadView('oral-health-report-pdf', [
            'reportData' => $reportData,
            'grade_level' => $gradeLevel,
            'section' => $request->section,
            'fields' => $selectedFields,
            'oral_exam_fields' => $oralExamFields,
            'user_name' => auth()->user()->name ?? 'System'
        ]);

        $filename = 'oral-health-report-grade-' . $gradeLevel . ($request->section ? '-section-' . $request->section : '') . '.pdf';
        
        return $pdf->download($filename);
        
    } catch (\Exception $e) {
        \Log::error('PDF Export failed: ' . $e->getMessage());
        return response()->json([
            'error' => 'PDF generation failed', 
            'message' => $e->getMessage()
        ], 500);
    }
}
}
