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
        // Only nurses can create health treatments
        if (auth()->user()->role !== 'nurse') {
            abort(403, 'Access denied. Only nurses can create health treatments.');
        }
        
        return Inertia::render('HealthTreatment/Create', [
            'student' => $student
        ]);
    }

    public function store(Request $request)
    {
        // Only nurses can store health treatments
        if (auth()->user()->role !== 'nurse') {
            abort(403, 'Access denied. Only nurses can store health treatments.');
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

        // Debug log the validated data
        \Log::info('Health Treatment Store - Validated Data:', $validated);

        $treatment = HealthTreatment::create($validated);
        
        // Start the timer automatically when treatment is created
        $treatment->startTimer();

        return redirect()->route('pupil-health.health-exam.show', [
            'student' => $validated['student_id'],
            'grade' => $validated['grade_level']
        ])->with('success', 'Health treatment created successfully.');
    }

    public function index(Student $student, Request $request)
    {
        try {
            $query = HealthTreatment::where('student_id', $student->id);
            
            // Debug: Show all treatments and their grade_level values
            $allTreatments = HealthTreatment::where('student_id', $student->id)->get();
            \Log::info('All treatments for student:', [
                'student_id' => $student->id,
                'total_count' => $allTreatments->count(),
                'grade_levels' => $allTreatments->pluck('grade_level')->toArray(),
                'all_treatments' => $allTreatments->toArray()
            ]);
            
            // Filter by grade level if provided - handle multiple formats
            if ($request->has('grade') && $request->grade) {
                $requestGrade = $request->grade;
                
                \Log::info('Filtering by grade:', [
                    'original_grade' => $requestGrade,
                    'existing_grades' => $allTreatments->pluck('grade_level')->unique()->toArray()
                ]);
                
                // Handle both formats: "6" and "Grade 6", "Kinder 2" etc.
                $query->where(function($q) use ($requestGrade) {
                    $q->where('grade_level', $requestGrade);
                    
                    // If numeric grade level (e.g., "6"), also try "Grade 6" format
                    if (is_numeric($requestGrade)) {
                        $q->orWhere('grade_level', "Grade {$requestGrade}");
                    }
                    
                    // If "Grade X" format, also try just the number
                    if (preg_match('/^Grade (\d+)$/', $requestGrade, $matches)) {
                        $q->orWhere('grade_level', $matches[1]);
                    }
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
            
            \Log::info('Filtered Treatment Result:', [
                'grade_param' => $request->grade,
                'count' => $treatments->count(),
                'treatments' => $treatments->toArray()
            ]);

            return response()->json([
                'data' => $treatments
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching health treatments: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function edit(HealthTreatment $healthTreatment)
    {
        // Check if treatment can be edited
        if (!$healthTreatment->canEdit()) {
            return redirect()->back()->with('error', 'This treatment can no longer be edited (timer expired).');
        }

        return Inertia::render('HealthTreatment/Edit', [
            'treatment' => $healthTreatment,
            'student' => $healthTreatment->student,
            'timer_status' => $healthTreatment->getTimerStatus(),
            'remaining_minutes' => $healthTreatment->getRemainingMinutes()
        ]);
    }

    public function show(HealthTreatment $healthTreatment)
    {
        return Inertia::render('HealthTreatment/Show', [
            'treatment' => $healthTreatment,
            'student' => $healthTreatment->student,
            'timer_status' => $healthTreatment->getTimerStatus(),
            'remaining_minutes' => $healthTreatment->getRemainingMinutes()
        ]);
    }

    public function update(Request $request, HealthTreatment $healthTreatment)
    {
        // Check if treatment can be edited
        if (!$healthTreatment->canEdit()) {
            return response()->json(['error' => 'This treatment can no longer be edited (timer expired).'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'chief_complaint' => 'sometimes|required|string',
            'treatment' => 'sometimes|required|string',
            'remarks' => 'nullable|string',
        ]);

        $healthTreatment->update($validated);

        return response()->json([
            'treatment' => $healthTreatment,
            'timer_status' => $healthTreatment->getTimerStatus(),
            'can_edit' => $healthTreatment->canEdit()
        ]);
    }

    /**
     * Get current timer status for a treatment
     */
    public function getTimerStatus(HealthTreatment $healthTreatment)
    {
        return response()->json([
            'timer_status' => $healthTreatment->getTimerStatus(),
            'remaining_minutes' => $healthTreatment->getRemainingMinutes(),
            'can_edit' => $healthTreatment->canEdit(),
            'is_expired' => $healthTreatment->isExpired()
        ]);
    }

    /**
     * Start the treatment timer
     */
    public function startTimer(HealthTreatment $healthTreatment)
    {
        $healthTreatment->startTimer();
        
        return response()->json([
            'success' => true,
            'message' => 'Timer started successfully',
            'timer_status' => $healthTreatment->getTimerStatus(),
            'remaining_minutes' => $healthTreatment->getRemainingMinutes()
        ]);
    }

    /**
     * Pause the treatment timer
     */
    public function pauseTimer(HealthTreatment $healthTreatment)
    {
        $healthTreatment->pauseTimer();
        
        return response()->json([
            'success' => true,
            'message' => 'Timer paused successfully',
            'timer_status' => $healthTreatment->getTimerStatus()
        ]);
    }

    /**
     * Resume the treatment timer
     */
    public function resumeTimer(HealthTreatment $healthTreatment)
    {
        $healthTreatment->resumeTimer();
        
        return response()->json([
            'success' => true,
            'message' => 'Timer resumed successfully',
            'timer_status' => $healthTreatment->getTimerStatus(),
            'remaining_minutes' => $healthTreatment->getRemainingMinutes()
        ]);
    }

    /**
     * Complete the treatment timer
     */
    public function completeTimer(HealthTreatment $healthTreatment)
    {
        $healthTreatment->completeTimer();
        
        return response()->json([
            'success' => true,
            'message' => 'Timer completed successfully',
            'timer_status' => $healthTreatment->getTimerStatus()
        ]);
    }
}
