<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('oral_health_examinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->integer('index_dft')->nullable();
            $table->integer('number_of_teeth_decayed')->nullable();
            $table->integer('number_of_teeth_filled')->nullable();
            $table->integer('total_dft')->nullable();
            $table->integer('for_extraction')->nullable();
            $table->integer('for_filling')->nullable();
            $table->date('examination_date')->default(now());
            $table->timestamps();
        });

        DB::table('oral_health_examinations')->truncate();
    }

    public function down()
    {
        Schema::dropIfExists('oral_health_examinations');
    }
};
