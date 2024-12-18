<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->unique()->after('id');
            }
            if (!Schema::hasColumn('users', 'full_name')) {
                $table->string('full_name')->after('username');
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['admin', 'nurse', 'teacher'])->after('password');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'full_name', 'role']);
        });
    }
};
