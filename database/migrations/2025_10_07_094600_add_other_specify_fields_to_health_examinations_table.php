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
            $table->string('lungs_other_specify')->nullable()->after('lungs');
            $table->string('heart_other_specify')->nullable()->after('heart');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('health_examinations', function (Blueprint $table) {
            $table->dropColumn(['lungs_other_specify', 'heart_other_specify']);
        });
    }
};
