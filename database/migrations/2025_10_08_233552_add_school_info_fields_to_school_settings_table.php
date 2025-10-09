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
        Schema::table('school_settings', function (Blueprint $table) {
            $table->string('school_id')->nullable()->after('school_name');
            $table->string('region')->nullable()->after('school_id');
            $table->string('division')->nullable()->after('region');
            $table->string('telephone_no')->nullable()->after('division');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->dropColumn(['school_id', 'region', 'division', 'telephone_no']);
        });
    }
};
