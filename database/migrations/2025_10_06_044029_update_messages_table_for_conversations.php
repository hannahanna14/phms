<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // Add conversation_id
            $table->unsignedBigInteger('conversation_id')->after('id');
            
            // Remove old fields that don't fit chat model
            $table->dropColumn(['receiver_id', 'subject', 'type', 'priority', 'is_read', 'read_at']);
            
            // Add new chat-specific fields
            $table->enum('message_type', ['text', 'image', 'file', 'system'])->default('text')->after('content');
            $table->string('file_path')->nullable()->after('message_type');
            $table->string('file_name')->nullable()->after('file_path');
            $table->integer('file_size')->nullable()->after('file_name');
            
            // Add foreign key
            $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');
            $table->index(['conversation_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['conversation_id']);
            $table->dropColumn(['conversation_id', 'message_type', 'file_path', 'file_name', 'file_size']);
            
            // Restore old fields
            $table->unsignedBigInteger('receiver_id')->nullable();
            $table->string('subject');
            $table->enum('type', ['personal', 'broadcast', 'system', 'urgent'])->default('personal');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
        });
    }
};
