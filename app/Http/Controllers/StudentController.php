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

    public function edit(HealthExamination $healthExamination)
    {
        return Inertia::render('HealthExamination/Edit', [
            'student' => $healthExamination->student,
            'examination' => $healthExamination
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
}
