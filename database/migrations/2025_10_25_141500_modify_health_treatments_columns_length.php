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
        // First, truncate existing data to 100 characters
        DB::statement("UPDATE health_treatments SET title = LEFT(title, 100) WHERE LENGTH(title) > 100");
        DB::statement("UPDATE health_treatments SET chief_complaint = LEFT(chief_complaint, 100) WHERE LENGTH(chief_complaint) > 100");
        DB::statement("UPDATE health_treatments SET treatment = LEFT(treatment, 100) WHERE LENGTH(treatment) > 100");
        DB::statement("UPDATE health_treatments SET remarks = LEFT(remarks, 100) WHERE remarks IS NOT NULL AND LENGTH(remarks) > 100");
        
        // Then change the column types
        Schema::table('health_treatments', function (Blueprint $table) {
            $table->string('title', 100)->change();
            $table->string('chief_complaint', 100)->change();
            $table->string('treatment', 100)->change();
            $table->string('remarks', 100)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('health_treatments', function (Blueprint $table) {
            $table->string('title', 255)->change();
            $table->text('chief_complaint')->change();
            $table->text('treatment')->change();
            $table->text('remarks')->nullable()->change();
        });
    }
};
