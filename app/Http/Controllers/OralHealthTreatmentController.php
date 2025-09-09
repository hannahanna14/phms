<?php

namespace App\Http\Controllers;

use App\Models\OralHealthTreatment;
use App\Models\Student;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OralHealthTreatmentController extends Controller
{
    public function create(Student $student)
    {
        return Inertia::render('OralHealthTreatment/Create', [
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
            'remarks' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'grade_level' => 'required|string|min:1',
            'school_year' => 'required|string',
        ]);

        // Debug log the validated data
        \Log::info('Oral Health Treatment Store - Validated Data:', $validated);

        OralHealthTreatment::create($validated);

        return redirect()->route('pupil-health.oral-health.show', [
            'student' => $validated['student_id'],
            'grade' => $validated['grade_level']
        ])->with('success', 'Oral health treatment created successfully.');
    }

    public function index(Student $student, Request $request)
    {
        try {
            $query = OralHealthTreatment::where('student_id', $student->id);
            
            // Debug: Show all treatments and their grade_level values
            $allTreatments = OralHealthTreatment::where('student_id', $student->id)->get();
            \Log::info('All oral health treatments for student:', [
                'student_id' => $student->id,
                'total_count' => $allTreatments->count(),
                'grade_levels' => $allTreatments->pluck('grade_level')->toArray(),
                'all_treatments' => $allTreatments->toArray()
            ]);
            
            // Filter by grade level if provided - handle multiple formats
            if ($request->has('grade') && $request->grade) {
                // Convert display format to database format (e.g., "Grade 4" becomes "4")
                $gradeLevel = str_replace('Grade ', '', $request->grade);
                
                \Log::info('Filtering oral health treatments by grade:', [
                    'original_grade' => $request->grade,
                    'converted_grade' => $gradeLevel,
                    'existing_grades' => $allTreatments->pluck('grade_level')->unique()->toArray()
                ]);
                
                // Handle multiple grade level formats like HealthTreatmentController
                $query->where(function($q) use ($gradeLevel, $request) {
                    $q->where('grade_level', $gradeLevel)           // "4"
                      ->orWhere('grade_level', $request->grade)     // "Grade 4"  
                      ->orWhere(function($subQ) use ($gradeLevel) {
                          if ($gradeLevel == '4') {
                              $subQ->whereNull('grade_level')
                                   ->orWhere('grade_level', '');
                          }
                      });
                });
            }

            $treatments = $query->orderBy('date', 'desc')->get();
            
            \Log::info('Filtered Oral Health Treatment Result:', [
                'grade_param' => $request->grade,
                'count' => $treatments->count(),
                'treatments' => $treatments->toArray()
            ]);

            return response()->json([
                'data' => $treatments
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching oral health treatments: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, OralHealthTreatment $oralHealthTreatment)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        $oralHealthTreatment->update($validated);

        return response()->json($oralHealthTreatment);
    }
}
