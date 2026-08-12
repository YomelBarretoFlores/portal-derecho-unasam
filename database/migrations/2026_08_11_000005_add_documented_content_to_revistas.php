<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('revistas', function (Blueprint $table) {
            $table->longText('enfoque_alcance')->nullable()->after('presentacion');
            $table->string('modalidad')->nullable()->after('periodicidad');
            $table->json('idiomas')->nullable()->after('modalidad');
            $table->json('tipos_contribucion')->nullable()->after('idiomas');
            $table->string('sistema_arbitraje')->nullable()->after('tipos_contribucion');
            $table->string('norma_citacion')->nullable()->after('sistema_arbitraje');
            $table->longText('normas_publicacion')->nullable()->after('norma_citacion');
        });

        Schema::create('revista_miembros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('revista_id')->constrained('revistas')->cascadeOnDelete();
            $table->string('grupo')->index();
            $table->string('grado')->nullable();
            $table->string('nombre');
            $table->string('afiliacion')->nullable();
            $table->string('pais')->nullable();
            $table->string('orcid')->nullable();
            $table->string('email')->nullable();
            $table->unsignedInteger('orden')->default(0);
            $table->boolean('activo')->default(true)->index();
            $table->timestamps();

            $table->index(['revista_id', 'activo', 'grupo', 'orden']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('revista_miembros');

        Schema::table('revistas', function (Blueprint $table) {
            $table->dropColumn([
                'enfoque_alcance',
                'modalidad',
                'idiomas',
                'tipos_contribucion',
                'sistema_arbitraje',
                'norma_citacion',
                'normas_publicacion',
            ]);
        });
    }
};
