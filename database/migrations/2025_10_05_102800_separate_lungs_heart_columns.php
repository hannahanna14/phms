<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('health_examinations', function (Blueprint $table) {
            // Add separate lungs and heart columns
            $table->string('lungs')->nullable()->after('throat');
            $table->string('heart')->nullable()->after('lungs');
        });

        // Copy existing lungs_heart data to both new columns
        DB::statement("UPDATE health_examinations SET lungs = lungs_heart, heart = lungs_heart WHERE lungs_heart IS NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('health_examinations', function (Blueprint $table) {
            $table->dropColumn(['lungs', 'heart']);
        });
    }
};
