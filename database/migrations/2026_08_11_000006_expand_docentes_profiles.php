<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('docentes', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
            $table->string('categoria')->nullable()->after('grado');
            $table->string('dedicacion')->nullable()->after('categoria');
            $table->text('resena')->nullable()->after('dedicacion');
            $table->string('email_institucional')->nullable()->after('resena');
            $table->string('orcid')->nullable()->after('email_institucional');
            $table->string('google_scholar_url')->nullable()->after('orcid');
            $table->string('cti_vitae_url')->nullable()->after('google_scholar_url');
            $table->string('perfil_academico_url')->nullable()->after('cti_vitae_url');
            $table->json('publicaciones')->nullable()->after('perfil_academico_url');
            $table->string('estado_revision')->default('pending')->after('publicaciones')->index();
            $table->string('documento_fuente')->nullable()->after('estado_revision');
            $table->text('observaciones_revision')->nullable()->after('documento_fuente');
        });

        $usedSlugs = [];
        foreach (DB::table('docentes')->orderBy('id')->get(['id', 'name', 'activo']) as $docente) {
            $base = Str::slug($docente->name) ?: 'docente-'.$docente->id;
            $slug = $base;
            $suffix = 2;

            while (isset($usedSlugs[$slug])) {
                $slug = $base.'-'.$suffix++;
            }

            $usedSlugs[$slug] = true;

            DB::table('docentes')->where('id', $docente->id)->update([
                'slug' => $slug,
                // Conserva visibles los registros que ya estaban publicados antes
                // de incorporar el flujo de revisión documental.
                'estado_revision' => $docente->activo ? 'verified' : 'pending',
            ]);
        }

        Schema::table('docentes', function (Blueprint $table) {
            $table->unique('slug', 'docentes_slug_unique');
            $table->index(
                ['activo', 'estado_revision', 'orden'],
                'docentes_public_listing_index',
            );
        });
    }

    public function down(): void
    {
        Schema::table('docentes', function (Blueprint $table) {
            $table->dropIndex('docentes_public_listing_index');
            $table->dropUnique('docentes_slug_unique');
            $table->dropColumn([
                'slug',
                'categoria',
                'dedicacion',
                'resena',
                'email_institucional',
                'orcid',
                'google_scholar_url',
                'cti_vitae_url',
                'perfil_academico_url',
                'publicaciones',
                'estado_revision',
                'documento_fuente',
                'observaciones_revision',
            ]);
        });
    }
};
