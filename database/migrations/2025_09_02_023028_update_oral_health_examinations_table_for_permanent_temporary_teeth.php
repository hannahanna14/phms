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
            // Drop old columns
            $table->dropColumn([
                'index_dft',
                'number_of_teeth_decayed',
                'number_of_teeth_filled',
                'total_dft',
                'for_extraction',
                'for_filling'
            ]);
            
            // Add new permanent teeth columns
            $table->integer('permanent_index_dft')->nullable();
            $table->integer('permanent_teeth_decayed')->nullable();
            $table->integer('permanent_teeth_filled')->nullable();
            $table->integer('permanent_total_dft')->nullable();
            $table->integer('permanent_for_extraction')->nullable();
            $table->integer('permanent_for_filling')->nullable();
            
            // Add new temporary teeth columns
            $table->integer('temporary_index_dft')->nullable();
            $table->integer('temporary_teeth_decayed')->nullable();
            $table->integer('temporary_teeth_filled')->nullable();
            $table->integer('temporary_total_dft')->nullable();
            $table->integer('temporary_for_extraction')->nullable();
            $table->integer('temporary_for_filling')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('oral_health_examinations', function (Blueprint $table) {
            // Drop new columns
            $table->dropColumn([
                'permanent_index_dft',
                'permanent_teeth_decayed',
                'permanent_teeth_filled',
                'permanent_total_dft',
                'permanent_for_extraction',
                'permanent_for_filling',
                'temporary_index_dft',
                'temporary_teeth_decayed',
                'temporary_teeth_filled',
                'temporary_total_dft',
                'temporary_for_extraction',
                'temporary_for_filling'
            ]);
            
            // Restore old columns
            $table->integer('index_dft')->nullable();
            $table->integer('number_of_teeth_decayed')->nullable();
            $table->integer('number_of_teeth_filled')->nullable();
            $table->integer('total_dft')->nullable();
            $table->integer('for_extraction')->nullable();
            $table->integer('for_filling')->nullable();
        });
    }
};
