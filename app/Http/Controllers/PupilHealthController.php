<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\HealthExamination;
use App\Models\OralHealthExamination;
use App\Models\OralHealthTreatment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PupilHealthController extends Controller
{
    public function index(Request $request)
    {
        $selectedGrade = $request->query('grade') ?? 'Grade 6';

        session(['grade' => $selectedGrade]);
        Log::info('Index - Selected Grade:', ['grade' => $selectedGrade]);

        $user = auth()->user();

        Log::info('Current authenticated user:', [
            'user_id' => $user->id,
            'username' => $user->username,
            'full_name' => $user->full_name,
            'role' => $user->role
        ]);

        // Filter students based on user role
        if ($user->role === 'teacher') {
            // Teachers can only see their assigned students (including inactive ones from past years)
            $assignedStudentIds = $user->assignedStudents()->pluck('student_id');
            \Log::info('Teacher filtering debug:', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'assigned_student_ids' => $assignedStudentIds->toArray(),
                'assignments_count' => $user->assignedStudents()->count()
            ]);

            if ($assignedStudentIds->isEmpty()) {
                $students = collect([]); // Return empty collection if none assigned
            } else {
                $students = Student::withoutGlobalScopes()
                                  ->whereIn('id', $assignedStudentIds)
                                  ->orderBy('school_year', 'desc')
                                  ->orderBy('full_name', 'asc')
                                  ->get();
            }
        } else {
            // Admins can see all students (active and inactive, all school years)
            // Query directly using DB to get all records, then convert to models
            $studentIds = DB::table('students')->pluck('id');

            // Now get the models using those IDs, bypassing any scopes
            $students = Student::withoutGlobalScopes()
                              ->whereIn('id', $studentIds)
                              ->orderBy('school_year', 'desc')
                              ->orderBy('full_name', 'asc')
                              ->get();

            // Log what we got
            Log::info('Admin query result:', [
                'total_ids_found' => $studentIds->count(),
                'students_returned' => $students->count(),
                'unique_years' => $students->pluck('school_year')->unique()->values()->toArray(),
                'active_count' => $students->where('is_active', true)->count(),
                'inactive_count' => $students->where('is_active', false)->count()
            ]);
        }

        // Debug: Check what we actually got
        $actualCount = $students->count();

        // Get unique school years for debugging
        $uniqueSchoolYears = $students->pluck('school_year')->unique()->values()->toArray();

        // Debug: Check total students in database
        $totalInDb = Student::withoutGlobalScopes()->count();
        $activeInDb = Student::withoutGlobalScopes()->where('is_active', true)->count();
        $inactiveInDb = Student::withoutGlobalScopes()->where('is_active', false)->count();

        // Force check: Get a sample of inactive students to verify they exist
        $sampleInactive = Student::withoutGlobalScopes()
                                 ->where('is_active', false)
                                 ->take(5)
                                 ->get(['id', 'full_name', 'school_year', 'is_active']);

        Log::info('Final student query result:', [
            'user_role' => $user->role,
            'total_in_database' => $totalInDb,
            'active_in_database' => $activeInDb,
            'inactive_in_database' => $inactiveInDb,
            'actual_students_returned' => $actualCount,
            'unique_school_years' => $uniqueSchoolYears,
            'active_count_in_result' => $students->where('is_active', true)->count(),
            'inactive_count_in_result' => $students->where('is_active', false)->count(),
            'sample_inactive_students' => $sampleInactive->map(function($s) {
                return ['id' => $s->id, 'name' => $s->full_name, 'year' => $s->school_year, 'active' => $s->is_active];
            })->toArray(),
            'sample_all_students' => $students->take(10)->map(function($s) {
                return ['name' => $s->full_name, 'year' => $s->school_year, 'active' => $s->is_active];
            })->toArray()
        ]);
        // Compute current school year and add a computed 'is_currently_active' flag
        $currentSchoolYear = $this->getCurrentSchoolYear();
        $students = $students->map(function($s) use ($currentSchoolYear) {
            $s->is_currently_active = ($s->is_active && ($s->school_year === $currentSchoolYear));
            return $s;
        });

        return Inertia::render('PupilHealth/Index', [
            'selectedGrade' => $selectedGrade,
            'students' => $students,
            'userRole' => $user->role
            ,'currentSchoolYear' => $currentSchoolYear
        ]);
    }

    /**
     * Determine the current school year string, e.g. "2024-2025"
     */
    private function getCurrentSchoolYear()
    {
        $currentYear = date('Y');
        $currentMonth = date('n');

        // School year starts in June
        if ($currentMonth >= 6) {
            return $currentYear . '-' . ($currentYear + 1);
        }

        return ($currentYear - 1) . '-' . $currentYear;
    }

    public function showHealthExam(Student $student, Request $request)
    {
        $selectedGrade = $request->query('grade') ?? session('grade');
        Log::info('Show Health Exam - Selected Grade from URL:', ['grade' => $selectedGrade]);

        // Check if teacher has access to this student
        $user = auth()->user();
        if ($user->role === 'teacher') {
            $assignedStudentIds = $user->assignedStudents()->pluck('student_id');
            if (!$assignedStudentIds->contains($student->id)) {
                abort(403, 'Access denied. You can only view your assigned students.');
            }
        }

        // Get the health examination for this student
        $healthExamination = HealthExamination::where('student_id', $student->id)->first();

        // If no health examination exists, create a default one or handle appropriately
        if (!$healthExamination) {
            $healthExamination = new HealthExamination([
                'student_id' => $student->id,
                'examination_date' => now(),
            ]);
        }

        return Inertia::render('HealthExamination/Show', [
            'student' => $student,
            'healthExamination' => $healthExamination,
            'selectedGrade' => $selectedGrade,
            'userRole' => $user->role,
            'currentSchoolYear' => $this->getCurrentSchoolYear()
        ]);
    }

    public function createHealthExam(Student $student, Request $request)
    {
        // Only nurses can create health examinations
        if (auth()->user()->role !== 'nurse') {
            abort(403, 'Access denied. Only nurses can create health examinations.');
        }

        $gradeLevel = $request->query('grade');

        // Return a view for creating a new health examination
        return Inertia::render('HealthExamination/Create', [
            'student' => $student,
            'grade_level' => $gradeLevel
        ]);
    }

    public function storeHealthExam(Request $request)
    {
        // Only nurses can store health examinations
        if (auth()->user()->role !== 'nurse') {
            abort(403, 'Access denied. Only nurses can store health examinations.');
        }

        try {
            Log::info('Store Health Exam Request Data:', $request->all());

            $validated = $request->validate([
                'student_id' => 'required|exists:students,id',
                'grade_level' => 'required|string|max:255',
                'examination_date' => 'required|date',
                'temperature' => 'nullable|numeric|min:35|max:42',
                'heart_rate' => 'nullable|integer|min:40|max:200',
                'height' => 'nullable|numeric|min:50|max:200',
                'weight' => 'nullable|numeric|min:10|max:150',
                'nutritional_status_bmi' => 'nullable|string|max:255',
                'nutritional_status_height' => 'nullable|string|max:255',
                'vision_screening' => 'nullable|string|max:255',
                'vision_screening_specify' => 'nullable|string|max:255',
                'auditory_screening' => 'nullable|string|max:255',
                'auditory_screening_specify' => 'nullable|string|max:255',
                'skin' => 'nullable|string|max:255',
                'skin_specify' => 'nullable|string|max:15',
                'scalp' => 'nullable|string|max:255',
                'scalp_specify' => 'nullable|string|max:15',
                'eye' => 'nullable|string|max:255',
                'eye_specify' => 'nullable|string|max:15',
                'ear' => 'nullable|string|max:255',
                'ear_specify' => 'nullable|string|max:15',
                'nose' => 'nullable|string|max:255',
                'nose_specify' => 'nullable|string|max:15',
                'mouth' => 'nullable|string|max:255',
                'mouth_specify' => 'nullable|string|max:15',
                'neck' => 'nullable|string|max:255',
                'neck_specify' => 'nullable|string|max:15',
                'throat' => 'nullable|string|max:255',
                'throat_specify' => 'nullable|string|max:15',
                'lungs' => 'nullable|string|max:255',
                'lungs_other_specify' => 'nullable|string|max:15',
                'lungs_specify' => 'nullable|string|max:15',
                'heart' => 'nullable|string|max:255',
                'heart_other_specify' => 'nullable|string|max:15',
                'heart_specify' => 'nullable|string|max:15',
                'lungs_heart' => 'nullable|string|max:255',
                'abdomen' => 'nullable|string|max:255',
                'abdomen_specify' => 'nullable|string|max:15',
                'deformities' => 'nullable|string|max:255',
                'deformities_specify' => 'nullable|string|max:15',
                'iron_supplementation' => 'nullable|string|max:255',
                'deworming_status' => 'nullable|string|max:255',
                'immunization' => 'nullable|string|max:15',
                'sbfp_beneficiary' => 'nullable|boolean',
                'four_ps_beneficiary' => 'nullable|boolean',
                'other_specify' => 'nullable|string|max:255',
                'remarks' => 'nullable|string',
            ]);

            // Get student info to add school_year
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
            $student = $studentsQuery->findOrFail($validated['student_id']);
            $validated['school_year'] = $student->school_year;

            Log::info('Creating health examination with data:', $validated);

            // Create the health examination record
            $healthExam = HealthExamination::create($validated);
            Log::info('Health examination created successfully', ['id' => $healthExam->id]);

            $redirectUrl = route('pupil-health.health-exam.show', [
                'student' => $validated['student_id'],
                'grade' => $validated['grade_level']
            ]);

            Log::info('Redirecting to:', ['url' => $redirectUrl]);

            return redirect($redirectUrl)->with('success', 'Health examination created successfully.');

        } catch (\Exception $e) {
            Log::error('Error creating health examination: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withInput()->withErrors([
                'error' => 'Failed to save health examination. ' . $e->getMessage()
            ]);
        }
    }


    public function getHealthExaminationByGradeYear($studentId, Request $request)
    {
        try {
            // Log the incoming request parameters
            Log::info('getHealthExaminationByGradeYear - Request Data:', [
                'student_id' => $studentId,
                'query_params' => $request->query(),
                'grade_level' => $request->query('grade_level')
            ]);

            $gradeLevel = $request->query('grade_level');

            // Find health examination for this student with matching grade level
            // Handle both formats: "6" and "Grade 6", "Kinder 2" etc.
            // Get ALL matching records to see if there are duplicates
            $allRecords = HealthExamination::where('student_id', $studentId)
                ->where(function($query) use ($gradeLevel) {
                    $query->where('grade_level', $gradeLevel);

                    // If numeric grade level (e.g., "6"), also try "Grade 6" format
                    if (is_numeric($gradeLevel)) {
                        $query->orWhere('grade_level', "Grade {$gradeLevel}");
                    }

                    // If "Grade X" format, also try just the number
                    if (preg_match('/^Grade (\d+)$/', $gradeLevel, $matches)) {
                        $query->orWhere('grade_level', $matches[1]);
                    }
                })
                ->orderBy('examination_date', 'desc')
                ->get();

            \Log::info('All matching records found:', [
                'student_id' => $studentId,
                'grade_level' => $gradeLevel,
                'total_records' => $allRecords->count(),
                'record_ids' => $allRecords->pluck('id')->toArray(),
                'record_data' => $allRecords->map(function($record) {
                    return [
                        'id' => $record->id,
                        'grade_level' => $record->grade_level,
                        'examination_date' => $record->examination_date,
                        'height' => $record->height,
                        'weight' => $record->weight,
                        'heart_rate' => $record->heart_rate,
                        'temperature' => $record->temperature
                    ];
                })->toArray()
            ]);

            // Prioritize records with actual data over empty/null records
            $healthExamination = $allRecords->sortByDesc(function($record) {
                $score = 0;
                // Give points for having actual data vs null/empty values
                if ($record->height && $record->height !== 'NgN' && is_numeric($record->height)) $score += 10;
                if ($record->weight && $record->weight !== 'NgN' && is_numeric($record->weight)) $score += 10;
                if ($record->temperature && $record->temperature !== 'NoN' && is_numeric($record->temperature)) $score += 5;
                if ($record->heart_rate && $record->heart_rate !== 'NoN' && is_numeric($record->heart_rate)) $score += 5;
                if ($record->lungs && $record->lungs !== 'Normal' && !empty($record->lungs)) $score += 3;
                if ($record->heart && $record->heart !== 'Normal' && !empty($record->heart)) $score += 3;
                if ($record->skin && !empty($record->skin)) $score += 1;
                if ($record->eye && !empty($record->eye)) $score += 1;
                return $score;
            })->first();

            \Log::info('Selected Record:', [
                'student_id' => $studentId,
                'grade_level' => $gradeLevel,
                'found' => $healthExamination ? true : false,
                'selected_record_id' => $healthExamination ? $healthExamination->id : null,
                'selected_record_data' => $healthExamination ? [
                    'height' => $healthExamination->height,
                    'weight' => $healthExamination->weight,
                    'temperature' => $healthExamination->temperature,
                    'heart_rate' => $healthExamination->heart_rate,
                    'lungs' => $healthExamination->lungs,
                    'heart' => $healthExamination->heart,
                    'iron_supplementation' => $healthExamination->iron_supplementation,
                    'deworming_status' => $healthExamination->deworming_status
                ] : null
            ]);

            if ($healthExamination) {
                return response()->json($healthExamination);
            } else {
                return response()->json(['message' => 'No record found'], 200);
            }
        } catch (\Exception $e) {
            Log::error('Error in getHealthExaminationByGradeYear:', [
                'student_id' => $studentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function showOralHealth(Student $student)
    {
        // Check if teacher has access to this student
        $user = auth()->user();
        if ($user->role === 'teacher') {
            $assignedStudentIds = $user->assignedStudents()->pluck('student_id');
            if (!$assignedStudentIds->contains($student->id)) {
                abort(403, 'Access denied. You can only view your assigned students.');
            }
        }

        $examinations = OralHealthExamination::where('student_id', $student->id)->get();

        return Inertia::render('OralHealth/Show', [
            'student' => $student,
            'examinations' => $examinations,
            'userRole' => $user->role
            ,'currentSchoolYear' => $this->getCurrentSchoolYear()
        ]);
    }

    public function createOralHealth(Student $student)
    {
        // Only nurses can create oral health examinations
        if (auth()->user()->role !== 'nurse') {
            abort(403, 'Access denied. Only nurses can create oral health examinations.');
        }

        return Inertia::render('OralHealth/Create', [
            'student' => $student
        ]);
    }

    public function storeOralHealth(Request $request)
    {
        // Only nurses can store oral health examinations
        if (auth()->user()->role !== 'nurse') {
            abort(403, 'Access denied. Only nurses can store oral health examinations.');
        }

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'grade_level' => 'required|string',
            'school_year' => 'required|string',
            'examination_date' => 'required|date',
            'original_grade' => 'nullable|string',
            'permanent_index_dft' => 'nullable|integer|min:0',
            'permanent_teeth_decayed' => 'nullable|integer|min:0',
            'permanent_teeth_filled' => 'nullable|integer|min:0',
            'permanent_teeth_missing' => 'nullable|integer|min:0',
            'permanent_total_dft' => 'nullable|integer|min:0',
            'permanent_for_extraction' => 'nullable|integer|min:0',
            'permanent_for_filling' => 'nullable|integer|min:0',
            'temporary_index_dft' => 'nullable|integer|min:0',
            'temporary_teeth_decayed' => 'nullable|integer|min:0',
            'temporary_teeth_filled' => 'nullable|integer|min:0',
            'temporary_teeth_missing' => 'nullable|integer|min:0',
            'temporary_total_dft' => 'nullable|integer|min:0',
            'temporary_for_extraction' => 'nullable|integer|min:0',
            'temporary_for_filling' => 'nullable|integer|min:0',
            'tooth_symbols' => 'nullable|array',
            'conditions' => 'nullable|array',
        ]);

        OralHealthExamination::create($validated);

        // Extract grade number for redirect
        $gradeNumber = $validated['original_grade'] ?:
                      (is_numeric($validated['grade_level']) ? $validated['grade_level'] :
                      (preg_match('/(\d+)/', $validated['grade_level'], $matches) ? $matches[1] : '6'));

        return redirect("/pupil-health/oral-health/{$validated['student_id']}?grade={$gradeNumber}")
            ->with('success', 'Oral health examination created successfully.');
    }

    public function getOralHealthByGrade($studentId, Request $request)
    {
        try {
            $gradeLevel = $request->get('grade_level', '6');

            // Find oral health examination for this student with flexible grade level matching
            $oralHealthExamination = OralHealthExamination::where('student_id', $studentId)
                ->where(function($query) use ($gradeLevel) {
                    $query->where('grade_level', $gradeLevel);

                    // If numeric grade level (e.g., "6"), also try "Grade 6" format
                    if (is_numeric($gradeLevel)) {
                        $query->orWhere('grade_level', "Grade {$gradeLevel}");
                    }

                    // If "Grade X" format, also try just the number
                    if (preg_match('/^Grade (\d+)$/', $gradeLevel, $matches)) {
                        $query->orWhere('grade_level', $matches[1]);
                    }

                    // Handle Kinder levels
                    if ($gradeLevel === 'K-2' || $gradeLevel === 'Kinder 2') {
                        $query->orWhere('grade_level', 'K-2')->orWhere('grade_level', 'Kinder 2');
                    }
                })
                ->latest()
                ->first();

            if ($oralHealthExamination) {
                // Ensure tooth_symbols and conditions are properly formatted as objects/arrays
                $response = $oralHealthExamination->toArray();

                // Convert JSON strings back to objects for frontend compatibility
                if (isset($response['tooth_symbols']) && is_string($response['tooth_symbols'])) {
                    $response['tooth_symbols'] = json_decode($response['tooth_symbols'], true);
                }
                if (isset($response['conditions']) && is_string($response['conditions'])) {
                    $response['conditions'] = json_decode($response['conditions'], true);
                }

                return response()->json($response);
            } else {
                return response()->json(['message' => 'No record found'], 200);
            }
        } catch (\Exception $e) {
            Log::error('Error in getOralHealthByGrade: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Oral Health Treatment Methods
    public function createOralHealthTreatment(Student $student)
    {
        // Only nurses can create oral health treatments
        if (auth()->user()->role !== 'nurse') {
            abort(403, 'Access denied. Only nurses can create oral health treatments.');
        }

        return Inertia::render('OralHealthTreatment/Create', [
            'student' => $student
        ]);
    }

    public function storeOralHealthTreatment(Request $request)
    {
        // Only nurses can store oral health treatments
        if (auth()->user()->role !== 'nurse') {
            abort(403, 'Access denied. Only nurses can store oral health treatments.');
        }

        try {
            $validated = $request->validate([
                'student_id' => 'required|exists:students,id',
                'date' => 'required|date',
                'title' => 'required|string|max:255',
                'chief_complaint' => 'required|string',
                'treatment' => 'required|string',
                'remarks' => 'nullable|string',
                'status' => 'required|in:pending,in_progress,completed,cancelled',
                'grade_level' => 'required|string',
                'school_year' => 'required|string'
            ]);

            OralHealthTreatment::create($validated);

            return redirect()->route('pupil-health.oral-health.show', [
                'student' => $validated['student_id'],
                'grade' => $validated['grade_level']
            ])->with('success', 'Oral health treatment created successfully.');

        } catch (\Exception $e) {
            Log::error('Error creating oral health treatment: ' . $e->getMessage());

            return back()->withInput()->withErrors([
                'error' => 'Failed to save oral health treatment. ' . $e->getMessage()
            ]);
        }
    }

    public function getOralHealthTreatmentByStudent($studentId, Request $request)
    {
        try {
            $grade = $request->query('grade');
            $schoolYear = $this->getSchoolYearForGrade($grade);

            // Check if table exists first
            if (!Schema::hasTable('oral_health_treatments')) {
                return response()->json([]);
            }

            $query = OralHealthTreatment::where('student_id', $studentId);

            // Only filter by grade if columns exist
            if (Schema::hasColumn('oral_health_treatments', 'grade_level')) {
                $query->where(function($q) use ($grade) {
                    $q->where('grade_level', $grade)
                      ->orWhereNull('grade_level');
                });
            }

            $treatments = $query->orderBy('date', 'desc')->get();

            return response()->json($treatments);
        } catch (\Exception $e) {
            Log::error('Error fetching oral health treatments: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing a health examination
     */
    public function editHealthExam(HealthExamination $healthExamination, Request $request)
    {
        // Only nurses can edit health examinations
        if (auth()->user()->role !== 'nurse') {
            abort(403, 'Access denied. Only nurses can edit health examinations.');
        }

        $grade = $request->query('grade');

        return Inertia::render('HealthExamination/Edit', [
            'healthExamination' => $healthExamination,
            'student' => $healthExamination->student,
            'selectedGrade' => $grade
        ]);
    }

    /**
     * Update the health examination
     */
    public function updateHealthExam(Request $request, HealthExamination $healthExamination)
    {
        // Only nurses can update health examinations
        if (auth()->user()->role !== 'nurse') {
            abort(403, 'Access denied. Only nurses can update health examinations.');
        }

        $validated = $request->validate([
            'temperature' => 'required|numeric|min:35|max:42',
            'heart_rate' => 'required|integer|min:40|max:200',
            'height' => 'required|numeric|min:50|max:200',
            'weight' => 'required|numeric|min:10|max:150',
            'nutritional_status_bmi' => 'required|string|max:255',
            'nutritional_status_height' => 'required|string|max:255',
            'vision_screening' => 'required|string|max:255',
            'vision_screening_specify' => 'nullable|string|max:255',
            'auditory_screening' => 'required|string|max:255',
            'auditory_screening_specify' => 'nullable|string|max:255',
            'skin' => 'required|string|max:255',
            'skin_specify' => 'nullable|string|max:15',
            'scalp' => 'required|string|max:255',
            'scalp_specify' => 'nullable|string|max:15',
            'eye' => 'required|string|max:255',
            'eye_specify' => 'nullable|string|max:15',
            'ear' => 'required|string|max:255',
            'ear_specify' => 'nullable|string|max:15',
            'nose' => 'required|string|max:255',
            'nose_specify' => 'nullable|string|max:15',
            'mouth' => 'required|string|max:255',
            'mouth_specify' => 'nullable|string|max:15',
            'neck' => 'required|string|max:255',
            'neck_specify' => 'nullable|string|max:15',
            'throat' => 'required|string|max:255',
            'throat_specify' => 'nullable|string|max:15',
            'lungs' => 'required|string|max:255',
            'lungs_other_specify' => 'nullable|string|max:15',
            'lungs_specify' => 'nullable|string|max:15',
            'heart' => 'required|string|max:255',
            'heart_other_specify' => 'nullable|string|max:15',
            'heart_specify' => 'nullable|string|max:15',
            'abdomen' => 'required|string|max:255',
            'abdomen_specify' => 'nullable|string|max:15',
            'deformities' => 'required|string|max:255',
            'deformities_specify' => 'nullable|string|max:15',
            'iron_supplementation' => 'nullable|string|max:255',
            'deworming_status' => 'nullable|string|max:255',
            'immunization' => 'nullable|string|max:15',
            'sbfp_beneficiary' => 'nullable|boolean',
            'four_ps_beneficiary' => 'nullable|boolean',
            'other_specify' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
            'grade_level' => 'required|string|max:255',
            'school_year' => 'required|string|max:255',
        ]);

        $healthExamination->update($validated);

        return redirect()->route('pupil-health.health-exam.show', [
            'student' => $healthExamination->student_id,
            'grade' => $validated['grade_level']
        ])->with('success', 'Health examination updated successfully.');
    }

    /**
     * Show the form for editing an oral health examination
     */
    public function editOralHealth($id, Request $request)
    {
        // Only nurses can edit oral health examinations
        if (auth()->user()->role !== 'nurse') {
            abort(403, 'Access denied. Only nurses can edit oral health examinations.');
        }

        $grade = $request->query('grade');

        Log::info('EditOralHealth - Looking for examination with ID:', ['id' => $id, 'grade' => $grade]);

        // Find the oral health examination
        $oralHealthExamination = OralHealthExamination::with('student')->find($id);

        if (!$oralHealthExamination) {
            Log::error('EditOralHealth - Examination not found:', ['id' => $id]);
            return redirect()->back()->with('error', 'Oral health examination not found.');
        }

        Log::info('EditOralHealth - Examination found:', [
            'id' => $oralHealthExamination->id,
            'student_id' => $oralHealthExamination->student_id,
            'student_name' => $oralHealthExamination->student->full_name ?? 'Unknown'
        ]);

        // Ensure conditions and tooth_symbols are properly formatted for frontend
        $examData = $oralHealthExamination->toArray();

        // Handle conditions data format
        if (isset($examData['conditions'])) {
            if (is_string($examData['conditions'])) {
                $examData['conditions'] = json_decode($examData['conditions'], true);
            }
        }

        // Handle tooth_symbols data format
        if (isset($examData['tooth_symbols'])) {
            if (is_string($examData['tooth_symbols'])) {
                $examData['tooth_symbols'] = json_decode($examData['tooth_symbols'], true);
            }
        }

        return Inertia::render('OralHealth/Edit', [
            'oralHealthExamination' => $examData,
            'student' => $oralHealthExamination->student,
            'selectedGrade' => $grade ?: $oralHealthExamination->grade_level
        ]);
    }

    /**
     * Update the oral health examination
     */
    public function updateOralHealth(Request $request, OralHealthExamination $oralHealthExamination)
    {
        // Only nurses can update oral health examinations
        if (auth()->user()->role !== 'nurse') {
            abort(403, 'Access denied. Only nurses can update oral health examinations.');
        }

        $validated = $request->validate([
            'examination_date' => 'required|date',
            'permanent_index_dft' => 'required|numeric',
            'permanent_teeth_decayed' => 'required|integer',
            'permanent_teeth_filled' => 'required|integer',
            'permanent_teeth_missing' => 'required|integer',
            'permanent_total_dft' => 'required|integer',
            'permanent_for_extraction' => 'required|integer',
            'permanent_for_filling' => 'required|integer',
            'temporary_index_dft' => 'required|numeric',
            'temporary_teeth_decayed' => 'required|integer',
            'temporary_teeth_filled' => 'required|integer',
            'temporary_teeth_missing' => 'required|integer',
            'temporary_total_dft' => 'required|integer',
            'temporary_for_extraction' => 'required|integer',
            'temporary_for_filling' => 'required|integer',
            'tooth_symbols' => 'nullable|array',
            'conditions' => 'nullable|array',
            'grade_level' => 'required|string',
            'school_year' => 'required|string',
        ]);

        $oralHealthExamination->update($validated);

        return redirect()->route('pupil-health.oral-health.show', [
            'student' => $oralHealthExamination->student_id,
            'grade' => $validated['grade_level']
        ])->with('success', 'Oral health examination updated successfully.');
    }

    public function getAllHealthExams(Student $student)
    {
        // Check if teacher has access to this student
        $user = auth()->user();
        if ($user->role === 'teacher') {
            $assignedStudentIds = $user->assignedStudents()->pluck('student_id');
            if (!$assignedStudentIds->contains($student->id)) {
                return response()->json(['error' => 'Access denied'], 403);
            }
        }

        // Get all health examinations for this student, ordered by date
        $healthExaminations = HealthExamination::where('student_id', $student->id)
            ->orderBy('examination_date', 'asc')
            ->get(['id', 'examination_date', 'grade_level', 'height', 'weight', 'school_year']);

        return response()->json($healthExaminations);
    }

    private function getSchoolYearForGrade($grade)
    {
        $gradeToYear = [
            'Grade K' => '2024-2025',
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
