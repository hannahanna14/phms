<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Auto-update ended schedules to completed status
        Schedule::shouldBeCompleted()->update(['status' => 'completed']);
        
        $query = Schedule::with('creator')->orderBy('start_datetime', 'asc');

        // Filter by month/year if provided
        if ($request->has('month') && $request->has('year')) {
            $month = $request->month;
            $year = $request->year;
            $query->whereYear('start_datetime', $year)
                  ->whereMonth('start_datetime', $month);
        }

        // Filter by type if provided
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // Filter by status if provided
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Apply user-based filtering
        if (!in_array($user->role, ['admin', 'nurse'])) {
            // For teachers and other users, only show schedules they're involved in
            $query->where(function($q) use ($user) {
                $q->where('created_by', $user->id) // Schedules they created
                  ->orWhereJsonContains('selected_users', $user->id); // Schedules they're attendees of
            });
        }

        $schedules = $query->get()->filter(function($schedule) use ($user) {
            return $schedule->canBeViewedBy($user);
        });

        // Get upcoming schedules for sidebar (filtered by user permissions)
        $upcomingQuery = Schedule::with('creator')
            ->upcoming()
            ->orderBy('start_datetime', 'asc')
            ->limit(10); // Get more initially to filter down to 5

        if (!in_array($user->role, ['admin', 'nurse'])) {
            $upcomingQuery->where(function($q) use ($user) {
                $q->where('created_by', $user->id)
                  ->orWhereJsonContains('selected_users', $user->id);
            });
        }

        $upcomingSchedules = $upcomingQuery->get()
            ->filter(function($schedule) use ($user) {
                return $schedule->canBeViewedBy($user);
            })
            ->take(5);

        // Get today's schedules (filtered by user permissions)
        $todayQuery = Schedule::with('creator')
            ->today()
            ->orderBy('start_datetime', 'asc');

        if (!in_array($user->role, ['admin', 'nurse'])) {
            $todayQuery->where(function($q) use ($user) {
                $q->where('created_by', $user->id)
                  ->orWhereJsonContains('selected_users', $user->id);
            });
        }

        $todaySchedules = $todayQuery->get()->filter(function($schedule) use ($user) {
            return $schedule->canBeViewedBy($user);
        });

        return Inertia::render('Schedule/Index', [
            'schedules' => $schedules->values(), // Reset array keys after filtering
            'upcomingSchedules' => $upcomingSchedules->values(),
            'todaySchedules' => $todaySchedules->values(),
            'filters' => $request->only(['month', 'year', 'type', 'status']),
            'currentDate' => now()->format('Y-m-d')
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'nurse'])) {
            abort(403, 'Only administrators and nurses can create schedules.');
        }

        return Inertia::render('Schedule/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Only admins and nurses can create schedules
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'nurse'])) {
            abort(403, 'Only administrators and nurses can create schedules.');
        }

        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:5000',
                'start_datetime' => 'required|date',
                'end_datetime' => 'required|date|after:start_datetime',
                'type' => 'required|in:health_checkup,vaccination,meeting,training,other',
                'status' => 'required|in:scheduled,completed,cancelled',
                'location' => 'nullable|string|max:500',
                'attendees' => 'nullable|array',
                'attendees.*' => 'nullable|string|max:255',
                'selected_users' => 'nullable|array',
                'selected_users.*' => 'nullable|integer|exists:users,id',
                'notes' => 'nullable|string|max:5000'
            ]);

            $validated['created_by'] = auth()->id();
            
            // Ensure arrays are properly formatted
            $validated['attendees'] = $validated['attendees'] ?? [];
            $validated['selected_users'] = $validated['selected_users'] ?? [];

            \Log::info('Creating schedule with data:', $validated);

            $schedule = Schedule::create($validated);

            // Send notifications to attendees
            $this->sendScheduleNotifications($schedule, 'created');

            return redirect()->route('schedule-calendar.index')
                ->with('success', 'Schedule created successfully.');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Schedule validation failed:', [
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Schedule creation failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'Failed to create schedule: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = auth()->user();
        $schedule = Schedule::with('creator')->findOrFail($id);
        
        // Check if user can view this schedule
        if (!$schedule->canBeViewedBy($user)) {
            abort(403, 'You do not have permission to view this schedule.');
        }
        
        return Inertia::render('Schedule/Show', [
            'schedule' => $schedule
        ]);
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'nurse'])) {
            abort(403, 'Only administrators and nurses can edit schedules.');
        }

        $schedule = Schedule::findOrFail($id);
        
        // Check if user can view this schedule (additional security layer)
        if (!$schedule->canBeViewedBy($user)) {
            abort(403, 'You do not have permission to edit this schedule.');
        }
        
        return Inertia::render('Schedule/Edit', [
            'schedule' => $schedule
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'nurse'])) {
            abort(403, 'Only administrators and nurses can update schedules.');
        }

        $schedule = Schedule::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'type' => 'required|in:health_checkup,vaccination,meeting,training,other',
            'status' => 'required|in:scheduled,completed,cancelled',
            'location' => 'nullable|string|max:255',
            'attendees' => 'nullable|array',
            'selected_users' => 'nullable|array',
            'notes' => 'nullable|string'
        ]);

        // Store old attendees for comparison
        $oldAttendees = $schedule->selected_users ?? [];

        $schedule->update($validated);

        // Send notifications for schedule updates
        $this->sendScheduleNotifications($schedule, 'updated', $oldAttendees);

        return redirect()->route('schedule-calendar.index')
            ->with('success', 'Schedule updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'nurse'])) {
            abort(403, 'Only administrators and nurses can delete schedules.');
        }

        $schedule = Schedule::findOrFail($id);
        $schedule->delete();

        return redirect()->route('schedule-calendar.index')
            ->with('success', 'Schedule deleted successfully.');
    }

    /**
     * Get calendar events for API
     */
    public function getEvents(Request $request)
    {
        $user = auth()->user();
        $start = $request->start;
        $end = $request->end;

        // Auto-update ended schedules to completed status
        Schedule::shouldBeCompleted()->update(['status' => 'completed']);

        $query = Schedule::with('creator')
            ->whereBetween('start_datetime', [$start, $end]);

        // Apply the same filters as the main index method
        // Filter by type if provided
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // Filter by status if provided
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by month/year if provided (additional to date range)
        if ($request->has('month') && $request->has('year')) {
            $month = $request->month;
            $year = $request->year;
            $query->whereYear('start_datetime', $year)
                  ->whereMonth('start_datetime', $month);
        }

        // Apply user-based filtering for calendar events
        if (!in_array($user->role, ['admin', 'nurse'])) {
            $query->where(function($q) use ($user) {
                $q->where('created_by', $user->id)
                  ->orWhereJsonContains('selected_users', $user->id);
            });
        }

        $schedules = $query->get()
            ->filter(function($schedule) use ($user) {
                return $schedule->canBeViewedBy($user);
            })
            ->map(function ($schedule) {
                return [
                    'id' => $schedule->id,
                    'title' => $schedule->title,
                    'start' => $schedule->start_datetime->toISOString(),
                    'end' => $schedule->end_datetime->toISOString(),
                    'description' => $schedule->description,
                    'type' => $schedule->type,
                    'status' => $schedule->status,
                    'location' => $schedule->location,
                    'creator' => $schedule->creator->full_name ?? 'Unknown',
                    'backgroundColor' => $this->getTypeColor($schedule->type),
                    'borderColor' => $this->getTypeColor($schedule->type),
                ];
            });

        return response()->json($schedules->values());
    }

    /**
     * Get color based on schedule type
     */
    private function getTypeColor($type)
    {
        $colors = [
            'health_checkup' => '#10B981', // Green
            'vaccination' => '#3B82F6',    // Blue
            'meeting' => '#8B5CF6',        // Purple
            'training' => '#F59E0B',       // Orange
            'other' => '#6B7280'           // Gray
        ];

        return $colors[$type] ?? $colors['other'];
    }

    /**
     * Send notifications to schedule attendees
     */
    private function sendScheduleNotifications($schedule, $action, $oldAttendees = [])
    {
        // Get current attendees
        $currentAttendees = $schedule->selected_users ?? [];
        
        if ($action === 'created') {
            // Notify all selected attendees about new schedule
            $this->notifyAttendees($currentAttendees, $schedule, 'schedule_assigned');
        } elseif ($action === 'updated') {
            // Find newly added attendees
            $newAttendees = array_diff($currentAttendees, $oldAttendees);
            
            // Find removed attendees
            $removedAttendees = array_diff($oldAttendees, $currentAttendees);
            
            // Notify existing attendees about schedule changes
            $existingAttendees = array_intersect($currentAttendees, $oldAttendees);
            $this->notifyAttendees($existingAttendees, $schedule, 'schedule_updated');
            
            // Notify newly added attendees
            $this->notifyAttendees($newAttendees, $schedule, 'schedule_assigned');
            
            // Notify removed attendees about cancellation
            $this->notifyAttendees($removedAttendees, $schedule, 'schedule_removed');
        }
    }

    /**
     * Send notifications to specific attendees
     */
    private function notifyAttendees($userIds, $schedule, $notificationType)
    {
        if (empty($userIds)) {
            return;
        }

        // Create notification data
        $notificationData = [
            'schedule_id' => $schedule->id,
            'schedule_title' => $schedule->title,
            'schedule_type' => $schedule->type,
            'start_datetime' => $schedule->start_datetime->format('M d, Y h:i A'),
            'location' => $schedule->location,
            'creator_name' => $schedule->creator->full_name ?? 'System'
        ];

        // Send to notification system (using the existing notification store)
        foreach ($userIds as $userId) {
            $this->createNotificationForUser($userId, $notificationType, $notificationData);
        }
    }

    /**
     * Create notification for a specific user
     */
    private function createNotificationForUser($userId, $type, $data)
    {
        // This integrates with the existing notification system
        // We'll store it in a way that the frontend notification store can pick it up
        
        $messages = [
            'schedule_assigned' => "You've been added to the schedule: {$data['schedule_title']}",
            'schedule_updated' => "Schedule updated: {$data['schedule_title']}",
            'schedule_removed' => "You've been removed from schedule: {$data['schedule_title']}"
        ];

        $titles = [
            'schedule_assigned' => 'New Schedule Assignment',
            'schedule_updated' => 'Schedule Updated',
            'schedule_removed' => 'Schedule Removed'
        ];

        // Create notification record (you might want to create a notifications table)
        // For now, we'll use a simple approach that works with the existing system
        
        // Store notification data in a format the frontend can use
        $notification = [
            'id' => time() . rand(1000, 9999),
            'user_id' => $userId,
            'type' => 'schedule',
            'title' => $titles[$type] ?? 'Schedule Notification',
            'message' => $messages[$type] ?? 'Schedule notification',
            'data' => $data,
            'read' => false,
            'created_at' => now()->toISOString(),
            'schedule_id' => $data['schedule_id']
        ];

        // You could store this in a database table or cache
        // For now, we'll trigger it through a simple mechanism
        
        // Log the notification for debugging
        \Log::info('Schedule notification created', [
            'user_id' => $userId,
            'type' => $type,
            'schedule_id' => $data['schedule_id'],
            'title' => $notification['title']
        ]);
    }
}
