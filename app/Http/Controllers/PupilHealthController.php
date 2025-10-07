<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\HealthExamination;
use App\Models\OralHealthExamination;
use App\Models\OralHealthTreatment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class PupilHealthController extends Controller
{
    public function index(Request $request)
    {
        $selectedGrade = $request->query('grade') ?? 'Grade 6';
        
        session(['grade' => $selectedGrade]);
        \Log::info('Index - Selected Grade:', ['grade' => $selectedGrade]);
        
        $user = auth()->user();
        
        \Log::info('Current authenticated user:', [
            'user_id' => $user->id,
            'username' => $user->username,
            'full_name' => $user->full_name,
            'role' => $user->role
        ]);
        
        // Filter students based on user role
        if ($user->role === 'teacher') {
            // Teachers can only see their assigned students
            $assignedStudentIds = $user->assignedStudents()->pluck('student_id');
            \Log::info('Teacher filtering debug:', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'assigned_student_ids' => $assignedStudentIds->toArray(),
                'assignments_count' => $user->assignedStudents()->count()
            ]);
            
            if ($assignedStudentIds->isEmpty()) {
                $studentsQuery = Student::whereRaw('1 = 0'); // Return no students if none assigned
            } else {
                $studentsQuery = Student::whereIn('id', $assignedStudentIds);
            }
        } else {
            // Admins can see all students
            $studentsQuery = Student::query();
        }

        $students = $studentsQuery->get();
        
        \Log::info('Final student query result:', [
            'user_role' => $user->role,
            'students_count' => $students->count(),
            'student_names' => $students->pluck('full_name')->toArray()
        ]);
        
        return Inertia::render('PupilHealth/Index', [
            'selectedGrade' => $selectedGrade,
            'students' => $students
        ]);
    }

    public function showHealthExam(Student $student, Request $request)
    {
        $selectedGrade = $request->query('grade') ?? session('grade');
        \Log::info('Show Health Exam - Selected Grade from URL:', ['grade' => $selectedGrade]);
        
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
            'userRole' => $user->role
        ]);
    }

    public function createHealthExam(Student $student, Request $request)
    {
        $gradeLevel = $request->query('grade');
        
        // Return a view for creating a new health examination
        return Inertia::render('HealthExamination/Create', [
            'student' => $student,
            'grade_level' => $gradeLevel
        ]);
    }

    public function storeHealthExam(Request $request)
    {
        try {
            \Log::info('Store Health Exam Request Data:', $request->all());
            
            $validated = $request->validate([
                'student_id' => 'required|exists:students,id',
                'grade_level' => 'required|string',
                'examination_date' => 'required|date',
                'temperature' => 'nullable|numeric',
                'heart_rate' => 'nullable|integer',
                'height' => 'nullable|numeric',
                'weight' => 'nullable|numeric',
                'nutritional_status_bmi' => 'nullable|string',
                'nutritional_status_height' => 'nullable|string',
                'vision_screening' => 'nullable|string',
                'auditory_screening' => 'nullable|string',
                'skin' => 'nullable|string',
                'scalp' => 'nullable|string',
                'eye' => 'nullable|string',
                'ear' => 'nullable|string',
                'nose' => 'nullable|string',
                'mouth' => 'nullable|string',
                'neck' => 'nullable|string',
                'throat' => 'nullable|string',
                'lungs' => 'nullable|string',
                'lungs_other_specify' => 'nullable|string',
                'heart' => 'nullable|string',
                'heart_other_specify' => 'nullable|string',
                'lungs_heart' => 'nullable|string',
                'abdomen' => 'nullable|string',
                'deformities' => 'nullable|string',
                'iron_supplementation' => 'nullable|string',
                'deworming_status' => 'nullable|string',
                'immunization' => 'nullable|string',
                'sbfp_beneficiary' => 'nullable|boolean',
                'four_ps_beneficiary' => 'nullable|boolean',
                'other_specify' => 'nullable|string',
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

            \Log::info('Creating health examination with data:', $validated);
            
            // Create the health examination record
            $healthExam = HealthExamination::create($validated);
            \Log::info('Health examination created successfully', ['id' => $healthExam->id]);

            $redirectUrl = route('pupil-health.health-exam.show', [
                'student' => $validated['student_id'],
                'grade' => $validated['grade_level']
            ]);
            
            \Log::info('Redirecting to:', ['url' => $redirectUrl]);
            
            return redirect($redirectUrl)->with('success', 'Health examination created successfully.');
            
        } catch (\Exception $e) {
            \Log::error('Error creating health examination: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withInput()->withErrors([
                'error' => 'Failed to save health examination. ' . $e->getMessage()
            ]);
        }
    }

    public function getHealthExamination($id)
    {
        $healthExamination = HealthExamination::findOrFail($id);
        return response()->json($healthExamination);
    }

    public function getHealthExaminationByGradeYear($studentId, Request $request)
    {
        try {
            // Log the incoming request parameters
            \Log::info('getHealthExaminationByGradeYear - Request Data:', [
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
            \Log::error('Error in getHealthExaminationByGradeYear:', [
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
        ]);
    }

    public function createOralHealth(Student $student)
    {
        return Inertia::render('OralHealth/Create', [
            'student' => $student
        ]);
    }

    public function storeOralHealth(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'grade_level' => 'required|string',
            'school_year' => 'required|string',
            'examination_date' => 'required|date',
            'original_grade' => 'nullable|string',
            'permanent_index_dft' => 'nullable|integer|min:0',
            'permanent_teeth_decayed' => 'nullable|integer|min:0',
            'permanent_teeth_filled' => 'nullable|integer|min:0',
            'permanent_total_dft' => 'nullable|integer|min:0',
            'permanent_for_extraction' => 'nullable|integer|min:0',
            'permanent_for_filling' => 'nullable|integer|min:0',
            'temporary_index_dft' => 'nullable|integer|min:0',
            'temporary_teeth_decayed' => 'nullable|integer|min:0',
            'temporary_teeth_filled' => 'nullable|integer|min:0',
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
            \Log::error('Error in getOralHealthByGrade: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Oral Health Treatment Methods
    public function createOralHealthTreatment(Student $student)
    {
        return Inertia::render('OralHealthTreatment/Create', [
            'student' => $student
        ]);
    }

    public function storeOralHealthTreatment(Request $request)
    {
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
            \Log::error('Error creating oral health treatment: ' . $e->getMessage());
            
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
            \Log::error('Error fetching oral health treatments: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Show the form for editing a health examination
     */
    public function editHealthExam(HealthExamination $healthExamination, Request $request)
    {
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
        $validated = $request->validate([
            'temperature' => 'nullable|numeric',
            'heart_rate' => 'nullable|integer',
            'height' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'nutritional_status_bmi' => 'nullable|string',
            'nutritional_status_height' => 'nullable|string',
            'vision_screening' => 'nullable|string',
            'auditory_screening' => 'nullable|string',
            'skin' => 'nullable|string',
            'scalp' => 'nullable|string',
            'eye' => 'nullable|string',
            'ear' => 'nullable|string',
            'nose' => 'nullable|string',
            'mouth' => 'nullable|string',
            'neck' => 'nullable|string',
            'throat' => 'nullable|string',
            'lungs' => 'nullable|string',
            'lungs_other_specify' => 'nullable|string',
            'heart' => 'nullable|string',
            'heart_other_specify' => 'nullable|string',
            'abdomen' => 'nullable|string',
            'deformities' => 'nullable|string',
            'iron_supplementation' => 'nullable|string',
            'deworming_status' => 'nullable|string',
            'immunization' => 'nullable|string',
            'sbfp_beneficiary' => 'nullable|boolean',
            'four_ps_beneficiary' => 'nullable|boolean',
            'other_specify' => 'nullable|string',
            'remarks' => 'nullable|string',
            'grade_level' => 'required|string',
            'school_year' => 'required|string',
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
        $grade = $request->query('grade');
        
        \Log::info('EditOralHealth - Looking for examination with ID:', ['id' => $id, 'grade' => $grade]);
        
        // Find the oral health examination
        $oralHealthExamination = OralHealthExamination::with('student')->find($id);
        
        if (!$oralHealthExamination) {
            \Log::error('EditOralHealth - Examination not found:', ['id' => $id]);
            return redirect()->back()->with('error', 'Oral health examination not found.');
        }
        
        \Log::info('EditOralHealth - Examination found:', [
            'id' => $oralHealthExamination->id,
            'student_id' => $oralHealthExamination->student_id,
            'student_name' => $oralHealthExamination->student->full_name ?? 'Unknown'
        ]);
        
        return Inertia::render('OralHealth/Edit', [
            'oralHealthExamination' => $oralHealthExamination,
            'student' => $oralHealthExamination->student,
            'selectedGrade' => $grade ?: $oralHealthExamination->grade_level
        ]);
    }

    /**
     * Update the oral health examination
     */
    public function updateOralHealth(Request $request, OralHealthExamination $oralHealthExamination)
    {
        $validated = $request->validate([
            'examination_date' => 'nullable|date',
            'permanent_index_dft' => 'nullable|numeric',
            'permanent_teeth_decayed' => 'nullable|integer',
            'permanent_teeth_filled' => 'nullable|integer',
            'permanent_teeth_missing' => 'nullable|integer',
            'permanent_total_dft' => 'nullable|integer',
            'permanent_for_extraction' => 'nullable|integer',
            'permanent_for_filling' => 'nullable|integer',
            'temporary_index_dft' => 'nullable|numeric',
            'temporary_teeth_decayed' => 'nullable|integer',
            'temporary_teeth_filled' => 'nullable|integer',
            'temporary_teeth_missing' => 'nullable|integer',
            'temporary_total_dft' => 'nullable|integer',
            'temporary_for_extraction' => 'nullable|integer',
            'temporary_for_filling' => 'nullable|integer',
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
