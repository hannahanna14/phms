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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sender_id');
            $table->unsignedBigInteger('receiver_id')->nullable(); // null for broadcast messages
            $table->string('subject');
            $table->text('content');
            $table->enum('type', ['personal', 'broadcast', 'system', 'urgent'])->default('personal');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->json('attachments')->nullable(); // Store file paths/info
            $table->unsignedBigInteger('related_student_id')->nullable(); // Link to specific student
            $table->string('related_module')->nullable(); // health_exam, treatment, etc.
            $table->unsignedBigInteger('related_record_id')->nullable(); // ID of related record
            $table->timestamps();

            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('related_student_id')->references('id')->on('students')->onDelete('set null');
            
            $table->index(['receiver_id', 'is_read']);
            $table->index(['sender_id', 'created_at']);
            $table->index('type');
            $table->index('priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
