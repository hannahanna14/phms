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
            $table->json('tooth_symbols')->nullable()->after('temporary_for_filling');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('oral_health_examinations', function (Blueprint $table) {
            $table->dropColumn('tooth_symbols');
        });
    }
};
