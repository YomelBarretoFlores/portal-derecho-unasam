<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('revistas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('nombre_corto')->nullable();
            $table->longText('presentacion')->nullable();
            $table->string('unidad_responsable')->nullable();
            $table->string('resolucion_numero')->nullable();
            $table->date('resolucion_fecha')->nullable();
            $table->text('resolucion_resumen')->nullable();
            $table->string('periodicidad')->nullable();
            $table->string('issn')->nullable();
            $table->string('contacto_email')->nullable();
            $table->boolean('activo')->default(false)->index();
            $table->boolean('publicado')->default(false)->index();
            $table->timestamps();
        });

        Schema::create('revista_numeros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('revista_id')->constrained('revistas')->cascadeOnDelete();
            $table->string('volumen');
            $table->string('numero');
            $table->string('slug')->unique();
            $table->string('titulo');
            $table->string('subtitulo')->nullable();
            $table->text('descripcion')->nullable();
            $table->date('fecha_publicacion')->nullable()->index();
            $table->unsignedInteger('orden')->default(0);
            $table->boolean('publicado')->default(false)->index();
            $table->timestamps();
            $table->unique(['revista_id', 'volumen', 'numero']);
        });

        Schema::table('articulos', function (Blueprint $table) {
            $table->foreignId('revista_numero_id')->nullable()->after('id')->constrained('revista_numeros')->nullOnDelete();
            $table->longText('contenido')->nullable()->after('resumen');
            $table->unsignedInteger('orden')->default(0)->after('descargas');
            $table->index(['revista_numero_id', 'publicado', 'fecha']);
        });

        Schema::table('cursos', function (Blueprint $table) {
            $table->string('plan')->default('2023')->after('id')->index();
            $table->boolean('publicado')->default(false)->after('orden')->index();
            $table->unique(['plan', 'ciclo', 'nombre']);
        });
    }

    public function down(): void
    {
        Schema::table('cursos', function (Blueprint $table) {
            $table->dropUnique(['plan', 'ciclo', 'nombre']);
            $table->dropColumn(['plan', 'publicado']);
        });

        Schema::table('articulos', function (Blueprint $table) {
            $table->dropIndex(['revista_numero_id', 'publicado', 'fecha']);
            $table->dropConstrainedForeignId('revista_numero_id');
            $table->dropColumn(['contenido', 'orden']);
        });

        Schema::dropIfExists('revista_numeros');
        Schema::dropIfExists('revistas');
    }
};
