<?php

namespace App\Http\Controllers;

use App\Models\OralHealthTreatment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
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
            Log::error('Error creating oral health treatment: ' . $e->getMessage());
            
            return back()->withInput()->withErrors([
                'error' => 'Failed to save oral health treatment. ' . $e->getMessage()
            ]);
        }
    }

    public function index(Student $student, Request $request)
    {
        try {
            $grade = $request->query('grade', $student->grade_level);
            $schoolYear = $this->getSchoolYearForGrade($grade);
            
            // Check if table exists first
            if (!Schema::hasTable('oral_health_treatments')) {
                return response()->json([]);
            }
            
            $query = OralHealthTreatment::where('student_id', $student->id);
            
            // Only filter by grade if columns exist
            if (Schema::hasColumn('oral_health_treatments', 'grade_level')) {
                $query->where('grade_level', $grade);
            }

            $treatments = $query->orderBy('date', 'desc')->get();

            return response()->json($treatments);
        } catch (\Exception $e) {
            Log::error('Error fetching oral health treatments: ' . $e->getMessage());
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
