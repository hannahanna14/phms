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
            // Add missing teeth count fields
            $table->integer('permanent_teeth_missing')->nullable()->after('permanent_teeth_filled');
            $table->integer('temporary_teeth_missing')->nullable()->after('temporary_teeth_filled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('oral_health_examinations', function (Blueprint $table) {
            $table->dropColumn(['permanent_teeth_missing', 'temporary_teeth_missing']);
        });
    }
};
