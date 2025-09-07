<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('health_examinations', function (Blueprint $table) {
            $table->string('deworming_status')->nullable();
            $table->string('iron_supplementation')->nullable();
            $table->boolean('sbfp_beneficiary')->default(false);
            $table->boolean('four_ps_beneficiary')->default(false);
            $table->string('immunization')->nullable();
            $table->text('other_specify')->nullable();
        });
    }

    public function down()
    {
        Schema::table('health_examinations', function (Blueprint $table) {
            $table->dropColumn(['deworming_status', 'iron_supplementation']);
        });
    }
};