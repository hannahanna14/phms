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
            // Add grade_level if it doesn't exist
            if (!Schema::hasColumn('oral_health_examinations', 'grade_level')) {
                $table->string('grade_level')->after('student_id');
            }
            
            // Add school_year if it doesn't exist
            if (!Schema::hasColumn('oral_health_examinations', 'school_year')) {
                $table->string('school_year')->after('grade_level');
            }
            
            // Add examination_date if it doesn't exist
            if (!Schema::hasColumn('oral_health_examinations', 'examination_date')) {
                $table->date('examination_date')->nullable()->after('school_year');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('oral_health_examinations', function (Blueprint $table) {
            // Remove the columns if they exist
            if (Schema::hasColumn('oral_health_examinations', 'grade_level')) {
                $table->dropColumn('grade_level');
            }
            if (Schema::hasColumn('oral_health_examinations', 'school_year')) {
                $table->dropColumn('school_year');
            }
            if (Schema::hasColumn('oral_health_examinations', 'examination_date')) {
                $table->dropColumn('examination_date');
            }
        });
    }
};
