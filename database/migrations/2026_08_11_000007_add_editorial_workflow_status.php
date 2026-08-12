<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** @var array<string, string> */
    private array $tables = [
        'blog_posts' => 'publicado',
        'comunicados' => 'publicado',
        'articulos' => 'publicado',
        'revistas' => 'publicado',
        'revista_numeros' => 'publicado',
        'cursos' => 'publicado',
        'docentes' => 'activo',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table => $publicationColumn) {
            Schema::table($table, function (Blueprint $blueprint): void {
                $blueprint->string('estado_editorial', 24)->default('draft')->index();
            });

            DB::table($table)->where($publicationColumn, true)->update(['estado_editorial' => 'published']);
        }

        DB::table('docentes')
            ->where('activo', false)
            ->where('estado_revision', 'verified')
            ->update(['estado_editorial' => 'verified']);
        DB::table('docentes')
            ->where('activo', false)
            ->where('estado_revision', 'pending')
            ->whereNotNull('documento_fuente')
            ->update(['estado_editorial' => 'pending']);
    }

    public function down(): void
    {
        foreach (array_keys($this->tables) as $table) {
            Schema::table($table, fn (Blueprint $blueprint) => $blueprint->dropColumn('estado_editorial'));
        }
    }
};
