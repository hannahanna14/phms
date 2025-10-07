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

    public function dashboard(Request $request)
    {
        $user = auth()->user();
        $selectedYear = $request->get('year', 'All');
        
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
                $deformitiesNone = 0;
                $deformitiesCongenital = 0;
                $deformitiesAcquired = 0;
                $deformitiesOther = 0;
                $skinNormal = 0; $skinAbnormal = 0;
                $scalpNormal = 0; $scalpAbnormal = 0;
                $eyesNormal = 0; $eyesAbnormal = 0;
                $earsNormal = 0; $earsAbnormal = 0;
                $noseNormal = 0; $noseAbnormal = 0;
                $mouthNormal = 0; $mouthAbnormal = 0;
                
                // Initialize empty collections for examination stats
                $skinStats = collect();
                $scalpStats = collect();
                $eyeStats = collect();
                $earStats = collect();
                $noseStats = collect();
                $mouthStats = collect();
                $throatStats = collect();
                $neckStats = collect();
                $lungsStats = collect();
                $heartStats = collect();
                $abdomenStats = collect();
                $throatNormal = 0; $throatAbnormal = 0;
                $neckNormal = 0; $neckAbnormal = 0;
                $lungsNormal = 0; $lungsAbnormal = 0;
                $heartNormal = 0; $heartAbnormal = 0;
                $abdomenNormal = 0; $abdomenDistended = 0; $abdomenAbnormal = 0;
                $oralHealthExaminations = 0;
                $oralHealthTreatments = 0;
                $oralHealthConditions = collect();
            } else {
                // Filter by assigned students
                $totalStudents = Student::whereIn('id', $assignedStudentIds)->count();
                $femaleStudents = Student::whereIn('id', $assignedStudentIds)->where('sex', 'Female')->count();
                $maleStudents = Student::whereIn('id', $assignedStudentIds)->where('sex', 'Male')->count();

                // Build base query with year filter
                $baseQuery = HealthExamination::whereIn('student_id', $assignedStudentIds);
                if ($selectedYear !== 'All') {
                    $baseQuery->where('school_year', $selectedYear);
                }

                // Nutritional Status BMI Distribution for assigned students
                $nutritionalStatusBMI = (clone $baseQuery)
                    ->selectRaw('nutritional_status_bmi, COUNT(*) as count')
                    ->groupBy('nutritional_status_bmi')->get();

                // Nutritional Status Height Distribution for assigned students
                $nutritionalStatusHeight = (clone $baseQuery)
                    ->selectRaw('nutritional_status_height, COUNT(*) as count')
                    ->groupBy('nutritional_status_height')->get();

                // Deworming stats for assigned students
                $dewormingDewormed = (clone $baseQuery)
                    ->where('deworming_status', 'dewormed')->count();
                $dewormingNotDewormed = (clone $baseQuery)
                    ->where('deworming_status', 'not_dewormed')->count();

                // Iron supplement stats for assigned students
                $ironPositive = (clone $baseQuery)
                    ->where('iron_supplementation', 'positive')->count();
                $ironNegative = (clone $baseQuery)
                    ->where('iron_supplementation', 'negative')->count();


                // Deformities stats for assigned students with flexible value matching
                $deformitiesNone = (clone $baseQuery)
                    ->whereIn('deformities', ['None', 'none', 'Normal', 'normal'])->count();
                $deformitiesCongenital = (clone $baseQuery)
                    ->whereIn('deformities', ['Congenital', 'congenital'])->count();
                $deformitiesAcquired = (clone $baseQuery)
                    ->whereIn('deformities', ['Acquired', 'acquired'])->count();
                $deformitiesOther = (clone $baseQuery)
                    ->whereNotNull('deformities')
                    ->whereNotIn('deformities', ['None', 'none', 'Normal', 'normal', 'Congenital', 'congenital', 'Acquired', 'acquired'])
                    ->count();

                // Individual examination stats for assigned students - return actual data
                $skinStats = (clone $baseQuery)->whereNotNull('skin')->selectRaw('skin, COUNT(*) as count')->groupBy('skin')->get();
                $scalpStats = (clone $baseQuery)->whereNotNull('scalp')->selectRaw('scalp, COUNT(*) as count')->groupBy('scalp')->get();
                $eyeStats = (clone $baseQuery)->whereNotNull('eye')->selectRaw('eye, COUNT(*) as count')->groupBy('eye')->get();
                $earStats = (clone $baseQuery)->whereNotNull('ear')->selectRaw('ear, COUNT(*) as count')->groupBy('ear')->get();
                $noseStats = (clone $baseQuery)->whereNotNull('nose')->selectRaw('nose, COUNT(*) as count')->groupBy('nose')->get();
                $mouthStats = (clone $baseQuery)->whereNotNull('mouth')->selectRaw('mouth, COUNT(*) as count')->groupBy('mouth')->get();
                $throatStats = (clone $baseQuery)->whereNotNull('throat')->selectRaw('throat, COUNT(*) as count')->groupBy('throat')->get();
                $neckStats = (clone $baseQuery)->whereNotNull('neck')->selectRaw('neck, COUNT(*) as count')->groupBy('neck')->get();
                $abdomenStats = (clone $baseQuery)->whereNotNull('abdomen')->selectRaw('abdomen, COUNT(*) as count')->groupBy('abdomen')->get();
                
                // Handle lungs and heart with fallback to lungs_heart
                $lungsStats = (clone $baseQuery)->where(function($q) {
                    $q->whereNotNull('lungs')->orWhereNotNull('lungs_heart');
                })->selectRaw('COALESCE(lungs, lungs_heart) as lungs_value, COUNT(*) as count')->groupBy('lungs_value')->get();
                
                $heartStats = (clone $baseQuery)->where(function($q) {
                    $q->whereNotNull('heart')->orWhereNotNull('lungs_heart');
                })->selectRaw('COALESCE(heart, lungs_heart) as heart_value, COUNT(*) as count')->groupBy('heart_value')->get();
                
                // Oral Health Examination stats for assigned students
                $oralHealthExaminations = \App\Models\OralHealthExamination::whereIn('student_id', $assignedStudentIds)->count();
                $oralHealthTreatments = \App\Models\OralHealthTreatment::whereIn('student_id', $assignedStudentIds)->count();
                
                // Oral Health Conditions stats for assigned students
                $oralHealthConditions = \App\Models\OralHealthExamination::whereIn('student_id', $assignedStudentIds)
                    ->whereNotNull('conditions')
                    ->get()
                    ->flatMap(function ($exam) {
                        $conditions = [];
                        if ($exam->conditions) {
                            foreach ($exam->conditions as $conditionKey => $gradeData) {
                                foreach ($gradeData as $grade => $data) {
                                    if (isset($data['present']) && $data['present']) {
                                        $conditions[] = $conditionKey;
                                    }
                                }
                            }
                        }
                        return $conditions;
                    })
                    ->countBy()
                    ->map(function ($count, $condition) {
                        return ['condition' => $condition, 'count' => $count];
                    })
                    ->values();
            }
        } else {
            // Admins can see all students
            $totalStudents = Student::count();
            $femaleStudents = Student::where('sex', 'Female')->count();
            $maleStudents = Student::where('sex', 'Male')->count();

            // Build base query with year filter for all students
            $baseQuery = HealthExamination::query();
            if ($selectedYear !== 'All') {
                $baseQuery->where('school_year', $selectedYear);
            }

            // Nutritional Status BMI Distribution
            $nutritionalStatusBMI = (clone $baseQuery)->selectRaw('
                nutritional_status_bmi,
                COUNT(*) as count
            ')->groupBy('nutritional_status_bmi')->get();

            // Nutritional Status Height Distribution
            $nutritionalStatusHeight = (clone $baseQuery)->selectRaw('
                nutritional_status_height,
                COUNT(*) as count
            ')->groupBy('nutritional_status_height')->get();

            $dewormingDewormed = (clone $baseQuery)->where('deworming_status', 'dewormed')->count();
            $dewormingNotDewormed = (clone $baseQuery)->where('deworming_status', 'not_dewormed')->count();
            $ironPositive = (clone $baseQuery)->where('iron_supplementation', 'positive')->count();
            $ironNegative = (clone $baseQuery)->where('iron_supplementation', 'negative')->count();


            // Deformities stats for all students with flexible value matching
            $deformitiesNone = (clone $baseQuery)->whereIn('deformities', ['None', 'none', 'Normal', 'normal'])->count();
            $deformitiesCongenital = (clone $baseQuery)->whereIn('deformities', ['Congenital', 'congenital'])->count();
            $deformitiesAcquired = (clone $baseQuery)->whereIn('deformities', ['Acquired', 'acquired'])->count();
            $deformitiesOther = (clone $baseQuery)->whereNotNull('deformities')
                ->whereNotIn('deformities', ['None', 'none', 'Normal', 'normal', 'Congenital', 'congenital', 'Acquired', 'acquired'])
                ->count();

            // Individual examination stats for all students - return actual data
            $skinStats = (clone $baseQuery)->whereNotNull('skin')->selectRaw('skin, COUNT(*) as count')->groupBy('skin')->get();
            $scalpStats = (clone $baseQuery)->whereNotNull('scalp')->selectRaw('scalp, COUNT(*) as count')->groupBy('scalp')->get();
            $eyeStats = (clone $baseQuery)->whereNotNull('eye')->selectRaw('eye, COUNT(*) as count')->groupBy('eye')->get();
            $earStats = (clone $baseQuery)->whereNotNull('ear')->selectRaw('ear, COUNT(*) as count')->groupBy('ear')->get();
            $noseStats = (clone $baseQuery)->whereNotNull('nose')->selectRaw('nose, COUNT(*) as count')->groupBy('nose')->get();
            $mouthStats = (clone $baseQuery)->whereNotNull('mouth')->selectRaw('mouth, COUNT(*) as count')->groupBy('mouth')->get();
            $throatStats = (clone $baseQuery)->whereNotNull('throat')->selectRaw('throat, COUNT(*) as count')->groupBy('throat')->get();
            $neckStats = (clone $baseQuery)->whereNotNull('neck')->selectRaw('neck, COUNT(*) as count')->groupBy('neck')->get();
            $abdomenStats = (clone $baseQuery)->whereNotNull('abdomen')->selectRaw('abdomen, COUNT(*) as count')->groupBy('abdomen')->get();
            
            // Handle lungs and heart with fallback to lungs_heart
            $lungsStats = (clone $baseQuery)->where(function($q) {
                $q->whereNotNull('lungs')->orWhereNotNull('lungs_heart');
            })->selectRaw('COALESCE(lungs, lungs_heart) as lungs_value, COUNT(*) as count')->groupBy('lungs_value')->get();
            
            $heartStats = (clone $baseQuery)->where(function($q) {
                $q->whereNotNull('heart')->orWhereNotNull('lungs_heart');
            })->selectRaw('COALESCE(heart, lungs_heart) as heart_value, COUNT(*) as count')->groupBy('heart_value')->get();
            
            // Oral Health Examination stats for all students
            $oralHealthExaminations = \App\Models\OralHealthExamination::count();
            $oralHealthTreatments = \App\Models\OralHealthTreatment::count();
            
            // Oral Health Conditions stats for all students
            $oralHealthConditions = \App\Models\OralHealthExamination::whereNotNull('conditions')
                ->get()
                ->flatMap(function ($exam) {
                    $conditions = [];
                    if ($exam->conditions) {
                        // Handle both array and string conditions
                        $conditionsData = is_string($exam->conditions) ? json_decode($exam->conditions, true) : $exam->conditions;
                        
                        if (is_array($conditionsData)) {
                            foreach ($conditionsData as $conditionKey => $value) {
                                // Handle simple key-value pairs (from our seeder)
                                if (is_string($value)) {
                                    $conditions[] = $conditionKey;
                                }
                                // Handle nested structure (from form submissions)
                                elseif (is_array($value)) {
                                    foreach ($value as $grade => $data) {
                                        if (isset($data['present']) && $data['present']) {
                                            $conditions[] = $conditionKey;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    return $conditions;
                })
                ->countBy()
                ->map(function ($count, $condition) {
                    return ['condition' => $condition, 'count' => $count];
                })
                ->values();
        }

        \Log::info('Dashboard stats:', [
            'user_role' => $user->role,
            'total_students' => $totalStudents,
            'female_students' => $femaleStudents,
            'male_students' => $maleStudents
        ]);

        $dashboardData = [
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
            ],
            'deformities' => [
                'none' => $deformitiesNone,
                'congenital' => $deformitiesCongenital,
                'acquired' => $deformitiesAcquired,
                'other' => $deformitiesOther
            ],
            'examinations' => [
                'skin' => $skinStats->pluck('count', 'skin')->toArray(),
                'scalp' => $scalpStats->pluck('count', 'scalp')->toArray(),
                'eyes' => $eyeStats->pluck('count', 'eye')->toArray(),
                'ears' => $earStats->pluck('count', 'ear')->toArray(),
                'nose' => $noseStats->pluck('count', 'nose')->toArray(),
                'mouth' => $mouthStats->pluck('count', 'mouth')->toArray(),
                'throat' => $throatStats->pluck('count', 'throat')->toArray(),
                'neck' => $neckStats->pluck('count', 'neck')->toArray(),
                'lungs' => $lungsStats->pluck('count', 'lungs_value')->toArray(),
                'heart' => $heartStats->pluck('count', 'heart_value')->toArray(),
                'abdomen' => $abdomenStats->pluck('count', 'abdomen')->toArray()
            ],
            'oralHealth' => [
                'examinations' => $oralHealthExaminations,
                'treatments' => $oralHealthTreatments,
                'conditions' => $oralHealthConditions
            ]
        ];

        // Return JSON for AJAX requests
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'dashboardData' => $dashboardData,
                'userRole' => $user->role
            ]);
        }

        // Return Inertia view for regular requests
        return Inertia::render('Home', [
            'dashboardData' => $dashboardData,
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

    /**
     * Start the incident timer
     */
    public function startIncidentTimer(Incident $incident)
    {
        $incident->startTimer();
        
        return response()->json([
            'success' => true,
            'message' => 'Incident timer started successfully',
            'timer_status' => $incident->timer_status,
            'remaining_minutes' => $incident->getRemainingMinutes()
        ]);
    }

    /**
     * Pause the incident timer
     */
    public function pauseIncidentTimer(Incident $incident)
    {
        $incident->timer_status = 'paused';
        $incident->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Incident timer paused successfully',
            'timer_status' => $incident->timer_status
        ]);
    }

    /**
     * Resume the incident timer
     */
    public function resumeIncidentTimer(Incident $incident)
    {
        $incident->timer_status = 'active';
        $incident->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Incident timer resumed successfully',
            'timer_status' => $incident->timer_status,
            'remaining_minutes' => $incident->getRemainingMinutes()
        ]);
    }

    /**
     * Complete the incident timer
     */
    public function completeIncidentTimer(Incident $incident)
    {
        $incident->timer_status = 'completed';
        $incident->status = 'resolved';
        $incident->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Incident timer completed successfully',
            'timer_status' => $incident->timer_status
        ]);
    }

    /**
     * Get incident timer status for notifications
     */
    public function getIncidentTimerStatus($id)
    {
        $incident = Incident::findOrFail($id);
        
        return response()->json([
            'timer_status' => $incident->timer_status,
            'remaining_minutes' => $incident->getRemainingMinutes(),
            'is_expired' => $incident->isExpired()
        ]);
    }
}
