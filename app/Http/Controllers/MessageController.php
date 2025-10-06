<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $tab = $request->get('tab', 'inbox');

        $query = Message::with(['sender', 'receiver', 'relatedStudent']);

        switch ($tab) {
            case 'inbox':
                $query->receivedBy($user->id);
                break;
            case 'sent':
                $query->sentBy($user->id);
                break;
            case 'unread':
                $query->receivedBy($user->id)->unread();
                break;
            case 'urgent':
                $query->receivedBy($user->id)->byPriority('urgent');
                break;
        }

        $messages = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get unread count for badge
        $unreadCount = Message::receivedBy($user->id)->unread()->count();

        return Inertia::render('Messages/Index', [
            'messages' => $messages,
            'currentTab' => $tab,
            'unreadCount' => $unreadCount
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Get all users for recipient selection
        $users = User::where('id', '!=', auth()->id())
                    ->select('id', 'full_name', 'role')
                    ->orderBy('full_name')
                    ->get();

        // Get students for context linking
        $students = Student::select('id', 'full_name', 'grade_level', 'section')
                          ->orderBy('full_name')
                          ->get();

        // Pre-fill data if provided
        $preData = [
            'receiver_id' => $request->get('to'),
            'subject' => $request->get('subject'),
            'related_student_id' => $request->get('student_id'),
            'related_module' => $request->get('module'),
            'related_record_id' => $request->get('record_id'),
        ];

        return Inertia::render('Messages/Create', [
            'users' => $users,
            'students' => $students,
            'preData' => $preData
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => 'nullable|exists:users,id',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:personal,broadcast,system,urgent',
            'priority' => 'required|in:low,normal,high,urgent',
            'related_student_id' => 'nullable|exists:students,id',
            'related_module' => 'nullable|string',
            'related_record_id' => 'nullable|integer',
            'attachments' => 'nullable|array'
        ]);

        $validated['sender_id'] = auth()->id();

        Message::create($validated);

        return redirect()->route('messages.index')
            ->with('success', 'Message sent successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $message = Message::with(['sender', 'receiver', 'relatedStudent'])->findOrFail($id);
        
        // Check if user can view this message
        $user = auth()->user();
        if ($message->receiver_id !== $user->id && $message->sender_id !== $user->id && 
            !($message->type === 'broadcast' && $message->receiver_id === null)) {
            abort(403, 'Unauthorized to view this message.');
        }

        // Mark as read if user is the receiver
        if ($message->receiver_id === $user->id && !$message->is_read) {
            $message->markAsRead();
        }

        return Inertia::render('Messages/Show', [
            'message' => $message
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $message = Message::findOrFail($id);
        
        // Only sender can edit unsent messages (if we implement draft functionality)
        if ($message->sender_id !== auth()->id()) {
            abort(403, 'Unauthorized to edit this message.');
        }

        $users = User::where('id', '!=', auth()->id())
                    ->select('id', 'full_name', 'role')
                    ->orderBy('full_name')
                    ->get();

        $students = Student::select('id', 'full_name', 'grade_level', 'section')
                          ->orderBy('full_name')
                          ->get();

        return Inertia::render('Messages/Edit', [
            'message' => $message,
            'users' => $users,
            'students' => $students
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $message = Message::findOrFail($id);
        
        if ($message->sender_id !== auth()->id()) {
            abort(403, 'Unauthorized to update this message.');
        }

        $validated = $request->validate([
            'receiver_id' => 'nullable|exists:users,id',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:personal,broadcast,system,urgent',
            'priority' => 'required|in:low,normal,high,urgent',
            'related_student_id' => 'nullable|exists:students,id',
            'related_module' => 'nullable|string',
            'related_record_id' => 'nullable|integer',
            'attachments' => 'nullable|array'
        ]);

        $message->update($validated);

        return redirect()->route('messages.show', $message->id)
            ->with('success', 'Message updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $message = Message::findOrFail($id);
        
        // Only sender or receiver can delete
        $user = auth()->user();
        if ($message->sender_id !== $user->id && $message->receiver_id !== $user->id) {
            abort(403, 'Unauthorized to delete this message.');
        }

        $message->delete();

        return redirect()->route('messages.index')
            ->with('success', 'Message deleted successfully.');
    }

    /**
     * Mark message as read
     */
    public function markAsRead($id)
    {
        $message = Message::findOrFail($id);
        
        if ($message->receiver_id === auth()->id()) {
            $message->markAsRead();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Mark all messages as read
     */
    public function markAllAsRead()
    {
        Message::receivedBy(auth()->id())
               ->unread()
               ->update([
                   'is_read' => true,
                   'read_at' => now()
               ]);

        return response()->json(['success' => true]);
    }

    /**
     * Get unread message count
     */
    public function getUnreadCount()
    {
        $count = Message::receivedBy(auth()->id())->unread()->count();
        
        return response()->json(['count' => $count]);
    }
}
