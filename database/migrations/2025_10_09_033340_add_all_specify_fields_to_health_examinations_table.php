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
        Schema::table('health_examinations', function (Blueprint $table) {
            // Add all the missing specify fields
            $table->string('vision_screening_specify')->nullable()->after('vision_screening');
            $table->string('auditory_screening_specify')->nullable()->after('auditory_screening');
            $table->string('skin_specify')->nullable()->after('skin');
            $table->string('scalp_specify')->nullable()->after('scalp');
            $table->string('eye_specify')->nullable()->after('eye');
            $table->string('ear_specify')->nullable()->after('ear');
            $table->string('nose_specify')->nullable()->after('nose');
            $table->string('mouth_specify')->nullable()->after('mouth');
            $table->string('lungs_specify')->nullable()->after('lungs');
            $table->string('heart_specify')->nullable()->after('heart');
            $table->string('abdomen_specify')->nullable()->after('abdomen');
            $table->string('deformities_specify')->nullable()->after('deformities');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('health_examinations', function (Blueprint $table) {
            $table->dropColumn([
                'vision_screening_specify',
                'auditory_screening_specify',
                'skin_specify',
                'scalp_specify',
                'eye_specify',
                'ear_specify',
                'nose_specify',
                'mouth_specify',
                'lungs_specify',
                'heart_specify',
                'abdomen_specify',
                'deformities_specify'
            ]);
        });
    }
};
