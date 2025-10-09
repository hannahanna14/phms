<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\OralHealthExamination;
use App\Models\SchoolSettings;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
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
            'Kinder 2', 
            'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6'
        ];
        
        $allGradeLevels = array_unique(array_merge($standardGradeLevels, $dbGradeLevels));
        
        // Sort grade levels
        usort($allGradeLevels, function($a, $b) {
            $order = [
                'Kinder 2' => 1,
                'Grade 1' => 2, 'Grade 2' => 3, 'Grade 3' => 4, 'Grade 4' => 5, 'Grade 5' => 6, 'Grade 6' => 7
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
            'gradeLevels' => array_values($allGradeLevels),
            'userRole' => auth()->user()->role
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
            // Extract student IDs - handle both object format and plain ID format
            $studentIds = collect($request->selected_students)
                ->map(function($student) {
                    if (is_array($student) && isset($student['id'])) {
                        return $student['id'];
                    } elseif (is_object($student) && isset($student->id)) {
                        return $student->id;
                    } else {
                        return $student; // Plain ID from frontend
                    }
                })
                ->filter(function($id) {
                    return !empty($id) && is_numeric($id);
                })
                ->toArray();
            
            \Log::info('Selected student IDs for PDF:', [
                'raw_selected_students' => $request->selected_students,
                'extracted_ids' => $studentIds
            ]);
            
            // Use selected students
            $user = auth()->user();
            
            if ($user->role === 'teacher') {
                // Teachers can only see their assigned students
                $assignedStudentIds = $user->assignedStudents()->pluck('student_id');
                $studentsQuery = Student::whereIn('id', $studentIds)
                    ->whereIn('id', $assignedStudentIds);
            } else {
                // Admins and nurses can see all students
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
                // Admins and nurses can see all students
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
            
            // Add oral health data for display - only selected fields
            $selectedOralFields = $request->oral_exam_fields ?? [];
            
            // If no specific oral fields selected, include all for backward compatibility
            if (empty($selectedOralFields)) {
                $selectedOralFields = [
                    'permanent_teeth_filled', 'permanent_teeth_decayed', 'permanent_for_extraction', 
                    'permanent_for_filling', 'permanent_index_dft', 'temporary_teeth_filled',
                    'temporary_teeth_decayed', 'temporary_for_extraction', 'temporary_for_filling', 
                    'temporary_index_dft'
                ];
            }
            
            // Add only selected oral health fields
            foreach ($selectedOralFields as $field) {
                if ($oralHealth) {
                    $studentData[$field] = $oralHealth->$field ?? 0;
                } else {
                    $studentData[$field] = 0;
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
        
        // Use selected oral exam fields from request, or default to all if none specified
        $requestedOralFields = $request->oral_exam_fields ?? [];
        
        if (!empty($requestedOralFields)) {
            // Use only the fields selected by user
            $oralExamFields = $requestedOralFields;
        } else {
            // Default to all fields if none specified
            $oralExamFields = [
                'permanent_teeth_decayed',
                'permanent_teeth_filled', 
                'permanent_for_extraction',
                'permanent_for_filling',
                'temporary_teeth_decayed',
                'temporary_teeth_filled',
                'temporary_for_extraction', 
                'temporary_for_filling'
            ];
        }
        
        // Get fields that have filter values set (for filtering logic only)
        $fieldsWithFilters = [];
        if (!empty($minValues) || !empty($maxValues)) {
            // Check minValues for actual non-null values
            foreach ($minValues as $field => $value) {
                if ($value !== null && $value !== "" && $value !== "null") {
                    $fieldsWithFilters[] = $field;
                }
            }
            
            // Check maxValues for actual non-null values
            foreach ($maxValues as $field => $value) {
                if ($value !== null && $value !== "" && $value !== "null") {
                    $fieldsWithFilters[] = $field;
                }
            }
            
            $fieldsWithFilters = array_unique($fieldsWithFilters);
        }

        // Check if specific students are selected
        if ($request->selected_students && count($request->selected_students) > 0) {
            // Extract student IDs - handle both object format and plain ID format
            $studentIds = collect($request->selected_students)
                ->map(function($student) {
                    if (is_array($student) && isset($student['id'])) {
                        return $student['id'];
                    } elseif (is_object($student) && isset($student->id)) {
                        return $student->id;
                    } else {
                        return $student; // Plain ID from frontend
                    }
                })
                ->filter(function($id) {
                    return !empty($id) && is_numeric($id);
                })
                ->toArray();
            
            \Log::info('Selected student IDs for PDF:', [
                'raw_selected_students' => $request->selected_students,
                'extracted_ids' => $studentIds
            ]);
            
            // Use selected students
            $user = auth()->user();
            
            if ($user->role === 'teacher') {
                // Teachers can only see their assigned students
                $assignedStudentIds = $user->assignedStudents()->pluck('student_id');
                $studentsQuery = Student::whereIn('id', $studentIds)
                    ->whereIn('id', $assignedStudentIds);
            } else {
                // Admins and nurses can see all students
                $studentsQuery = Student::whereIn('id', $studentIds);
            }
        } else {
            // Use filter-based approach (same logic as generate method)
            $user = auth()->user();
            
            // Filter students based on user role
            if ($user->role === 'teacher') {
                // Teachers can only see their assigned students
                $assignedStudentIds = $user->assignedStudents()->pluck('student_id');
                \Log::info('Teacher PDF Debug:', [
                    'teacher_id' => $user->id,
                    'teacher_name' => $user->full_name,
                    'assigned_student_ids' => $assignedStudentIds->toArray(),
                    'assigned_count' => $assignedStudentIds->count()
                ]);
                $studentsQuery = Student::whereIn('id', $assignedStudentIds);
            } else {
                // Admins and nurses can see all students
                \Log::info('Admin PDF Debug:', [
                    'admin_id' => $user->id,
                    'admin_name' => $user->full_name
                ]);
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
        
        \Log::info('PDF Export Debug:', [
            'students_count' => $students->count(),
            'grade_level' => $gradeLevel,
            'section' => $request->section,
            'selected_students' => $request->selected_students ? count($request->selected_students) : 0,
            'query_sql' => $studentsQuery->toSql()
        ]);

        $reportData = [];

        foreach ($students as $student) {
            \Log::info('Processing student for PDF:', [
                'student_id' => $student->id,
                'student_name' => $student->full_name
            ]);
            
            $studentData = [
                'name' => $student->full_name,
                'lrn' => $student->lrn,
                'grade_level' => $student->grade_level,
                'section' => $student->section,
                'gender' => $student->sex,
                'age' => $student->age,
                'birthdate' => $student->birthdate ? $student->birthdate->format('Y-m-d') : null,
            ];

            // Get oral health examination data for the student's current grade level
            $oralHealth = OralHealthExamination::where('student_id', $student->id)
                ->where('grade_level', $student->grade_level)
                ->latest()
                ->first();
            
            // If no examination found for current grade, fall back to latest examination
            if (!$oralHealth) {
                $oralHealth = OralHealthExamination::where('student_id', $student->id)
                    ->latest()
                    ->first();
            }
            
            \Log::info('Oral Health Data Debug:', [
                'student_id' => $student->id,
                'student_name' => $student->full_name,
                'oral_health_found' => $oralHealth ? 'yes' : 'no',
                'examination_grade' => $oralHealth ? $oralHealth->grade_level : 'no data',
                'examination_date' => $oralHealth ? $oralHealth->examination_date : 'no data',
                'permanent_for_filling' => $oralHealth ? $oralHealth->permanent_for_filling : 'no data',
                'temporary_for_filling' => $oralHealth ? $oralHealth->temporary_for_filling : 'no data'
            ]);
            
            // Apply min/max filters if specified - but skip filtering for selected students
            $includeStudent = true;
            
            // Skip filtering if specific students are selected
            $isSelectedStudentMode = $request->selected_students && count($request->selected_students) > 0;
            
            if (!$isSelectedStudentMode) {
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
            } else {
                $hasFilters = false; // No filtering for selected students
            }
            
            if ($hasFilters) {
                // Map frontend field names to database column names
                $fieldMapping = [
                    'permanent_teeth_decayed' => 'permanent_teeth_decayed',
                    'permanent_teeth_filled' => 'permanent_teeth_filled',
                    'permanent_for_extraction' => 'permanent_for_extraction',
                    'permanent_for_filling' => 'permanent_for_filling',
                    'temporary_teeth_decayed' => 'temporary_teeth_decayed',
                    'temporary_teeth_filled' => 'temporary_teeth_filled',
                    'temporary_for_extraction' => 'temporary_for_extraction',
                    'temporary_for_filling' => 'temporary_for_filling',
                    // Legacy mappings
                    'decayed_teeth' => 'permanent_teeth_decayed',
                    'missing_teeth' => 'permanent_for_extraction', 
                    'filled_teeth' => 'permanent_teeth_filled',
                    'total_dmft' => 'permanent_total_dft',
                    'sealant' => 'permanent_index_dft',
                    'fluoride_application' => 'temporary_total_dft'
                ];
                
                foreach ($fieldsWithFilters as $field) {
                    $min = $minValues[$field] ?? null;
                    $max = $maxValues[$field] ?? null;
                    
                    // Convert string "null" to actual null
                    if ($min === "null" || $min === "") $min = null;
                    if ($max === "null" || $max === "") $max = null;
                    
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
                    
                    \Log::info('Filtering student:', [
                        'student_id' => $student->id,
                        'field' => $field,
                        'db_field' => $dbField,
                        'value' => $value,
                        'min' => $min,
                        'max' => $max
                    ]);
                    
                    // Check min value
                    if ($min !== null && $value < $min) {
                        \Log::info('Student excluded by min filter:', [
                            'student_id' => $student->id,
                            'field' => $field,
                            'value' => $value,
                            'min' => $min
                        ]);
                        $includeStudent = false;
                        break;
                    }
                    
                    // Check max value
                    if ($max !== null && $value > $max) {
                        \Log::info('Student excluded by max filter:', [
                            'student_id' => $student->id,
                            'field' => $field,
                            'value' => $value,
                            'max' => $max
                        ]);
                        $includeStudent = false;
                        break;
                    }
                }
            }
            
            if ($includeStudent) {
                // Add oral health examination data in the structure expected by the template
                $oralHealthData = [];
                foreach ($oralExamFields as $field) {
                    if ($oralHealth) {
                        $oralHealthData[$field] = $oralHealth->$field ?? 0;
                    } else {
                        $oralHealthData[$field] = 0;
                    }
                }
                $studentData['oral_health_examination'] = $oralHealthData;
                
                \Log::info('Adding student to reportData:', [
                    'student_id' => $student->id,
                    'student_name' => $studentData['name'],
                    'oral_health_data' => $oralHealthData,
                    'include_student' => $includeStudent
                ]);
                
                $reportData[] = $studentData;
            } else {
                \Log::info('Student excluded from report:', [
                    'student_id' => $student->id,
                    'student_name' => $student->full_name,
                    'include_student' => $includeStudent
                ]);
            }
        }

        \Log::info('Final reportData for PDF:', [
            'reportData_count' => count($reportData),
            'reportData_sample' => count($reportData) > 0 ? $reportData[0] : 'empty',
            'oral_exam_fields_being_returned' => $oralExamFields,
            'request_oral_exam_fields' => $request->oral_exam_fields,
            'all_request_data' => $request->all()
        ]);

        // Get school settings for PDF header
        $schoolSettings = \App\Models\SchoolSettings::getInstance();
        
        // Generate server-side PDF using Blade template (like health examination)
        $pdf = PDF::loadView('oral-health-report-pdf', [
            'reportData' => $reportData,
            'grade_level' => $gradeLevel,
            'section' => $request->section,
            'fields' => $selectedFields,
            'oral_exam_fields' => $oralExamFields,
            'selected_students' => $request->selected_students ?? [],
            'user_name' => auth()->user()->full_name ?? 'System',
            'schoolSettings' => $schoolSettings
        ]);
        
        $filename = 'oral-health-report-grade-' . ($gradeLevel ?: 'selected') . '.pdf';
        return $pdf->stream($filename);
        
    } catch (\Exception $e) {
        \Log::error('PDF Export failed: ' . $e->getMessage());
        return response()->json([
            'error' => 'PDF generation failed', 
            'message' => $e->getMessage()
        ], 500);
    }
}
}
