<?php

namespace App\Http\Controllers;

use App\Models\OralHealthTreatment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class OralHealthTreatmentController extends Controller
{
    public function create(Student $student)
    {
        // Only nurses can create oral health treatments
        if (auth()->user()->role !== 'nurse') {
            abort(403, 'Access denied. Only nurses can create oral health treatments.');
        }
        
        return Inertia::render('OralHealthTreatment/Create', [
            'student' => $student
        ]);
    }

    public function store(Request $request)
    {
        // Only nurses can store oral health treatments
        if (auth()->user()->role !== 'nurse') {
            abort(403, 'Access denied. Only nurses can store oral health treatments.');
        }
        
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'date' => 'required|date',
            'title' => 'required|string|max:255',
            'chief_complaint' => 'required|string',
            'treatment' => 'required|string',
            'remarks' => 'nullable|string',
            'grade_level' => 'required|string|min:1',
            'school_year' => 'required|string',
        ]);

        // Add the current user's name as attended_by
        $validated['attended_by'] = auth()->user()->name ?? 'School Nurse';

        // Debug log the validated data
        Log::info('Oral Health Treatment Store - Validated Data:', $validated);

        $treatment = OralHealthTreatment::create($validated);
        
        // Start the timer automatically when treatment is created
        $treatment->startTimer();

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
            Log::info('All oral health treatments for student:', [
                'student_id' => $student->id,
                'total_count' => $allTreatments->count(),
                'grade_levels' => $allTreatments->pluck('grade_level')->toArray(),
                'all_treatments' => $allTreatments->toArray()
            ]);
            
            // Filter by grade level if provided - handle multiple formats
            if ($request->has('grade') && $request->grade) {
                // Convert display format to database format (e.g., "Grade 4" becomes "4")
                $gradeLevel = str_replace('Grade ', '', $request->grade);
                
                Log::info('Filtering oral health treatments by grade:', [
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
            
            // Add timer information to each treatment
            $treatments = $treatments->map(function ($treatment) {
                $timerStatus = $treatment->getTimerStatus();
                return [
                    'id' => $treatment->id,
                    'student_id' => $treatment->student_id,
                    'date' => $treatment->date,
                    'title' => $treatment->title,
                    'chief_complaint' => $treatment->chief_complaint,
                    'treatment' => $treatment->treatment,
                    'status' => $treatment->status,
                    'remarks' => $treatment->remarks,
                    'grade_level' => $treatment->grade_level,
                    'school_year' => $treatment->school_year,
                    'started_at' => $treatment->started_at,
                    'expires_at' => $treatment->expires_at,
                    'is_expired' => $treatment->is_expired,
                    'can_edit' => $treatment->canEdit(),
                    'remaining_minutes' => $treatment->getRemainingMinutes(),
                    'timer_status' => $timerStatus,
                    'created_at' => $treatment->created_at,
                    'updated_at' => $treatment->updated_at,
                ];
            });
            
            Log::info('Filtered Oral Health Treatment Result:', [
                'grade_param' => $request->grade,
                'count' => $treatments->count(),
                'treatments' => $treatments->toArray()
            ]);

            return response()->json([
                'data' => $treatments
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching oral health treatments: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, OralHealthTreatment $oralHealthTreatment)
    {
        // Check if treatment can be edited
        if (!$oralHealthTreatment->canEdit()) {
            return response()->json(['error' => 'This treatment can no longer be edited (timer expired).'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'chief_complaint' => 'sometimes|required|string',
            'treatment' => 'sometimes|required|string',
            'remarks' => 'nullable|string',
        ]);

        $oralHealthTreatment->update($validated);

        return response()->json([
            'treatment' => $oralHealthTreatment,
            'timer_status' => $oralHealthTreatment->getTimerStatus(),
            'can_edit' => $oralHealthTreatment->canEdit()
        ]);
    }

    public function edit(OralHealthTreatment $oralHealthTreatment)
    {
        // Check if treatment can be edited
        if (!$oralHealthTreatment->canEdit()) {
            return redirect()->back()->with('error', 'This treatment can no longer be edited (timer expired).');
        }

        return Inertia::render('OralHealthTreatment/Edit', [
            'treatment' => $oralHealthTreatment,
            'student' => $oralHealthTreatment->student,
            'timer_status' => $oralHealthTreatment->getTimerStatus(),
            'remaining_minutes' => $oralHealthTreatment->getRemainingMinutes()
        ]);
    }

    public function show(OralHealthTreatment $oralHealthTreatment)
    {
        return Inertia::render('OralHealthTreatment/Show', [
            'treatment' => $oralHealthTreatment,
            'student' => $oralHealthTreatment->student,
            'timer_status' => $oralHealthTreatment->getTimerStatus(),
            'remaining_minutes' => $oralHealthTreatment->getRemainingMinutes()
        ]);
    }

    /**
     * Start the treatment timer
     */
    public function startTimer(OralHealthTreatment $oralHealthTreatment)
    {
        $oralHealthTreatment->startTimer();
        
        return response()->json([
            'success' => true,
            'message' => 'Timer started successfully',
            'timer_status' => $oralHealthTreatment->getTimerStatus(),
            'remaining_minutes' => $oralHealthTreatment->getRemainingMinutes()
        ]);
    }

    /**
     * Pause the treatment timer
     */
    public function pauseTimer(OralHealthTreatment $oralHealthTreatment)
    {
        $oralHealthTreatment->pauseTimer();
        
        return response()->json([
            'success' => true,
            'message' => 'Timer paused successfully',
            'timer_status' => $oralHealthTreatment->getTimerStatus()
        ]);
    }

    /**
     * Resume the treatment timer
     */
    public function resumeTimer(OralHealthTreatment $oralHealthTreatment)
    {
        $oralHealthTreatment->resumeTimer();
        
        return response()->json([
            'success' => true,
            'message' => 'Timer resumed successfully',
            'timer_status' => $oralHealthTreatment->getTimerStatus(),
            'remaining_minutes' => $oralHealthTreatment->getRemainingMinutes()
        ]);
    }

    /**
     * Complete the treatment timer
     */
    public function completeTimer(OralHealthTreatment $oralHealthTreatment)
    {
        $oralHealthTreatment->completeTimer();
        
        return response()->json([
            'success' => true,
            'message' => 'Timer completed successfully',
            'timer_status' => $oralHealthTreatment->getTimerStatus()
        ]);
    }
}
