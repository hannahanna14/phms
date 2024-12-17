<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('health_examinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->date('examination_date');
            
            // Vital Signs
            $table->decimal('temperature', 4, 2)->nullable();
            $table->string('heart_rate')->nullable();
            $table->string('height')->nullable();
            $table->string('weight')->nullable();
            
            // Nutritional Status
            $table->string('nutritional_status_bmi')->nullable();
            $table->string('nutritional_status_height')->nullable();
            
            // Screenings
            $table->string('vision_screening')->nullable();
            $table->string('auditory_screening')->nullable();
            
            // Physical Assessment
            $table->string('skin')->nullable();
            $table->string('scalp')->nullable();
            $table->string('eye')->nullable();
            $table->string('ear')->nullable();
            $table->string('nose')->nullable();
            $table->string('mouth')->nullable();
            $table->string('neck')->nullable();
            $table->string('throat')->nullable();
            $table->string('lungs_heart')->nullable();
            $table->string('abdomen')->nullable();
            $table->string('deformities')->nullable();
            $table->text('remarks')->nullable();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('health_examinations');
    }
};
