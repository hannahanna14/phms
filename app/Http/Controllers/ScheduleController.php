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

        $schedules = $query->get();

        // Get upcoming schedules for sidebar
        $upcomingSchedules = Schedule::with('creator')
            ->upcoming()
            ->orderBy('start_datetime', 'asc')
            ->limit(5)
            ->get();

        // Get today's schedules
        $todaySchedules = Schedule::with('creator')
            ->today()
            ->orderBy('start_datetime', 'asc')
            ->get();

        return Inertia::render('Schedule/Index', [
            'schedules' => $schedules,
            'upcomingSchedules' => $upcomingSchedules,
            'todaySchedules' => $todaySchedules,
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

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'type' => 'required|in:health_checkup,vaccination,meeting,training,other',
            'status' => 'required|in:scheduled,completed,cancelled',
            'location' => 'nullable|string|max:255',
            'attendees' => 'nullable|array',
            'notes' => 'nullable|string'
        ]);

        $validated['created_by'] = auth()->id();

        Schedule::create($validated);

        return redirect()->route('schedule-calendar.index')
            ->with('success', 'Schedule created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $schedule = Schedule::with('creator')->findOrFail($id);
        
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
            'notes' => 'nullable|string'
        ]);

        $schedule->update($validated);

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
        $start = $request->start;
        $end = $request->end;

        $schedules = Schedule::with('creator')
            ->whereBetween('start_datetime', [$start, $end])
            ->get()
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

        return response()->json($schedules);
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
}
