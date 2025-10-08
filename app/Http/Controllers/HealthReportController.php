<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Student;
use App\Models\HealthTreatment;
use App\Models\OralHealthTreatment;
use App\Models\HealthExamination;
use App\Models\OralHealthExamination;
use App\Models\SchoolSettings;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
            'gradeLevels' => array_values($allGradeLevels),
            'userRole' => auth()->user()->role
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
            'selected_students' => 'nullable|array',
        ]);

        $gradeLevel = $request->grade_level;
        $schoolYear = $request->school_year;
        $selectedFields = $request->fields;

        // Check if specific students are selected
        if ($request->selected_students && count($request->selected_students) > 0) {
            // Extract student IDs from the student objects
            $studentIds = collect($request->selected_students)->pluck('id')->toArray();
            
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
                
                if ($healthExam) {
                    $healthData = [];
                    foreach ($request->health_exam_fields as $field) {
                        // Check if the field exists in the HealthExamination model
                        if (in_array($field, (new HealthExamination())->getFillable())) {
                            $healthData[$field] = $healthExam->$field ?? 'N/A';
                        } else {
                            $healthData[$field] = 'N/A';
                        }
                    }
                    $studentData['health_exam'] = $healthData;
                } else {
                    // No health exam found, set all fields to N/A
                    $healthData = [];
                    foreach ($request->health_exam_fields as $field) {
                        $healthData[$field] = 'N/A';
                    }
                    $studentData['health_exam'] = $healthData;
                }
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
            'sort_by' => $request->sort_by,
            'selected_students' => $request->selected_students ?? []
        ]);
    }

    public function exportPdf(Request $request)
    {
        Log::info('PDF Export started', $request->all());
        
        try {
            $request->validate([
                'grade_level' => 'nullable|string',
                'school_year' => 'nullable|string',
                'section' => 'nullable|string',
                'fields' => 'nullable|array',
                'health_exam_fields' => 'nullable|array',
                'gender_filter' => 'nullable|string',
                'min_age' => 'nullable|integer',
                'max_age' => 'nullable|integer',
                'sort_by' => 'nullable|string',
                'selected_students' => 'nullable|array',
            ]);

            // Ensure either grade_level or selected_students is provided
            if (!$request->grade_level && (!$request->selected_students || count($request->selected_students) === 0)) {
                return response()->json([
                    'error' => 'Either grade level or selected students must be provided'
                ], 400);
            }

            $gradeLevel = $request->grade_level;
            $schoolYear = $request->school_year;
            $selectedFields = $request->fields ?? ['name', 'lrn', 'grade_level', 'section', 'gender', 'age'];

            // Check if specific students are selected (same logic as generate method)
            if ($request->selected_students && count($request->selected_students) > 0) {
                // Extract student IDs from the student objects (same as generate method)
                $studentIds = collect($request->selected_students)->pluck('id')->filter()->toArray();
                
                // If no IDs found, treat as plain array of IDs
                if (empty($studentIds)) {
                    $studentIds = $request->selected_students;
                }
                
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
            }
            
            // Apply sorting
            switch ($request->sort_by) {
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
                    
                    if ($healthExam) {
                        $healthData = [];
                        foreach ($request->health_exam_fields as $field) {
                            // Check if the field exists in the HealthExamination model
                            if (in_array($field, (new HealthExamination())->getFillable())) {
                                $healthData[$field] = $healthExam->$field ?? 'N/A';
                            } else {
                                $healthData[$field] = 'N/A';
                            }
                        }
                        $studentData['health_exam'] = $healthData;
                    } else {
                        // No health exam found, set all fields to N/A
                        $healthData = [];
                        foreach ($request->health_exam_fields as $field) {
                            $healthData[$field] = 'N/A';
                        }
                        $studentData['health_exam'] = $healthData;
                    }
                }

                $reportData[] = $studentData;
            }

            // Get school settings for PDF header
            $schoolSettings = SchoolSettings::getInstance();
            
            // Generate server-side PDF using Blade template (like oral health report)
            $pdf = PDF::loadView('health-report-pdf', [
                'reportData' => $reportData,
                'grade_level' => $gradeLevel,
                'school_year' => $schoolYear,
                'section' => $request->section,
                'fields' => $selectedFields,
                'health_exam_fields' => $request->health_exam_fields ?? [],
                'selected_students' => $request->selected_students ?? [],
                'user_name' => auth()->user()->full_name ?? 'System',
                'schoolSettings' => $schoolSettings
            ]);
            
            $filename = 'health-report-grade-' . ($gradeLevel ?: 'selected') . '.pdf';
            return $pdf->stream($filename);
            
        } catch (\Exception $e) {
            Log::error('PDF Export failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'error' => 'PDF generation failed', 
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }

    public function searchStudents(Request $request)
    {
        try {
            $query = $request->get('query', '');
            
            // For teachers with empty query, return all assigned students
            $user = auth()->user();
            if (strlen($query) < 1) {
                if ($user && $user->role === 'teacher') {
                    // Return all assigned students for teachers
                    $assignedStudentIds = $user->assignedStudents()->pluck('student_id');
                    if ($assignedStudentIds->isEmpty()) {
                        return response()->json([]);
                    }
                    
                    $students = Student::whereIn('id', $assignedStudentIds)
                        ->select('id', 'full_name', 'lrn', 'grade_level')
                        ->orderBy('full_name')
                        ->get();
                        
                    $result = $students->map(function($student) {
                        return [
                            'id' => $student->id,
                            'name' => $student->full_name,
                            'lrn' => $student->lrn,
                            'grade_level' => $student->grade_level,
                            'section' => 'N/A',
                            'display_text' => $student->full_name . ' (LRN: ' . $student->lrn . ')'
                        ];
                    });
                    
                    return response()->json($result);
                }
                return response()->json([]);
            }
            
            Log::info("Searching for: '{$query}'");
            
            $user = auth()->user();
            
            Log::info("Search Debug - User Info:", [
                'user_id' => $user ? $user->id : 'null',
                'user_role' => $user ? $user->role : 'null',
                'is_authenticated' => auth()->check()
            ]);
            
            // Build base query with search criteria
            $studentsQuery = Student::where(function($q) use ($query) {
                $q->whereRaw('LOWER(full_name) LIKE ?', ['%' . strtolower($query) . '%'])
                  ->orWhereRaw('LOWER(lrn) LIKE ?', ['%' . strtolower($query) . '%']);
            });
            
            // Filter by teacher assignments if user is a teacher
            if ($user->role === 'teacher') {
                $assignedStudentIds = $user->assignedStudents()->pluck('student_id');
                Log::info("Teacher search - filtering by assigned students:", ['assigned_ids' => $assignedStudentIds->toArray()]);
                
                if ($assignedStudentIds->isEmpty()) {
                    return response()->json([]);
                }
                
                $studentsQuery->whereIn('id', $assignedStudentIds);
            }
            
            $students = $studentsQuery->select('id', 'full_name', 'lrn', 'grade_level')
                ->orderBy('full_name')
                ->limit(20)
                ->get();
            
            Log::info("Search results:", [
                'user_role' => $user->role,
                'total_found' => count($students),
                'student_names' => $students->pluck('full_name')->toArray()
            ]);
            
            $result = $students->map(function($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->full_name,
                    'lrn' => $student->lrn,
                    'grade_level' => $student->grade_level,
                    'section' => 'N/A', // Section column doesn't exist in students table
                    'display_text' => $student->full_name . ' (LRN: ' . $student->lrn . ')'
                ];
            });
            
            return response()->json($result);
            
        } catch (\Exception $e) {
            Log::error('Student search error: ' . $e->getMessage());
            
            // Fallback to basic query without section if there's an issue
            try {
                $students = Student::whereRaw('LOWER(full_name) LIKE ?', ['%' . strtolower($query) . '%'])
                    ->orWhereRaw('LOWER(lrn) LIKE ?', ['%' . strtolower($query) . '%'])
                    ->select('id', 'full_name', 'lrn', 'grade_level')
                    ->orderBy('full_name')
                    ->limit(20)
                    ->get();
                
                $result = $students->map(function($student) {
                    return [
                        'id' => $student->id,
                        'name' => $student->full_name,
                        'lrn' => $student->lrn,
                        'grade_level' => $student->grade_level,
                        'section' => 'N/A',
                        'display_text' => $student->full_name . ' (LRN: ' . $student->lrn . ')'
                    ];
                });
                
                return response()->json($result);
                
            } catch (\Exception $e2) {
                Log::error('Student search fallback error: ' . $e2->getMessage());
                return response()->json(['error' => 'Search failed'], 500);
            }
        }
    }

    public function exportHealthExaminationPdf($studentId)
    {
        $student = Student::findOrFail($studentId);
        
        // Get health examinations for this student
        $healthExaminations = HealthExamination::where('student_id', $studentId)->get();
        
        // Create ordered examinations array with flexible grade level matching
        $orderedExaminations = [];
        $gradeOrder = ['Kinder', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6', 'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'];
        
        foreach ($gradeOrder as $grade) {
            // Try to find examination with exact match first
            $exam = $healthExaminations->firstWhere('grade_level', $grade);
            
            // If not found, try variations (e.g., "6" for "Grade 6")
            if (!$exam) {
                $gradeNumber = str_replace('Grade ', '', $grade);
                $exam = $healthExaminations->firstWhere('grade_level', $gradeNumber);
            }
            
            $orderedExaminations[$grade] = $exam;
        }
        
        // Get health treatments for this student
        $healthTreatments = HealthTreatment::where('student_id', $studentId)
            ->orderBy('date', 'desc')
            ->get();
        
        $pdf = PDF::loadView('health-examination-pdf', compact('student', 'orderedExaminations', 'healthTreatments'));
        
        return $pdf->stream('health-examination-' . $student->lrn . '.pdf');
    }

    public function exportOralHealthExaminationPdf($studentId)
    {
        $student = Student::findOrFail($studentId);
        
        // Get oral health data for all grade levels
        $oralHealthDataByGrade = [];
        $gradeNames = ['Kinder', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6'];
        $gradeNumbers = ['K', '1', '2', '3', '4', '5', '6'];
        
        foreach ($gradeNumbers as $index => $gradeNum) {
            $gradeName = $gradeNames[$index];
            
            // Try different grade level formats that might be stored in database
            $oralHealthData = OralHealthExamination::where('student_id', $studentId)
                ->where(function($query) use ($gradeNum, $gradeName) {
                    $query->where('grade_level', $gradeNum)
                          ->orWhere('grade_level', $gradeName)
                          ->orWhere('grade_level', strtolower($gradeName))
                          ->orWhere('grade_level', ucfirst(strtolower($gradeName)));
                })
                ->first();
            
            // Process the tooth_symbols JSON data if it exists
            if ($oralHealthData && $oralHealthData->tooth_symbols) {
                $toothSymbols = is_string($oralHealthData->tooth_symbols) 
                    ? json_decode($oralHealthData->tooth_symbols, true) 
                    : $oralHealthData->tooth_symbols;
                
                // Map tooth symbols to individual tooth fields
                if ($toothSymbols) {
                    foreach ($toothSymbols as $toothNumber => $symbols) {
                        $symbolValue = is_array($symbols) ? implode('', $symbols) : $symbols;
                        
                        // Determine if it's permanent or temporary tooth
                        if (in_array($toothNumber, ['55', '54', '53', '52', '51', '61', '62', '63', '64', '65', '85', '84', '83', '82', '81', '71', '72', '73', '74', '75'])) {
                            $oralHealthData->{"temp_$toothNumber"} = $symbolValue;
                        } else {
                            $oralHealthData->{"perm_$toothNumber"} = $symbolValue;
                        }
                    }
                }
            }
                
            $oralHealthDataByGrade[$gradeNum] = $oralHealthData;
        }
        
        // Get oral health treatments for all grade levels
        $oralHealthTreatmentsByGrade = [];
        foreach ($gradeNumbers as $index => $gradeNum) {
            $gradeName = $gradeNames[$index];
            
            $treatments = \App\Models\OralHealthTreatment::where('student_id', $studentId)
                ->where(function($query) use ($gradeNum, $gradeName) {
                    $query->where('grade_level', $gradeNum)
                          ->orWhere('grade_level', $gradeName)
                          ->orWhere('grade_level', strtolower($gradeName))
                          ->orWhere('grade_level', ucfirst(strtolower($gradeName)));
                })
                ->orderBy('date', 'asc')
                ->get();
                
            $oralHealthTreatmentsByGrade[$gradeNum] = $treatments;
        }
        
        Log::info('Oral Health Data Retrieved:', $oralHealthDataByGrade);
        Log::info('Oral Health Treatments Retrieved:', $oralHealthTreatmentsByGrade);
        
        $pdf = PDF::loadView('oral-health-examination-pdf', compact('student', 'oralHealthDataByGrade', 'oralHealthTreatmentsByGrade'));
        
        return $pdf->stream('oral-health-examination-' . $student->lrn . '.pdf');
    }

    public function testHealthExaminationPdf($studentId = null)
    {
        // Use a specific student ID or get the first student for testing
        $student = $studentId ? Student::find($studentId) : Student::first();
        
        if (!$student) {
            return response('No student found', 404);
        }
        
        // Define all possible grade levels in order
        $allGradeLevels = ['Kinder', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6', 'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'];
        
        // Get all health examinations for this student
        $healthExaminations = HealthExamination::where('student_id', $student->id)
            ->get()
            ->keyBy('grade_level'); // Key by grade level for easy lookup
        
        // Create an ordered array with data only for grades where student has records
        $orderedExaminations = [];
        foreach ($allGradeLevels as $grade) {
            if (isset($healthExaminations[$grade])) {
                $orderedExaminations[$grade] = $healthExaminations[$grade];
            } else {
                $orderedExaminations[$grade] = null; // No data for this grade
            }
        }
        
        return view('health-examination-pdf', compact('student', 'orderedExaminations', 'allGradeLevels'));
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
