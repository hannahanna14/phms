<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\HealthExamination;
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
}
