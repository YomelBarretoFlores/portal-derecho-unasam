<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->nullable()->after('password')->index();
            $table->text('app_authentication_secret')->nullable()->after('remember_token');
            $table->text('app_authentication_recovery_codes')->nullable()->after('app_authentication_secret');
        });

        DB::table('users')
            ->where('is_admin', true)
            ->update(['role' => 'super_admin']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropColumn(['role', 'app_authentication_secret', 'app_authentication_recovery_codes']);
        });
    }
};
