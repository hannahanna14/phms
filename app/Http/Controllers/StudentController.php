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
        // Total students
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

        return Inertia::render('Home', [
            'dashboardData' => [
                'totalStudents' => $totalStudents,
                'femaleStudents' => $femaleStudents,
                'maleStudents' => $maleStudents,
                'nutritionalStatusBMI' => $nutritionalStatusBMI,
                'nutritionalStatusHeight' => $nutritionalStatusHeight,
                'deworming' => [
                    'dewormed' => HealthExamination::where('deworming_status', 'dewormed')->count(),
                    'notDewormed' => HealthExamination::where('deworming_status', 'not_dewormed')->count()
                ],
                'ironSupplement' => [
                    'positive' => HealthExamination::where('iron_supplementation', 'positive')->count(),
                    'negative' => HealthExamination::where('iron_supplementation', 'negative')->count()
                ]
            ]
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
        $students = Student::with(['healthExaminations'])
            ->select('id', 'full_name', 'age', 'sex', 'grade_level', 'school_year')
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
        $grade = $request->query('grade', $student->grade_level);
        
        // Get school year based on grade level
        $schoolYear = $this->getSchoolYearForGrade($grade);
        
        $incidents = Incident::where('student_id', $student->id)
            ->where('grade_level', $grade)
            ->where('school_year', $schoolYear)
            ->latest()
            ->get();

        return Inertia::render('Pupil Health/Incident/Show', [
            'student' => $student,
            'incidents' => $incidents,
            'currentGrade' => $grade,
            'currentSchoolYear' => $schoolYear
        ]);
    }

    public function createIncident(Student $student)
    {
        return Inertia::render('Pupil Health/Incident/Create', [
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
            'grade_level' => 'required|string',
            'school_year' => 'required|string'
        ]);

        // Set default status to pending
        $validated['status'] = 'pending';

        Incident::create($validated);

        return redirect()->route('pupil-health.incident', [
            'student' => $validated['student_id'],
            'grade' => $validated['grade_level']
        ])->with('success', 'Incident report created successfully.');
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
