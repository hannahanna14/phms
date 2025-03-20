<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\HealthExamination;
use App\Models\OralHealthExamination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class PupilHealthController extends Controller
{
    public function showHealthExam(Student $student)
    {
        // Fetch the latest health examination for this student
        $healthExamination = HealthExamination::where('student_id', $student->id)
            ->latest()
            ->first();

        // Log for debugging
        Log::info('Showing Health Exam for Student', [
            'student_id' => $student->id,
            'health_examination' => $healthExamination
        ]);

        return Inertia::render('HealthExamination/Show', [
            'student' => $student,
            'healthExamination' => $healthExamination
        ]);
    }

    public function createHealthExam()
    {
        // Return a view for creating a new health examination
        return Inertia::render('HealthExamination/Create');
    }
}
