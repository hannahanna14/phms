<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('health_examinations', function (Blueprint $table) {
            $table->enum('deworming_status', ['dewormed', 'not_dewormed'])->nullable();
            $table->enum('iron_supplementation', ['positive', 'negative'])->nullable();
        });
    }

    public function down()
    {
        Schema::table('health_examinations', function (Blueprint $table) {
            $table->dropColumn(['deworming_status', 'iron_supplementation']);
        });
    }
};