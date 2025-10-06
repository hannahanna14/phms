<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'content',
        'message_type',
        'file_path',
        'file_name',
        'file_size'
    ];

    /**
     * Get the conversation this message belongs to
     */
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Get the sender of the message
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get formatted time for chat display
     */
    public function getFormattedTimeAttribute()
    {
        $now = now();
        $messageTime = $this->created_at;
        
        if ($messageTime->isToday()) {
            return $messageTime->format('g:i A');
        } elseif ($messageTime->isYesterday()) {
            return 'Yesterday ' . $messageTime->format('g:i A');
        } elseif ($messageTime->diffInDays($now) < 7) {
            return $messageTime->format('D g:i A');
        } else {
            return $messageTime->format('M j, g:i A');
        }
    }

    /**
     * Check if message is from current user
     */
    public function isFromUser($userId)
    {
        return $this->sender_id == $userId;
    }

    /**
     * Check if message has file attachment
     */
    public function hasFile()
    {
        return !empty($this->file_path);
    }

    /**
     * Get file URL
     */
    public function getFileUrlAttribute()
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute()
    {
        if (!$this->file_size) return null;
        
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Boot method to update conversation's last_message_at
     */
    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($message) {
            $message->conversation->update([
                'last_message_at' => $message->created_at
            ]);
        });
    }
}
