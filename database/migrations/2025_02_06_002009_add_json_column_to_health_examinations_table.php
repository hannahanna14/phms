<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('health_examinations', function (Blueprint $table) {
            $table->json('examination_data')->nullable(); // Add a JSON column for examination data
        });
    }

    public function down()
    {
        Schema::table('health_examinations', function (Blueprint $table) {
            $table->dropColumn('examination_data'); // Remove the JSON column if needed
        });
    }
};
