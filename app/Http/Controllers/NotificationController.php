<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HealthTreatment;
use App\Models\OralHealthTreatment;
use App\Models\Incident;
use Inertia\Inertia;

class NotificationController extends Controller
{
    /**
     * Check for timer notifications across all treatment types
     */
    public function checkTimerNotifications()
    {
        $user = auth()->user();
        $treatments = [];

        // Teachers should not receive timer notifications
        if ($user->role === 'teacher') {
            return response()->json([
                'treatments' => [],
                'count' => 0
            ]);
        }

        // Check Health Treatment timers (admins and nurses only)
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

        // Check Oral Health Treatment timers (admins and nurses only)
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

        // Check Incident timers (admins and nurses only)
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
        $user = auth()->user();
        
        // Teachers should not receive unrecorded student notifications
        if ($user->role === 'teacher') {
            return response()->json([
                'notifications' => [],
                'unrecorded_health' => 0,
                'unrecorded_oral_health' => 0
            ]);
        }
        
        // Students without any health examinations (admins and nurses only)
        $studentsWithoutHealthExam = \App\Models\Student::whereDoesntHave('healthExaminations')
            ->limit(10)
            ->get();

        // Students without any oral health examinations (admins and nurses only)
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

    /**
     * Check for schedule notifications for the current user
     */
    public function checkScheduleNotifications()
    {
        $user = auth()->user();
        $notifications = [];

        // Get upcoming schedules where the user can view them
        $upcomingQuery = \App\Models\Schedule::where('start_datetime', '>', now())
            ->where('start_datetime', '<=', now()->addDays(7)) // Next 7 days
            ->where('status', 'scheduled');

        // Apply user-based filtering
        if (!in_array($user->role, ['admin', 'nurse'])) {
            $upcomingQuery->where(function($q) use ($user) {
                $q->where('created_by', $user->id)
                  ->orWhereJsonContains('selected_users', $user->id);
            });
        }

        $upcomingSchedules = $upcomingQuery->get()
            ->filter(function ($schedule) use ($user) {
                return $schedule->canBeViewedBy($user);
            });

        foreach ($upcomingSchedules as $schedule) {
            $timeUntil = now()->diffInHours($schedule->start_datetime);
            
            // Create notifications for schedules starting soon
            if ($timeUntil <= 24 && $timeUntil > 0) {
                $notifications[] = [
                    'id' => 'schedule_' . $schedule->id . '_reminder',
                    'type' => 'schedule_reminder',
                    'title' => 'Upcoming Schedule',
                    'message' => "'{$schedule->title}' starts in " . ($timeUntil < 1 ? 'less than an hour' : ceil($timeUntil) . ' hours'),
                    'schedule_id' => $schedule->id,
                    'schedule_title' => $schedule->title,
                    'schedule_type' => $schedule->type,
                    'start_datetime' => $schedule->start_datetime->format('M d, Y h:i A'),
                    'location' => $schedule->location,
                    'priority' => $timeUntil <= 2 ? 'high' : 'medium',
                    'created_at' => now()->toISOString()
                ];
            }
        }

        // Get today's schedules
        $todayQuery = \App\Models\Schedule::whereDate('start_datetime', today())
            ->where('status', 'scheduled');

        // Apply user-based filtering
        if (!in_array($user->role, ['admin', 'nurse'])) {
            $todayQuery->where(function($q) use ($user) {
                $q->where('created_by', $user->id)
                  ->orWhereJsonContains('selected_users', $user->id);
            });
        }

        $todaySchedules = $todayQuery->get()
            ->filter(function ($schedule) use ($user) {
                return $schedule->canBeViewedBy($user);
            });

        foreach ($todaySchedules as $schedule) {
            $notifications[] = [
                'id' => 'schedule_' . $schedule->id . '_today',
                'type' => 'schedule_today',
                'title' => 'Today\'s Schedule',
                'message' => "You have '{$schedule->title}' scheduled today at " . $schedule->start_datetime->format('h:i A'),
                'schedule_id' => $schedule->id,
                'schedule_title' => $schedule->title,
                'schedule_type' => $schedule->type,
                'start_datetime' => $schedule->start_datetime->format('M d, Y h:i A'),
                'location' => $schedule->location,
                'priority' => 'medium',
                'created_at' => now()->toISOString()
            ];
        }

        return response()->json([
            'notifications' => $notifications,
            'upcoming_count' => $upcomingSchedules->count(),
            'today_count' => $todaySchedules->count()
        ]);
    }

    /**
     * Display all notifications page
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get all notifications from the notification store
        // For now, we'll return empty array since notifications are handled client-side
        // In a real implementation, you might want to store notifications in database
        $notifications = [];
        
        return Inertia::render('Notifications/Index', [
            'notifications' => $notifications
        ]);
    }
}
