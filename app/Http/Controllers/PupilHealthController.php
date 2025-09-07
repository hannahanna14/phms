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
        
        // Filter students based on user role
        if ($user->role === 'teacher') {
            // Teachers can only see their assigned students
            $assignedStudentIds = $user->assignedStudents()->pluck('student_id');
            if ($assignedStudentIds->isEmpty()) {
                $studentsQuery = Student::whereRaw('1 = 0'); // Return no students if none assigned
            } else {
                $studentsQuery = Student::whereIn('id', $assignedStudentIds);
            }
        } else {
            // Admins can see all students
            $studentsQuery = Student::query();
        }

        return Inertia::render('PupilHealth/Index', [
            'selectedGrade' => $selectedGrade,
            'students' => $studentsQuery->get()
        ]);
    }

    public function showHealthExam(Student $student, Request $request)
    {
        $selectedGrade = $request->query('grade') ?? session('grade');
        \Log::info('Show Health Exam - Selected Grade from URL:', ['grade' => $selectedGrade]);
        
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
            'selectedGrade' => $selectedGrade
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
                'height' => 'nullable|string',
                'weight' => 'nullable|string',
                'temperature' => 'nullable|string',
                'heart_rate' => 'nullable|string',
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
                'throat' => 'nullable|string',
                'neck' => 'nullable|string',
                'lungs_heart' => 'nullable|string',
                'abdomen' => 'nullable|string',
                'deformities' => 'nullable|string',
                'deworming_status' => 'nullable|string',
                'iron_supplementation' => 'nullable|string',
                'sbfp_beneficiary' => 'nullable|boolean',
                'four_ps_beneficiary' => 'nullable|boolean',
                'remarks' => 'nullable|string',
                'immunization' => 'nullable|string',
                'other_specify' => 'nullable|string',
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
        // Log the incoming request parameters
        \Log::info('getHealthExaminationByGradeYear - Request Data:', [
            'student_id' => $studentId,
            'query_params' => $request->query(),
            'grade_level' => $request->query('grade_level')
        ]);

        $gradeLevel = $request->query('grade_level');

        // Find health examination for this student with matching grade level
        $healthExamination = HealthExamination::where('student_id', $studentId)
            ->where('grade_level', $gradeLevel)
            ->latest()
            ->first();

        \Log::info('Query Result:', [
            'student_id' => $studentId,
            'grade_level' => $gradeLevel,
            'found' => $healthExamination ? true : false
        ]);

        if ($healthExamination) {
            return response()->json($healthExamination);
        } else {
            return response()->json(['message' => 'No record found'], 200);
        }
    }

    public function showOralHealth(Student $student)
    {
        $examinations = OralHealthExamination::where('student_id', $student->id)->get();
        
        return Inertia::render('OralHealth/Show', [
            'student' => $student,
            'examinations' => $examinations
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

            // Find oral health examination for this student with matching grade level
            $oralHealthExamination = OralHealthExamination::where('student_id', $studentId)
                ->where('grade_level', $gradeLevel)
                ->latest()
                ->first();

            if ($oralHealthExamination) {
                return response()->json($oralHealthExamination);
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
