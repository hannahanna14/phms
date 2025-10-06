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
        Schema::table('oral_health_treatments', function (Blueprint $table) {
            $table->enum('timer_status', ['not_started', 'active', 'paused', 'completed', 'expired'])
                  ->default('not_started')
                  ->after('is_expired');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('oral_health_treatments', function (Blueprint $table) {
            $table->dropColumn('timer_status');
        });
    }
};
