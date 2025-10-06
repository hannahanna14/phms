<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HealthTreatment;
use App\Models\OralHealthTreatment;
use App\Models\Incident;

class NotificationController extends Controller
{
    /**
     * Check for timer notifications across all treatment types
     */
    public function checkTimerNotifications()
    {
        $treatments = [];

        // Check Health Treatment timers
        $healthTreatments = HealthTreatment::with('student')
            ->where('timer_status', 'active')
            ->get();

        foreach ($healthTreatments as $treatment) {
            $remainingMinutes = $treatment->getRemainingMinutes();
            
            $treatments[] = [
                'id' => $treatment->id,
                'type' => 'health',
                'title' => $treatment->title,
                'chief_complaint' => $treatment->chief_complaint,
                'remaining_minutes' => $remainingMinutes,
                'timer_status' => $treatment->timer_status,
                'student' => [
                    'id' => $treatment->student->id,
                    'full_name' => $treatment->student->full_name
                ],
                // Flags to prevent duplicate notifications (will be handled by frontend)
                'thirty_min_notified' => false,
                'fifteen_min_notified' => false,
                'expired_notified' => false
            ];
            
            // Auto-update expired status
            if ($remainingMinutes <= 0 && $treatment->timer_status !== 'expired') {
                $treatment->update(['timer_status' => 'expired']);
            }
        }

        // Check Oral Health Treatment timers
        $oralHealthTreatments = OralHealthTreatment::with('student')
            ->where('timer_status', 'active')
            ->get();

        foreach ($oralHealthTreatments as $treatment) {
            $remainingMinutes = $treatment->getRemainingMinutes();
            
            $treatments[] = [
                'id' => $treatment->id,
                'type' => 'oral_health',
                'title' => $treatment->title,
                'chief_complaint' => $treatment->chief_complaint,
                'remaining_minutes' => $remainingMinutes,
                'timer_status' => $treatment->timer_status,
                'student' => [
                    'id' => $treatment->student->id,
                    'full_name' => $treatment->student->full_name
                ],
                'thirty_min_notified' => false,
                'fifteen_min_notified' => false,
                'expired_notified' => false
            ];
            
            // Auto-update expired status
            if ($remainingMinutes <= 0 && $treatment->timer_status !== 'expired') {
                $treatment->update(['timer_status' => 'expired']);
            }
        }

        // Check Incident timers
        $incidents = Incident::with('student')
            ->where('timer_status', 'active')
            ->get();

        foreach ($incidents as $incident) {
            $remainingMinutes = $incident->getRemainingMinutes();
            
            $treatments[] = [
                'id' => $incident->id,
                'type' => 'incident',
                'title' => $incident->complaint,
                'chief_complaint' => $incident->complaint,
                'remaining_minutes' => $remainingMinutes,
                'timer_status' => $incident->timer_status,
                'student' => [
                    'id' => $incident->student->id,
                    'full_name' => $incident->student->full_name
                ],
                'thirty_min_notified' => false,
                'fifteen_min_notified' => false,
                'expired_notified' => false
            ];
            
            // Auto-update expired status
            if ($remainingMinutes <= 0 && $incident->timer_status !== 'expired') {
                $incident->update(['timer_status' => 'expired']);
            }
        }

        return response()->json([
            'treatments' => $treatments,
            'count' => count($treatments)
        ]);
    }

    /**
     * Check for unrecorded students
     */
    public function checkUnrecordedStudents()
    {
        // Students without any health examinations
        $studentsWithoutHealthExam = \App\Models\Student::whereDoesntHave('healthExaminations')
            ->limit(10)
            ->get();

        // Students without any oral health examinations  
        $studentsWithoutOralHealth = \App\Models\Student::whereDoesntHave('oralHealthExaminations')
            ->limit(10)
            ->get();

        $notifications = [];

        // Individual notifications for first 5 students
        foreach ($studentsWithoutHealthExam->take(5) as $student) {
            $notifications[] = [
                'type' => 'unrecorded_student',
                'student_name' => $student->full_name,
                'missing_type' => 'health examination',
                'priority' => 'medium'
            ];
        }

        // Batch notification if more than 5
        if ($studentsWithoutHealthExam->count() > 5) {
            $notifications[] = [
                'type' => 'batch_unrecorded',
                'count' => $studentsWithoutHealthExam->count(),
                'record_type' => 'health examinations',
                'priority' => 'medium'
            ];
        }

        return response()->json([
            'notifications' => $notifications,
            'unrecorded_health' => $studentsWithoutHealthExam->count(),
            'unrecorded_oral_health' => $studentsWithoutOralHealth->count()
        ]);
    }
}
