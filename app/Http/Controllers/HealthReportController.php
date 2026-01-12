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
            'Kinder 2',
            'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6', 'Non-Graded',
            '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'
        ];

        // Merge and remove duplicates, then sort
        $allGradeLevels = array_unique(array_merge($standardGradeLevels, $dbGradeLevels));

        // Custom sort to put them in logical order
        usort($allGradeLevels, function($a, $b) {
            // Define order priority
            $order = [
                'Kinder 2' => 1,
                'Grade 1' => 2, 'Grade 2' => 3, 'Grade 3' => 4, 'Grade 4' => 5, 'Grade 5' => 6, 'Grade 6' => 7,
                '1' => 8, '2' => 9, '3' => 10, '4' => 11, '5' => 12, '6' => 13,
                '7' => 14, '8' => 15, '9' => 16, '10' => 17, '11' => 18, '12' => 19
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
                // Calculate current school year
                $currentYear = date('Y');
                $currentMonth = date('n');
                if ($currentMonth >= 6) {
                    $currentSchoolYear = $currentYear . '-' . ($currentYear + 1);
                } else {
                    $currentSchoolYear = ($currentYear - 1) . '-' . $currentYear;
                }

                $studentsQuery = Student::where('is_active', true)
                    ->where('school_year', $currentSchoolYear);
            }

            // Apply grade level filter (skip if "All" is selected)
            if ($gradeLevel !== 'All') {
                // Try both formats: "1" and "Grade 1"
                $studentsQuery->where(function($q) use ($gradeLevel) {
                    $q->where('grade_level', $gradeLevel)
                      ->orWhere('grade_level', 'Grade ' . $gradeLevel);
                });
            }

            // Apply section filter (skip if "All" is selected)
            if ($request->section && $request->section !== 'All') {
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

        // Get students first, then sort by last name if needed
        $sortBy = $request->sort_by ?? 'Name (A-Z)';

        // For grade level sorting, we need to handle it differently
        if (in_array($sortBy, ['Grade Level (Lowest First)', 'Grade Level (Highest First)'])) {
            // Apply grade level sorting
            $students = $studentsQuery->get();
            $students = $this->sortByGradeLevel($students, $sortBy === 'Grade Level (Highest First)');
        } elseif (in_array($sortBy, ['Name (A-Z)', 'Name (Z-A)'])) {
            // Get all students and sort by last name
            $students = $studentsQuery->get();
            $students = $this->sortByLastName($students, $sortBy === 'Name (Z-A)');
        } else {
            // Apply age sorting in database
            switch ($sortBy) {
                case 'Age (Youngest First)':
                    $studentsQuery->orderBy('age', 'asc');
                    break;
                case 'Age (Oldest First)':
                    $studentsQuery->orderBy('age', 'desc');
                    break;
                default:
                    // Default to last name sorting
                    $students = $studentsQuery->get();
                    $students = $this->sortByLastName($students, false);
            }

            if (!isset($students)) {
                $students = $studentsQuery->get();
            }
        }

        $reportData = [];

        foreach ($students as $student) {
            $studentData = [];

            // Basic student info
            if (in_array('name', $selectedFields)) {
                // Format name as "Last Name, First Name Middle Initial"
                $studentData['name'] = $this->formatNameForReport($student->full_name);
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
                            $value = $healthExam->$field ?? 'N/A';

                            // Check if value is "Others (specify)" and replace with the specify field
                            if (stripos($value, 'others') !== false && stripos($value, 'specify') !== false) {
                                $specifyField = $field . '_specify';
                                if (!empty($healthExam->$specifyField)) {
                                    $value = $healthExam->$specifyField;
                                }
                            }

                            $healthData[$field] = $value;
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
                    // Calculate current school year
                    $currentYear = date('Y');
                    $currentMonth = date('n');
                    if ($currentMonth >= 6) {
                        $currentSchoolYear = $currentYear . '-' . ($currentYear + 1);
                    } else {
                        $currentSchoolYear = ($currentYear - 1) . '-' . $currentYear;
                    }

                    $studentsQuery = Student::where('is_active', true)
                        ->where('school_year', $currentSchoolYear);
                }

                // Apply grade level filter (skip if "All" is selected)
                if ($gradeLevel !== 'All') {
                    // Try both formats: "1" and "Grade 1"
                    $studentsQuery->where(function($q) use ($gradeLevel) {
                        $q->where('grade_level', $gradeLevel)
                          ->orWhere('grade_level', 'Grade ' . $gradeLevel);
                    });
                }

                // Apply section filter (skip if "All" is selected)
                if ($request->section && $request->section !== 'All') {
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

            // Apply sorting (same logic as generate method)
            $sortBy = $request->sort_by ?? 'Name (A-Z)';

            if (in_array($sortBy, ['Grade Level (Lowest First)', 'Grade Level (Highest First)'])) {
                $students = $studentsQuery->get();
                $students = $this->sortByGradeLevel($students, $sortBy === 'Grade Level (Highest First)');
            } elseif (in_array($sortBy, ['Name (A-Z)', 'Name (Z-A)'])) {
                $students = $studentsQuery->get();
                $students = $this->sortByLastName($students, $sortBy === 'Name (Z-A)');
            } else {
                switch ($sortBy) {
                    case 'Age (Youngest First)':
                        $studentsQuery->orderBy('age', 'asc');
                        break;
                    case 'Age (Oldest First)':
                        $studentsQuery->orderBy('age', 'desc');
                        break;
                    default:
                        $students = $studentsQuery->get();
                        $students = $this->sortByLastName($students, false);
                }

                if (!isset($students)) {
                    $students = $studentsQuery->get();
                }
            }

            // Debug logging
            Log::info('PDF Export Query Debug', [
                'grade_level' => $gradeLevel,
                'section' => $request->section,
                'sort_by' => $sortBy,
                'student_count' => $students->count()
            ]);

            Log::info('PDF Export Students Found', [
                'count' => $students->count(),
                'student_ids' => $students->pluck('id')->toArray()
            ]);

            $reportData = [];

            foreach ($students as $student) {
                $studentData = [];

                // Basic student info
                if (in_array('name', $selectedFields)) {
                    // Format name as "Last Name, First Name Middle Initial"
                    $studentData['name'] = $this->formatNameForReport($student->full_name);
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
                                $value = $healthExam->$field ?? 'N/A';

                                // Check if value is "Others (specify)" and replace with the specify field
                                if (stripos($value, 'others') !== false && stripos($value, 'specify') !== false) {
                                    $specifyField = $field . '_specify';
                                    if (!empty($healthExam->$specifyField)) {
                                        $value = $healthExam->$specifyField;
                                    }
                                }

                                $healthData[$field] = $value;
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
            // Prevent extremely large PDF generations that exhaust memory
            $studentCount = $students->count();
            $maxPdfStudents = config('reports.max_pdf_students', 800);
            if ($studentCount === 0) {
                return response()->json(['error' => 'No students match the selected filters'], 400);
            }

            if ($studentCount > $maxPdfStudents) {
                Log::warning('PDF Export aborted: too many students', ['count' => $studentCount, 'max' => $maxPdfStudents]);
                return response()->json(['error' => "Too many students to generate PDF (selected: {$studentCount}). Please narrow your filters or use CSV export."], 400);
            }

            // Ensure dompdf temp dir exists to reduce memory pressure
            $dompdfTemp = storage_path('app/dompdf_temp');
            if (!is_dir($dompdfTemp)) {
                try {
                    mkdir($dompdfTemp, 0755, true);
                } catch (\Throwable $e) {
                    Log::warning('Could not create dompdf temp dir: ' . $e->getMessage());
                }
            }

            // Generate server-side PDF using Blade template (like oral health report)
            PDF::setOptions([
                'tempDir' => $dompdfTemp,
                'isPhpEnabled' => false,
                'isRemoteEnabled' => false,
            ]);

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
            // Lower memory pressure by forcing rendering and garbage collection hints
            if (function_exists('gc_collect_cycles')) {
                gc_collect_cycles();
            }

            $filename = 'health-report-grade-' . ($gradeLevel ?: 'selected') . '.pdf';
            return $pdf->download($filename);

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

    public function exportPdfQueued(Request $request)
    {
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
            return response()->json(['error' => 'Either grade level or selected students must be provided'], 400);
        }

        $exportId = \Illuminate\Support\Str::uuid()->toString();

        // Dispatch job
        \App\Jobs\GenerateHealthReportPdf::dispatch($request->all(), auth()->id(), $exportId);

        return response()->json(['status' => 'queued', 'id' => $exportId]);
    }

    public function exportPdfStatus($exportId)
    {
        $zipPath = storage_path('app/exports/' . $exportId . '.zip');
        $tarPath = storage_path('app/exports/' . $exportId . '.tar');
        $tarGzPath = storage_path('app/exports/' . $exportId . '.tar.gz');
        $failedPath = storage_path('app/exports/' . $exportId . '.failed');

        if (file_exists($zipPath)) {
            return response()->json(['ready' => true, 'download_url' => route('health-report.export-pdf.download', $exportId)]);
        }

        if (file_exists($tarGzPath)) {
            return response()->json(['ready' => true, 'download_url' => route('health-report.export-pdf.download', $exportId)]);
        }

        if (file_exists($tarPath)) {
            return response()->json(['ready' => true, 'download_url' => route('health-report.export-pdf.download', $exportId)]);
        }

        if (file_exists($failedPath)) {
            $message = file_get_contents($failedPath);
            return response()->json(['ready' => false, 'failed' => true, 'message' => $message]);
        }

        return response()->json(['ready' => false]);
    }

    public function exportPdfDownload($exportId)
    {
        $candidates = [
            storage_path('app/exports/' . $exportId . '.zip'),
            storage_path('app/exports/' . $exportId . '.tar.gz'),
            storage_path('app/exports/' . $exportId . '.tar'),
        ];

        foreach ($candidates as $path) {
            if (file_exists($path)) {
                return response()->download($path, basename($path));
            }
        }

        return response()->json(['error' => 'Export not ready or not found'], 404);
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
                // Always search by LRN (anywhere in LRN)
                $q->whereRaw('LOWER(lrn) LIKE ?', ['%' . strtolower($query) . '%']);

                // For single character: only match first letter of first name or last name
                if (strlen($query) === 1) {
                    $q->orWhere(function($subQ) use ($query) {
                        // Match first letter of first name (name starts with this letter)
                        $subQ->whereRaw('LOWER(full_name) LIKE ?', [strtolower($query) . '%'])
                             // Match first letter after a space (last name starts with this letter)
                             ->orWhereRaw('LOWER(full_name) LIKE ?', ['% ' . strtolower($query) . '%']);
                    });
                } else {
                    // For multi-character: search anywhere in name
                    $q->orWhereRaw('LOWER(full_name) LIKE ?', ['%' . strtolower($query) . '%']);
                }
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

            // If not found, try variations
            if (!$exam) {
                if ($grade === 'Kinder') {
                    // For Kinder, try "Kinder 2" and "K-2"
                    $exam = $healthExaminations->firstWhere('grade_level', 'Kinder 2');
                    if (!$exam) {
                        $exam = $healthExaminations->firstWhere('grade_level', 'K-2');
                    }
                } else {
                    // For grades, try numeric format (e.g., "6" for "Grade 6")
                    $gradeNumber = str_replace('Grade ', '', $grade);
                    $exam = $healthExaminations->firstWhere('grade_level', $gradeNumber);
                }
            }

            $orderedExaminations[$grade] = $exam;
        }

        // Get health treatments for this student
        $healthTreatments = HealthTreatment::where('student_id', $studentId)
            ->orderBy('date', 'desc')
            ->get();

        // Get school settings for PDF header
        $schoolSettings = \App\Models\SchoolSettings::getInstance();

        $pdf = PDF::loadView('health-examination-pdf', compact('student', 'orderedExaminations', 'healthTreatments', 'schoolSettings'));

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
            $oralHealthData = null;

            // Handle Kinder specially since we only have "Kinder 2"
            if ($gradeNum === 'K') {
                $oralHealthData = OralHealthExamination::where('student_id', $studentId)
                    ->where('grade_level', 'Kinder 2')
                    ->latest()
                    ->first();
            } else {
                // For numeric grades, try both formats
                $oralHealthData = OralHealthExamination::where('student_id', $studentId)
                    ->where(function($query) use ($gradeNum) {
                        $query->where('grade_level', $gradeNum)
                              ->orWhere('grade_level', "Grade {$gradeNum}");
                    })
                    ->latest()
                    ->first();
            }

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

                    // Special handling for Kinder variations
                    if ($gradeNum === 'K' || $gradeName === 'Kinder') {
                        $query->orWhere('grade_level', 'Kinder 2')
                              ->orWhere('grade_level', 'K-2');
                    }
                })
                ->orderBy('date', 'asc')
                ->get();

            $oralHealthTreatmentsByGrade[$gradeNum] = $treatments;
        }

        Log::info('Oral Health Data Retrieved:', $oralHealthDataByGrade);
        Log::info('Oral Health Treatments Retrieved:', $oralHealthTreatmentsByGrade);

        // Get school settings for PDF header
        $schoolSettings = \App\Models\SchoolSettings::getInstance();

        $pdf = PDF::loadView('oral-health-examination-pdf', compact('student', 'oralHealthDataByGrade', 'oralHealthTreatmentsByGrade', 'schoolSettings'));

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
            $exam = null;

            // Try exact match first
            if (isset($healthExaminations[$grade])) {
                $exam = $healthExaminations[$grade];
            } else {
                // Try variations
                if ($grade === 'Kinder') {
                    // For Kinder, try "Kinder 2" and "K-2"
                    if (isset($healthExaminations['Kinder 2'])) {
                        $exam = $healthExaminations['Kinder 2'];
                    } elseif (isset($healthExaminations['K-2'])) {
                        $exam = $healthExaminations['K-2'];
                    }
                } else {
                    // For grades, try numeric format (e.g., "6" for "Grade 6")
                    $gradeNumber = str_replace('Grade ', '', $grade);
                    if (isset($healthExaminations[$gradeNumber])) {
                        $exam = $healthExaminations[$gradeNumber];
                    }
                }
            }

            $orderedExaminations[$grade] = $exam;
        }

        // Get school settings for PDF header
        $schoolSettings = \App\Models\SchoolSettings::getInstance();

        return view('health-examination-pdf', compact('student', 'orderedExaminations', 'allGradeLevels', 'schoolSettings'));
    }

    private function getSchoolYearForGrade($grade)
    {
        $gradeToYear = [
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

    /**
     * Format student name as "Last Name, First Name Middle Initial"
     * Assumes full_name is stored as "First Middle Last"
     */
    private function formatNameForReport($fullName)
    {
        if (empty($fullName)) {
            return 'N/A';
        }

        $nameParts = explode(' ', trim($fullName));
        $count = count($nameParts);

        if ($count === 1) {
            // Only one name part
            return $nameParts[0];
        } elseif ($count === 2) {
            // First Last -> Last, First
            return $nameParts[1] . ', ' . $nameParts[0];
        } else {
            // First Middle Last -> Last, First M.
            $lastName = array_pop($nameParts);
            $firstName = array_shift($nameParts);
            $middleInitial = !empty($nameParts) ? strtoupper(substr($nameParts[0], 0, 1)) . '.' : '';

            return $lastName . ', ' . $firstName . ($middleInitial ? ' ' . $middleInitial : '');
        }
    }

    /**
     * Extract last name from full name
     */
    private function getLastName($fullName)
    {
        if (empty($fullName)) {
            return '';
        }
        $nameParts = explode(' ', trim($fullName));
        return end($nameParts);
    }

    /**
     * Sort students by last name
     */
    private function sortByLastName($students, $descending = false)
    {
        return $students->sort(function($a, $b) use ($descending) {
            $lastNameA = strtolower($this->getLastName($a->full_name));
            $lastNameB = strtolower($this->getLastName($b->full_name));

            $comparison = strcmp($lastNameA, $lastNameB);
            return $descending ? -$comparison : $comparison;
        })->values();
    }

    /**
     * Sort students by grade level
     */
    private function sortByGradeLevel($students, $descending = false)
    {
        $gradeOrder = [
            'Kinder 2' => 0, 'K-2' => 0,
            'Grade 1' => 1, '1' => 1,
            'Grade 2' => 2, '2' => 2,
            'Grade 3' => 3, '3' => 3,
            'Grade 4' => 4, '4' => 4,
            'Grade 5' => 5, '5' => 5,
            'Grade 6' => 6, '6' => 6,
        ];

        return $students->sort(function($a, $b) use ($gradeOrder, $descending) {
            $gradeA = $gradeOrder[$a->grade_level] ?? 999;
            $gradeB = $gradeOrder[$b->grade_level] ?? 999;

            $comparison = $gradeA - $gradeB;
            return $descending ? -$comparison : $comparison;
        })->values();
    }
}
