<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('blog_posts')->whereIn('slug', [
            'estudiantes-de-derecho-unasam-destacan-en-concurso-nacional-de-litigacion-oral',
            'el-rol-del-abogado-en-la-era-de-la-inteligencia-artificial',
            'seminario-internacional-de-derecho-constitucional-2026',
            'facultad-de-derecho-firma-convenio-con-el-poder-judicial-de-ancash',
            'justicia-ambiental-en-ancash-deudas-pendientes',
            'conferencia-reforma-procesal-penal-avances-y-retrocesos',
        ])->update(['publicado' => false]);

        DB::table('articulos')->where('doi', 'like', '10.37249/rjunasam.v1i1.%')->update(['publicado' => false]);
        DB::table('comunicados')->whereIn('slug', [
            'cronograma-de-matricula-2026-i',
            'convocatoria-a-practicas-preprofesionales',
            'suspension-de-actividades-academicas',
            'proceso-de-sustentacion-de-tesis',
        ])->update(['publicado' => false]);

        DB::table('docentes')->whereIn('name', [
            'Dr. Carlos Javier Rojas Meza', 'Dra. María Elena Quispe Huamán',
            'Mg. Jorge Alberto Sánchez Díaz', 'Dra. Ana Beatriz Villanueva Torres',
            'Mg. Roberto Mendoza Calderón', 'Dr. Eduardo Vargas Linares',
            'Mg. Patricia Mendoza Ríos', 'Dr. Diego Alejandro Huerta Castillo',
        ])->update(['activo' => false]);

        DB::table('cursos')->where('plan', '2023')->update(['publicado' => false]);

        DB::table('settings')
            ->where('clave', 'home_revista_eyebrow')
            ->where('valor', 'Revista Jurídica UNASAM')
            ->update(['valor' => 'Revista Derecho y Cultura']);
        DB::table('settings')
            ->where('clave', 'home_revista_badge')
            ->where('valor', 'Vol. 1 · Núm. 1 — Marzo 2026')
            ->delete();
    }

    public function down(): void
    {
        // La publicación de contenido requiere revisión editorial; no se reactiva automáticamente.
    }
};
