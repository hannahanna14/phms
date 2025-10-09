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
        Schema::table('students', function (Blueprint $table) {
            $table->date('date_of_birth')->nullable()->after('age');
            $table->string('birthplace')->nullable()->after('date_of_birth');
            $table->string('parent_guardian')->nullable()->after('birthplace');
            $table->text('address')->nullable()->after('parent_guardian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['date_of_birth', 'birthplace', 'parent_guardian', 'address']);
        });
    }
};
