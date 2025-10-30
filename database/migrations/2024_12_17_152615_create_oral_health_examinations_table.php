<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('oral_health_examinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('grade_level')->nullable();
            $table->string('school_year')->nullable();
            $table->date('examination_date');
            
            // Permanent Teeth
            $table->integer('permanent_index_dft')->nullable();
            $table->integer('permanent_teeth_decayed')->nullable();
            $table->integer('permanent_teeth_filled')->nullable();
            $table->integer('permanent_total_dft')->nullable();
            $table->integer('permanent_for_extraction')->nullable();
            $table->integer('permanent_for_filling')->nullable();
            $table->integer('permanent_teeth_missing')->nullable();
            
            // Temporary Teeth
            $table->integer('temporary_index_dft')->nullable();
            $table->integer('temporary_teeth_decayed')->nullable();
            $table->integer('temporary_teeth_filled')->nullable();
            $table->integer('temporary_total_dft')->nullable();
            $table->integer('temporary_for_extraction')->nullable();
            $table->integer('temporary_for_filling')->nullable();
            $table->integer('temporary_teeth_missing')->nullable();
            
            // Additional Information
            $table->json('tooth_symbols')->nullable();
            $table->json('conditions')->nullable();
            $table->text('remarks')->nullable();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('oral_health_examinations');
    }
};
