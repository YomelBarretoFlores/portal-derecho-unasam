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
            $table->boolean('is_admin')->default(false)->after('password');
        });

        // Puente del modelo anterior (lista blanca por entorno) al nuevo: los
        // correos ya autorizados quedan como administradores, para no perder acceso.
        $emails = config('admin.emails', []);

        if (! empty($emails)) {
            DB::table('users')->whereIn('email', $emails)->update(['is_admin' => true]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};
