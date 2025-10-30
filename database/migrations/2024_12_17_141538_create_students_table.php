<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->integer('age');
            $table->enum('sex', ['Male', 'Female']);
            $table->date('date_of_birth')->nullable();
            $table->string('birthplace')->nullable();
            $table->string('parent_guardian')->nullable();
            $table->text('address')->nullable();
            $table->string('lrn')->nullable()->unique(); // Learner Reference Number
            $table->string('grade_level')->nullable();
            $table->string('section')->nullable();
            $table->string('school_year')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('students');
    }
};
