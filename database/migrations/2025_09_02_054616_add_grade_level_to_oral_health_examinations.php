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
        Schema::table('oral_health_examinations', function (Blueprint $table) {
            if (!Schema::hasColumn('oral_health_examinations', 'grade_level')) {
                $table->integer('grade_level')->after('student_id');
            }
            if (!Schema::hasColumn('oral_health_examinations', 'school_year')) {
                $table->string('school_year')->after('grade_level');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('oral_health_examinations', function (Blueprint $table) {
            $table->dropColumn(['grade_level', 'school_year']);
        });
    }
};
