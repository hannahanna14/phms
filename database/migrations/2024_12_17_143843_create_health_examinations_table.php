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
            $table->string('grade_level')->nullable();
            $table->string('school_year')->nullable();
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
            $table->string('vision_screening_specify')->nullable();
            $table->string('auditory_screening')->nullable();
            $table->string('auditory_screening_specify')->nullable();
            
            // Physical Assessment
            $table->string('skin')->nullable();
            $table->string('skin_specify')->nullable();
            $table->string('scalp')->nullable();
            $table->string('scalp_specify')->nullable();
            $table->string('eye')->nullable();
            $table->string('eye_specify')->nullable();
            $table->string('ear')->nullable();
            $table->string('ear_specify')->nullable();
            $table->string('nose')->nullable();
            $table->string('nose_specify')->nullable();
            $table->string('mouth')->nullable();
            $table->string('mouth_specify')->nullable();
            $table->string('neck')->nullable();
            $table->string('neck_specify')->nullable();
            $table->string('throat')->nullable();
            $table->string('throat_specify')->nullable();
            $table->string('lungs_heart')->nullable(); // Legacy field
            $table->string('lungs')->nullable();
            $table->string('lungs_specify')->nullable();
            $table->string('lungs_other_specify')->nullable();
            $table->string('heart')->nullable();
            $table->string('heart_specify')->nullable();
            $table->string('heart_other_specify')->nullable();
            $table->string('abdomen')->nullable();
            $table->string('abdomen_specify')->nullable();
            $table->string('deformities')->nullable();
            $table->string('deformities_specify')->nullable();
            
            // Additional Health Information
            $table->string('deworming_status')->nullable();
            $table->string('iron_supplementation')->nullable();
            $table->boolean('sbfp_beneficiary')->default(false);
            $table->boolean('four_ps_beneficiary')->default(false);
            $table->string('immunization')->nullable();
            $table->text('other_specify')->nullable();
            $table->text('remarks')->nullable();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('health_examinations');
    }
};
