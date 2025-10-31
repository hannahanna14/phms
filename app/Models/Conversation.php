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
        
        // If user has never read the conversation, only count messages from the last 24 hours
        if (!$lastReadAt) {
            return $this->messages()
                       ->where('sender_id', '!=', $userId)
                       ->where('created_at', '>', now()->subDay())
                       ->count();
        }
        
        // Count messages created AFTER the last read timestamp
        return $this->messages()
                   ->where('sender_id', '!=', $userId)
                   ->where('created_at', '>', $lastReadAt)
                   ->count();
    }

    /**
     * Mark conversation as read for a user
     */
    public function markAsReadForUser($userId)
    {
        // Use current timestamp to mark as read
        $readTimestamp = now();
        
        // Update using DB query directly to ensure it works
        $updated = \DB::table('conversation_participants')
            ->where('conversation_id', $this->id)
            ->where('user_id', $userId)
            ->update(['last_read_at' => $readTimestamp, 'updated_at' => now()]);
        
        // Log for debugging
        \Log::info('Updated last_read_at', [
            'conversation_id' => $this->id,
            'user_id' => $userId,
            'last_read_at' => $readTimestamp,
            'rows_updated' => $updated,
            'participant_exists' => \DB::table('conversation_participants')
                ->where('conversation_id', $this->id)
                ->where('user_id', $userId)
                ->exists()
        ]);
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
