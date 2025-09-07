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
            $table->string('grade_level')->nullable()->after('student_id');
            $table->string('school_year')->nullable()->after('grade_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('health_examinations', function (Blueprint $table) {
            $table->dropColumn(['grade_level', 'school_year']);
        });
    }
};
