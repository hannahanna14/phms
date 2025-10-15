<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\ConsultationMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConsultationController extends Controller
{
    /**
     * Display consultation interface
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Get user's conversations
        $conversations = Conversation::whereHas('participants', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['participants', 'latestMessage.sender'])
            ->orderBy('last_message_at', 'desc')
            ->get()
            ->map(function($conversation) use ($user) {
                return [
                    'id' => $conversation->id,
                    'title' => $conversation->getDisplayTitleForUser($user->id),
                    'type' => $conversation->type,
                    'participants' => $conversation->participants->map(function($participant) {
                        return [
                            'id' => $participant->id,
                            'name' => $participant->full_name,
                            'role' => $participant->role
                        ];
                    }),
                    'latest_message' => $conversation->latestMessage ? [
                        'content' => $conversation->latestMessage->content,
                        'sender_name' => $conversation->latestMessage->sender->full_name,
                        'created_at' => $conversation->latestMessage->created_at,
                        'formatted_time' => $conversation->latestMessage->formatted_time
                    ] : null,
                    'unread_count' => $conversation->getUnreadCountForUser($user->id),
                    'last_message_at' => $conversation->last_message_at
                ];
            });

        // Get selected conversation if provided
        $selectedConversation = null;
        $messages = [];
        
        if ($request->has('conversation')) {
            $conversationId = $request->get('conversation');
            $selectedConversation = Conversation::with(['participants', 'messages.sender'])
                ->where('id', $conversationId)
                ->first();
                
            if ($selectedConversation && $selectedConversation->hasParticipant($user->id)) {
                $messages = $selectedConversation->messages->map(function($message) use ($user) {
                    return [
                        'id' => $message->id,
                        'content' => $message->content,
                        'message_type' => $message->message_type,
                        'file_path' => $message->file_path,
                        'file_name' => $message->file_name,
                        'file_size' => $message->formatted_file_size,
                        'sender' => [
                            'id' => $message->sender->id,
                            'name' => $message->sender->full_name,
                            'role' => $message->sender->role
                        ],
                        'is_own' => $message->isFromUser($user->id),
                        'created_at' => $message->created_at,
                        'formatted_time' => $message->formatted_time
                    ];
                });
                
                // Mark conversation as read
                $selectedConversation->markAsReadForUser($user->id);
            }
        }

        // Get available users for consultations based on role restrictions
        $users = $this->getAvailableUsersForConsultation($user);

        return Inertia::render('Consultation/Index', [
            'conversations' => $conversations,
            'selectedConversation' => $selectedConversation ? [
                'id' => $selectedConversation->id,
                'title' => $selectedConversation->getDisplayTitleForUser($user->id),
                'type' => $selectedConversation->type,
                'participants' => $selectedConversation->participants
            ] : null,
            'messages' => $messages,
            'users' => $users
        ]);
    }

    /**
     * Start new consultation
     */
    public function startConversation(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id|different:' . auth()->id()
        ]);

        $currentUser = auth()->user();
        $targetUser = User::findOrFail($request->user_id);
        
        // Validate consultation permissions
        if (!$this->canStartConsultation($currentUser, $targetUser)) {
            abort(403, 'You cannot start a consultation with this user. Teachers can only consult with nurses.');
        }

        $conversation = Conversation::findOrCreateDirectConversation(
            auth()->id(), 
            $request->user_id
        );

        return redirect()->route('consultation.index', ['conversation' => $conversation->id]);
    }

    /**
     * Send message
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'content' => 'required|string|max:1000',
            'message_type' => 'in:text,image,file,system'
        ]);

        $conversation = Conversation::findOrFail($request->conversation_id);
        
        // Check if user is participant
        if (!$conversation->hasParticipant(auth()->id())) {
            abort(403, 'You are not a participant in this conversation.');
        }

        $message = ConsultationMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => auth()->id(),
            'content' => $request->content,
            'message_type' => $request->message_type ?? 'text'
        ]);

        return response()->json([
            'message' => [
                'id' => $message->id,
                'content' => $message->content,
                'message_type' => $message->message_type,
                'sender' => [
                    'id' => $message->sender->id,
                    'name' => $message->sender->full_name,
                    'role' => $message->sender->role
                ],
                'is_own' => true,
                'created_at' => $message->created_at,
                'formatted_time' => $message->formatted_time
            ]
        ]);
    }

    /**
     * Get conversation messages (for polling/refresh)
     */
    public function getMessages(Request $request, $conversationId)
    {
        $conversation = Conversation::findOrFail($conversationId);
        
        if (!$conversation->hasParticipant(auth()->id())) {
            abort(403, 'You are not a participant in this conversation.');
        }

        $afterTimestamp = $request->get('after');
        
        $query = $conversation->messages()->with('sender');
        
        if ($afterTimestamp) {
            $query->after($afterTimestamp);
        }
        
        $messages = $query->get()->map(function($message) {
            return [
                'id' => $message->id,
                'content' => $message->content,
                'message_type' => $message->message_type,
                'sender' => [
                    'id' => $message->sender->id,
                    'name' => $message->sender->full_name,
                    'role' => $message->sender->role
                ],
                'is_own' => $message->isFromUser(auth()->id()),
                'created_at' => $message->created_at,
                'formatted_time' => $message->formatted_time
            ];
        });

        return response()->json(['messages' => $messages]);
    }

    /**
     * Mark conversation as read
     */
    public function markAsRead($conversationId)
    {
        $conversation = Conversation::findOrFail($conversationId);
        
        if ($conversation->hasParticipant(auth()->id())) {
            $conversation->markAsReadForUser(auth()->id());
        }

        return response()->json(['success' => true]);
    }

    /**
     * Get available users for consultation based on role restrictions
     */
    private function getAvailableUsersForConsultation($user)
    {
        $query = User::where('id', '!=', $user->id)
                    ->select('id', 'full_name', 'role')
                    ->orderBy('full_name');

        // Apply role-based restrictions
        if ($user->role === 'teacher') {
            // Teachers can consult with nurses and admins
            $query->whereIn('role', ['nurse', 'admin']);
        } elseif ($user->role === 'nurse') {
            // Nurses can consult with admins and teachers
            $query->whereIn('role', ['admin', 'teacher']);
        } elseif ($user->role === 'admin') {
            // Admins can consult with anyone
            $query->whereIn('role', ['teacher', 'nurse']);
        }

        return $query->get();
    }

    /**
     * Check if current user can start consultation with target user
     */
    private function canStartConsultation($currentUser, $targetUser)
    {
        // Admin can consult with anyone
        if ($currentUser->role === 'admin') {
            return in_array($targetUser->role, ['teacher', 'nurse']);
        }

        // Teacher can consult with nurses and admins
        if ($currentUser->role === 'teacher') {
            return in_array($targetUser->role, ['nurse', 'admin']);
        }

        // Nurse can consult with admins and teachers
        if ($currentUser->role === 'nurse') {
            return in_array($targetUser->role, ['admin', 'teacher']);
        }

        return false;
    }
}
