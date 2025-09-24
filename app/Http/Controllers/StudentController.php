<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\HealthExamination;
use App\Models\OralHealth;
use App\Models\Incident;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StudentController extends Controller
{
    public function index()
    {
        return Inertia::render('HealthExamination/Index', [
            'students' => Student::all()
        ]);
    }

    public function show(Student $student)
    {
        return Inertia::render('HealthExamination/Show', [
            'student' => $student,
            'examinations' => $student->healthExaminations()->with('student')->orderBy('examination_date', 'desc')->get()
        ]);
    }

    public function create(Student $student)
    {
        return Inertia::render('HealthExamination/Create', [
            'student' => $student
        ]);
    }

    public function edit(HealthExamination $healthExamination)
    {
        return Inertia::render('HealthExamination/Edit', [
            'student' => $healthExamination->student,
            'examination' => $healthExamination
        ]);
    }

    public function dashboard()
    {
        $user = auth()->user();
        
        // Filter students based on user role
        if ($user->role === 'teacher') {
            // Teachers can only see their assigned students
            $assignedStudentIds = $user->assignedStudents()->pluck('student_id');
            \Log::info('Teacher dashboard filtering:', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'assigned_student_ids' => $assignedStudentIds->toArray()
            ]);
            
            if ($assignedStudentIds->isEmpty()) {
                // No assigned students
                $totalStudents = 0;
                $femaleStudents = 0;
                $maleStudents = 0;
                $nutritionalStatusBMI = collect();
                $nutritionalStatusHeight = collect();
                $dewormingDewormed = 0;
                $dewormingNotDewormed = 0;
                $ironPositive = 0;
                $ironNegative = 0;
            } else {
                // Filter by assigned students
                $totalStudents = Student::whereIn('id', $assignedStudentIds)->count();
                $femaleStudents = Student::whereIn('id', $assignedStudentIds)->where('sex', 'Female')->count();
                $maleStudents = Student::whereIn('id', $assignedStudentIds)->where('sex', 'Male')->count();

                // Nutritional Status BMI Distribution for assigned students
                $nutritionalStatusBMI = HealthExamination::whereIn('student_id', $assignedStudentIds)
                    ->selectRaw('nutritional_status_bmi, COUNT(*) as count')
                    ->groupBy('nutritional_status_bmi')->get();

                // Nutritional Status Height Distribution for assigned students
                $nutritionalStatusHeight = HealthExamination::whereIn('student_id', $assignedStudentIds)
                    ->selectRaw('nutritional_status_height, COUNT(*) as count')
                    ->groupBy('nutritional_status_height')->get();

                // Deworming stats for assigned students
                $dewormingDewormed = HealthExamination::whereIn('student_id', $assignedStudentIds)
                    ->where('deworming_status', 'dewormed')->count();
                $dewormingNotDewormed = HealthExamination::whereIn('student_id', $assignedStudentIds)
                    ->where('deworming_status', 'not_dewormed')->count();

                // Iron supplement stats for assigned students
                $ironPositive = HealthExamination::whereIn('student_id', $assignedStudentIds)
                    ->where('iron_supplementation', 'positive')->count();
                $ironNegative = HealthExamination::whereIn('student_id', $assignedStudentIds)
                    ->where('iron_supplementation', 'negative')->count();
            }
        } else {
            // Admins can see all students
            $totalStudents = Student::count();
            $femaleStudents = Student::where('sex', 'Female')->count();
            $maleStudents = Student::where('sex', 'Male')->count();

            // Nutritional Status BMI Distribution
            $nutritionalStatusBMI = HealthExamination::selectRaw('
                nutritional_status_bmi,
                COUNT(*) as count
            ')->groupBy('nutritional_status_bmi')->get();

            // Nutritional Status Height Distribution
            $nutritionalStatusHeight = HealthExamination::selectRaw('
                nutritional_status_height,
                COUNT(*) as count
            ')->groupBy('nutritional_status_height')->get();

            $dewormingDewormed = HealthExamination::where('deworming_status', 'dewormed')->count();
            $dewormingNotDewormed = HealthExamination::where('deworming_status', 'not_dewormed')->count();
            $ironPositive = HealthExamination::where('iron_supplementation', 'positive')->count();
            $ironNegative = HealthExamination::where('iron_supplementation', 'negative')->count();
        }

        \Log::info('Dashboard stats:', [
            'user_role' => $user->role,
            'total_students' => $totalStudents,
            'female_students' => $femaleStudents,
            'male_students' => $maleStudents
        ]);

        return Inertia::render('Home', [
            'dashboardData' => [
                'totalStudents' => $totalStudents,
                'femaleStudents' => $femaleStudents,
                'maleStudents' => $maleStudents,
                'nutritionalStatusBMI' => $nutritionalStatusBMI,
                'nutritionalStatusHeight' => $nutritionalStatusHeight,
                'deworming' => [
                    'dewormed' => $dewormingDewormed,
                    'notDewormed' => $dewormingNotDewormed
                ],
                'ironSupplement' => [
                    'positive' => $ironPositive,
                    'negative' => $ironNegative
                ]
            ],
            'userRole' => $user->role
        ]);
    }

    public function update(Request $request, HealthExamination $healthExamination)
    {
        $validated = $request->validate([
            'temperature' => 'required|numeric',
            'heart_rate' => 'required|numeric',
            'height' => 'required|numeric',
            'weight' => 'required|numeric',
            'nutritional_status_bmi' => 'required|string',
            'nutritional_status_height' => 'required|string',
            'vision_screening' => 'required|string',
            'auditory_screening' => 'required|string',
            'skin' => 'required|string',
            'scalp' => 'required|string',
            'eye' => 'required|string',
            'ear' => 'required|string',
            'nose' => 'required|string',
            'mouth' => 'required|string',
            'neck' => 'required|string',
            'throat' => 'required|string',
            'lungs_heart' => 'required|string',
            'abdomen' => 'required|string',
            'deformities' => 'required|string',
            'remarks' => 'nullable|string'
        ]);

        $validated['examination_date'] = now();
        
        $healthExamination->update($validated);

        return redirect()->route('health-examination.show', $healthExamination->student_id)
            ->with('message', 'Health examination updated successfully.');
    }

    public function destroy(HealthExamination $healthExamination)
    {
        $student_id = $healthExamination->student_id;
        $healthExamination->delete();

        return redirect()->route('health-examination.show', $student_id)
            ->with('message', 'Health examination deleted successfully.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'examination_date' => 'required|date',
            'temperature' => 'nullable|numeric',
            'heart_rate' => 'nullable|string',
            'height' => 'nullable|string',
            'weight' => 'nullable|string',
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
            'lungs_heart' => 'nullable|string',
            'abdomen' => 'nullable|string',
            'deformities' => 'nullable|string',
            'remarks' => 'nullable|string'
        ]);

        HealthExamination::create($validated);

        return redirect()->route('health-examination.show', $request->student_id);
    }

    public function pupilHealth()
    {
        $user = auth()->user();
        
        // Filter students based on user role
        if ($user->role === 'teacher') {
            // Teachers can only see their assigned students
            $assignedStudentIds = $user->assignedStudents()->pluck('student_id');
            \Log::info('Teacher filtering in StudentController:', [
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
        
        $students = $studentsQuery->with(['healthExaminations'])
            ->select('id', 'full_name', 'lrn', 'age', 'sex', 'grade_level', 'school_year')
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->full_name,
                    'lrn' => $student->lrn ?? 'N/A',
                    'grade_level' => $student->grade_level ?? 'N/A',
                    'school_year' => $student->school_year ?? 'N/A',
                    'health_record' => $student->healthExaminations->isNotEmpty() ? 'Health Examination' : null
                ];
            });

        \Log::info('Final student result in StudentController:', [
            'user_role' => $user->role,
            'students_count' => $students->count(),
            'student_names' => $students->pluck('name')->toArray()
        ]);

        return Inertia::render('Pupil Health/Index', [
            'students' => $students
        ]);
    }

    // Removed generateRecord method - replaced by HealthReportController

    public function showHealthExam(Student $student)
    {
        // Fetch the latest health examination for this student
        $healthExamination = HealthExamination::where('student_id', $student->id)
            ->latest()
            ->first();

        // Log for debugging
        \Log::info('Showing Health Exam for Student', [
            'student_id' => $student->id,
            'health_examination' => $healthExamination
        ]);

        return Inertia::render('HealthExam/Show', [
            'student' => $student,
            'healthExamination' => $healthExamination
        ]);
    }

    public function showOralHealth(Student $student)
    {
        $oralHealth = OralHealth::where('student_id', $student->id)
            ->latest()
            ->first();

        return Inertia::render('Pupil Health/OralHealth/Show', [
            'student' => $student,
            'oralHealth' => $oralHealth
        ]);
    }

    public function showIncident(Student $student, Request $request)
    {
        // Check if teacher has access to this student
        $user = auth()->user();
        if ($user->role === 'teacher') {
            $assignedStudentIds = $user->assignedStudents()->pluck('student_id');
            if (!$assignedStudentIds->contains($student->id)) {
                abort(403, 'Access denied. You can only view your assigned students.');
            }
        }
        
        $grade = $request->query('grade', $student->grade_level);
        
        // Get school year based on grade level
        $schoolYear = $this->getSchoolYearForGrade($grade);
        
        $incidents = Incident::where('student_id', $student->id)
            ->where('grade_level', $grade)
            ->where('school_year', $schoolYear)
            ->latest()
            ->get();

        // Add timer information to each incident
        $incidents = $incidents->map(function ($incident) {
            $timerStatus = $incident->getTimerStatus();
            return [
                'id' => $incident->id,
                'student_id' => $incident->student_id,
                'date' => $incident->date,
                'complaint' => $incident->complaint,
                'actions_taken' => $incident->actions_taken,
                'status' => $incident->status,
                'timer_status' => $incident->timer_status,
                'grade_level' => $incident->grade_level,
                'school_year' => $incident->school_year,
                'started_at' => $incident->started_at,
                'expires_at' => $incident->expires_at,
                'is_expired' => $incident->is_expired,
                'can_edit' => $incident->canEdit(),
                'remaining_minutes' => $incident->getRemainingMinutes(),
                'timer_display' => $timerStatus,
                'created_at' => $incident->created_at,
                'updated_at' => $incident->updated_at,
            ];
        });

        return Inertia::render('Incident/Show', [
            'student' => $student,
            'incidents' => $incidents,
            'currentGrade' => $grade,
            'currentSchoolYear' => $schoolYear,
            'userRole' => $user->role
        ]);
    }

    public function createIncident(Student $student)
    {
        return Inertia::render('Incident/Create', [
            'student' => $student
        ]);
    }

    public function storeIncident(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'date' => 'required|date',
            'complaint' => 'required|string',
            'actions_taken' => 'required|string',
            'timer_status' => 'nullable|in:not_started,active,paused,completed,expired',
            'grade_level' => 'required|string',
            'school_year' => 'required|string'
        ]);

        // Set default status to pending
        $validated['status'] = 'pending';
        
        // Set default timer status if not provided
        if (!isset($validated['timer_status'])) {
            $validated['timer_status'] = 'not_started';
        }

        $incident = Incident::create($validated);
        
        // Start the timer automatically when incident is created
        $incident->startTimer();

        return redirect()->route('pupil-health.incident', [
            'student' => $validated['student_id'],
            'grade' => $validated['grade_level']
        ])->with('success', 'Incident report created successfully.');
    }

    public function updateTimerStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'timer_status' => 'required|in:not_started,active,paused,completed,expired'
        ]);

        $incident = Incident::findOrFail($id);
        
        // Check if user has permission to update this incident
        $user = auth()->user();
        if ($user->role === 'teacher') {
            // Teachers can only update incidents for their assigned students
            $assignedStudentIds = $user->assignedStudents()->pluck('student_id');
            if (!$assignedStudentIds->contains($incident->student_id)) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        $incident->update(['timer_status' => $validated['timer_status']]);

        return response()->json([
            'success' => true,
            'message' => 'Timer status updated successfully',
            'timer_status' => $validated['timer_status']
        ]);
    }

    public function getIncidentsByStudent(Request $request, $studentId)
    {
        $user = auth()->user();
        
        // Check if user has access to this student
        if ($user->role === 'teacher') {
            $assignedStudentIds = $user->assignedStudents()->pluck('student_id');
            if (!$assignedStudentIds->contains($studentId)) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        $student = Student::findOrFail($studentId);
        $grade = $request->query('grade', $student->grade_level);
        
        // Get school year based on grade level
        $schoolYear = $this->getSchoolYearForGrade($grade);

        $incidents = Incident::where('student_id', $studentId)
            ->where('grade_level', $grade)
            ->where('school_year', $schoolYear)
            ->latest()
            ->get();

        // Add timer information to each incident
        $incidents = $incidents->map(function ($incident) {
            $timerStatus = $incident->getTimerStatus();
            return [
                'id' => $incident->id,
                'student_id' => $incident->student_id,
                'date' => $incident->date,
                'complaint' => $incident->complaint,
                'actions_taken' => $incident->actions_taken,
                'status' => $incident->status,
                'timer_status' => $incident->timer_status,
                'grade_level' => $incident->grade_level,
                'school_year' => $incident->school_year,
                'started_at' => $incident->started_at,
                'expires_at' => $incident->expires_at,
                'is_expired' => $incident->is_expired,
                'can_edit' => $incident->canEdit(),
                'remaining_minutes' => $incident->getRemainingMinutes(),
                'timer_display' => $timerStatus,
                'created_at' => $incident->created_at,
                'updated_at' => $incident->updated_at,
            ];
        });

        return response()->json($incidents);
    }

    private function getSchoolYearForGrade($grade)
    {
        // Map grade levels to school years
        $gradeToYear = [
            'Kinder 1' => '2022-2023',
            'Kinder 2' => '2023-2024', 
            'Grade 1' => '2024-2025',
            'Grade 2' => '2023-2024',
            'Grade 3' => '2022-2023',
            'Grade 4' => '2021-2022',
            'Grade 5' => '2020-2021',
            'Grade 6' => '2019-2020'
        ];
        
        return $gradeToYear[$grade] ?? '2024-2025';
    }
}
