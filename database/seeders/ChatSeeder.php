<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Conversation;
use App\Models\ConsultationMessage;

class ChatSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create a test conversation
        $conversation = Conversation::create([
            'type' => 'direct',
            'created_by' => 1
        ]);

        // Add participants (user with themselves for demo)
        $conversation->participants()->attach([
            1 => ['joined_at' => now()],
        ]);

        // Create some sample messages
        ConsultationMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => 1,
            'content' => 'Hello! Welcome to the new PHMS Chat system! 🎉'
        ]);

        ConsultationMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => 1,
            'content' => 'This is a modern messaging interface similar to WhatsApp or Messenger. You can now have real-time conversations!'
        ]);

        ConsultationMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => 1,
            'content' => 'Features include:
- Real-time messaging
- Chat bubbles
- Message timestamps
- Conversation list
- Unread message counts
- File sharing (coming soon)'
        ]);
    }
}
