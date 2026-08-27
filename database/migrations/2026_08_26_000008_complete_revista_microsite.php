<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('revistas', function (Blueprint $table) {
            $table->longText('contenido_politicas')->nullable();
            $table->longText('contenido_sobre')->nullable();
            $table->longText('contenido_indexacion')->nullable();
            $table->longText('contenido_privacidad')->nullable();
            $table->longText('contenido_preservacion')->nullable();
            $table->longText('introduccion_envios')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('whatsapp_url')->nullable();
        });

        Schema::table('revista_numeros', function (Blueprint $table) {
            $table->boolean('es_actual')->default(false)->index();
        });

        Schema::create('revista_documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('revista_id')->constrained('revistas')->cascadeOnDelete();
            $table->string('categoria')->index();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('url')->nullable();
            $table->unsignedInteger('orden')->default(0);
            $table->boolean('visible')->default(true)->index();
            $table->string('estado_editorial')->default('draft')->index();
            $table->timestamps();
            $table->index(['revista_id', 'categoria', 'visible', 'orden'], 'revista_documentos_publicos_idx');
        });

        Schema::create('revista_avisos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('revista_id')->constrained('revistas')->cascadeOnDelete();
            $table->string('titulo');
            $table->string('slug')->unique();
            $table->text('resumen')->nullable();
            $table->longText('contenido')->nullable();
            $table->timestamp('fecha_publicacion')->nullable()->index();
            $table->timestamp('fecha_caducidad')->nullable()->index();
            $table->string('enlace')->nullable();
            $table->string('estado_editorial')->default('draft')->index();
            $table->timestamps();
        });

        Schema::create('revista_contactos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('revista_id')->constrained('revistas')->cascadeOnDelete();
            $table->string('nombre');
            $table->string('cargo')->nullable();
            $table->string('email')->nullable();
            $table->string('telefono')->nullable();
            $table->string('tipo')->default('persona')->index();
            $table->unsignedInteger('orden')->default(0);
            $table->boolean('visible')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('revista_lineas_investigacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('revista_id')->constrained('revistas')->cascadeOnDelete();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->unsignedInteger('orden')->default(0);
            $table->boolean('activa')->default(true)->index();
            $table->timestamps();
            $table->unique(['revista_id', 'nombre']);
        });

        Schema::create('revista_envios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('revista_id')->constrained('revistas')->cascadeOnDelete();
            $table->foreignId('revista_linea_investigacion_id')->nullable()->constrained('revista_lineas_investigacion')->nullOnDelete();
            $table->string('codigo_seguimiento', 32)->unique();
            $table->string('nombres');
            $table->text('documento_identidad');
            $table->string('afiliacion');
            $table->string('ciudad');
            $table->string('pais');
            $table->string('email_institucional');
            $table->text('whatsapp');
            $table->string('orcid')->nullable();
            $table->string('tipo_contribucion');
            $table->string('titulo');
            $table->text('resumen');
            $table->json('coautores')->nullable();
            $table->string('manuscrito_path');
            $table->string('carta_path');
            $table->string('declaracion_path');
            $table->string('constancia_estilo_path');
            $table->string('estado')->default('recibido')->index();
            $table->text('observaciones_internas')->nullable();
            $table->timestamp('consentimiento_at');
            $table->string('ip_hash', 64)->nullable();
            $table->timestamps();
            $table->index(['email_institucional', 'codigo_seguimiento']);
        });

        Schema::create('revista_envio_versiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('revista_envio_id')->constrained('revista_envios')->cascadeOnDelete();
            $table->unsignedInteger('numero');
            $table->string('archivo_path');
            $table->text('nota_autor')->nullable();
            $table->timestamp('recibida_at');
            $table->timestamps();
            $table->unique(['revista_envio_id', 'numero']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('revista_envio_versiones');
        Schema::dropIfExists('revista_envios');
        Schema::dropIfExists('revista_lineas_investigacion');
        Schema::dropIfExists('revista_contactos');
        Schema::dropIfExists('revista_avisos');
        Schema::dropIfExists('revista_documentos');

        Schema::table('revista_numeros', fn (Blueprint $table) => $table->dropColumn('es_actual'));
        Schema::table('revistas', fn (Blueprint $table) => $table->dropColumn([
            'contenido_politicas', 'contenido_sobre', 'contenido_indexacion', 'contenido_privacidad',
            'contenido_preservacion', 'introduccion_envios', 'facebook_url', 'whatsapp_url',
        ]));
    }
};
