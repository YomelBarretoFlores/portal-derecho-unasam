<?php

use Database\Seeders\RevistaContentSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::table('revistas')->exists()) {
            app(RevistaContentSeeder::class)->run();
        }
    }

    public function down(): void
    {
        // La reparación es no destructiva para no retirar contenido ya publicado.
    }
};
