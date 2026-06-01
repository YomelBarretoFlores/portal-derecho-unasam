<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blog_posts', fn (Blueprint $t) => $t->index('publicado'));
        Schema::table('articulos', fn (Blueprint $t) => $t->index('publicado'));
        Schema::table('comunicados', fn (Blueprint $t) => $t->index('publicado'));
        Schema::table('docentes', fn (Blueprint $t) => $t->index('activo'));
        Schema::table('estadisticas', fn (Blueprint $t) => $t->index('tipo'));
        Schema::table('competencias', fn (Blueprint $t) => $t->index('grupo'));
    }

    public function down(): void
    {
        Schema::table('blog_posts', fn (Blueprint $t) => $t->dropIndex(['publicado']));
        Schema::table('articulos', fn (Blueprint $t) => $t->dropIndex(['publicado']));
        Schema::table('comunicados', fn (Blueprint $t) => $t->dropIndex(['publicado']));
        Schema::table('docentes', fn (Blueprint $t) => $t->dropIndex(['activo']));
        Schema::table('estadisticas', fn (Blueprint $t) => $t->dropIndex(['tipo']));
        Schema::table('competencias', fn (Blueprint $t) => $t->dropIndex(['grupo']));
    }
};
