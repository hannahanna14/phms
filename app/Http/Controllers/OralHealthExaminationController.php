<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\OralHealthExamination;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OralHealthExaminationController extends Controller
{
    public function index()
    {
        $students = Student::select('id', 'full_name', 'age', 'sex')
            ->orderBy('full_name')
            ->get();
            
        return Inertia::render('OralHealth/Index', [
            'students' => $students
        ]);
    }

    public function show(Student $student)
    {
        $examinations = $student->oralHealthExaminations()
            ->orderBy('examination_date', 'desc')
            ->get();
            
        return Inertia::render('OralHealth/Show', [
            'student' => $student,
            'examinations' => $examinations
        ]);
    }

    public function create(Student $student)
    {
        return Inertia::render('OralHealthExamination/Create', [
            'student' => $student
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'index_dft' => 'nullable|integer',
            'number_of_teeth_decayed' => 'nullable|integer',
            'number_of_teeth_filled' => 'nullable|integer',
            'total_dft' => 'nullable|integer',
            'for_extraction' => 'nullable|integer',
            'for_filling' => 'nullable|integer',
        ]);

        OralHealthExamination::create($validated);

        return redirect()->route('oral-health-examination.show', $request->student_id);
    }

    public function edit(OralHealthExamination $oralHealthExamination)
    {
        return Inertia::render('OralHealthExamination/Edit', [
            'student' => $oralHealthExamination->student,
            'examination' => $oralHealthExamination
        ]);
    }

    public function update(Request $request, OralHealthExamination $oralHealthExamination)
    {
        $validated = $request->validate([
            'index_dft' => 'nullable|integer',
            'number_of_teeth_decayed' => 'nullable|integer',
            'number_of_teeth_filled' => 'nullable|integer',
            'total_dft' => 'nullable|integer',
            'for_extraction' => 'nullable|integer',
            'for_filling' => 'nullable|integer',
        ]);

        $oralHealthExamination->update($validated);

        return redirect()->route('oral-health-examination.show', $oralHealthExamination->student_id);
    }
}
