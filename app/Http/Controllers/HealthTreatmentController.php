<?php

namespace App\Http\Controllers;

use App\Models\HealthTreatment;
use App\Models\Student;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HealthTreatmentController extends Controller
{
    public function create(Student $student)
    {
        return Inertia::render('HealthTreatment/Create', [
            'student' => $student
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'date' => 'required|date',
            'title' => 'required|string|max:255',
            'chief_complaint' => 'required|string',
            'treatment' => 'required|string',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'remarks' => 'nullable|string',
            'grade_level' => 'required|string',
            'school_year' => 'required|string',
        ]);

        HealthTreatment::create($validated);

        return redirect()->route('pupil-health.health-exam.show', [
            'student' => $validated['student_id'],
            'grade' => $validated['grade_level']
        ])->with('success', 'Health treatment record added successfully.');
    }

    public function index(Student $student, Request $request)
    {
        $query = HealthTreatment::where('student_id', $student->id);
        
        // Filter by grade level if provided
        if ($request->has('grade') && $request->grade) {
            // Convert display format to database format (e.g., "Grade 4" becomes "4")
            $gradeLevel = str_replace('Grade ', '', $request->grade);
            $query->where('grade_level', $gradeLevel);
        }
        
        $treatments = $query->orderBy('date', 'desc')->get();

        return response()->json([
            'data' => $treatments
        ]);
    }

    public function update(Request $request, HealthTreatment $healthTreatment)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        $healthTreatment->update($validated);

        return response()->json($healthTreatment);
    }
}
