<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'created_by',
        'last_message_at'
    ];

    protected $casts = [
        'last_message_at' => 'datetime'
    ];

    /**
     * Get the creator of the conversation
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all participants in the conversation
     */
    public function participants()
    {
        return $this->belongsToMany(User::class, 'conversation_participants')
                    ->withPivot('joined_at', 'last_read_at')
                    ->withTimestamps();
    }

    /**
     * Get all messages in the conversation
     */
    public function messages()
    {
        return $this->hasMany(ConsultationMessage::class)->orderBy('created_at', 'asc');
    }

    /**
     * Get the latest message
     */
    public function latestMessage()
    {
        return $this->hasOne(ConsultationMessage::class)->latestOfMany();
    }

    /**
     * Get unread messages count for a user
     */
    public function getUnreadCountForUser($userId)
    {
        $participant = $this->participants()->where('user_id', $userId)->first();
        if (!$participant) return 0;

        $lastReadAt = $participant->pivot->last_read_at;
        
        return $this->messages()
                   ->where('sender_id', '!=', $userId)
                   ->when($lastReadAt, function($query) use ($lastReadAt) {
                       return $query->where('created_at', '>', $lastReadAt);
                   })
                   ->count();
    }

    /**
     * Mark conversation as read for a user
     */
    public function markAsReadForUser($userId)
    {
        $this->participants()
             ->where('user_id', $userId)
             ->update(['last_read_at' => now()]);
    }

    /**
     * Get conversation title for display
     */
    public function getDisplayTitleForUser($userId)
    {
        if ($this->type === 'group') {
            return $this->title ?: 'Group Chat';
        }

        // For direct messages, show the other participant's name
        $otherParticipant = $this->participants()
                                ->where('user_id', '!=', $userId)
                                ->first();

        return $otherParticipant ? $otherParticipant->full_name : 'Unknown User';
    }

    /**
     * Get other participant in direct conversation
     */
    public function getOtherParticipant($userId)
    {
        return $this->participants()
                   ->where('user_id', '!=', $userId)
                   ->first();
    }

    /**
     * Check if user is participant
     */
    public function hasParticipant($userId)
    {
        return $this->participants()->where('user_id', $userId)->exists();
    }

    /**
     * Find or create direct conversation between two users
     */
    public static function findOrCreateDirectConversation($user1Id, $user2Id)
    {
        // Find existing direct conversation between these users
        $conversation = self::where('type', 'direct')
            ->whereHas('participants', function($query) use ($user1Id) {
                $query->where('user_id', $user1Id);
            })
            ->whereHas('participants', function($query) use ($user2Id) {
                $query->where('user_id', $user2Id);
            })
            ->first();

        if (!$conversation) {
            // Create new conversation
            $conversation = self::create([
                'type' => 'direct',
                'created_by' => $user1Id
            ]);

            // Add participants
            $conversation->participants()->attach([
                $user1Id => ['joined_at' => now()],
                $user2Id => ['joined_at' => now()]
            ]);
        }

        return $conversation;
    }
}
