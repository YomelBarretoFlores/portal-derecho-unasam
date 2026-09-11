<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * URL pública de respaldo para los archivos de la revista.
 *
 * Sin almacenamiento persistente (MEDIA_UPLOADS_ENABLED=false) los campos de
 * Media Library quedan deshabilitados y hoy resulta imposible publicar un número
 * con portada o PDF. RevistaDocumento ya resolvía esto con una columna `url`;
 * estas columnas extienden el mismo patrón a números, artículos y avisos.
 */
return new class extends Migration
{
    /** @var array<string, array<int, string>> */
    private const COLUMNAS = [
        'revista_numeros' => ['portada_url_respaldo', 'pdf_url_respaldo'],
        'articulos' => ['pdf_url_respaldo'],
        'revista_avisos' => ['adjunto_url_respaldo'],
    ];

    public function up(): void
    {
        foreach (self::COLUMNAS as $tabla => $columnas) {
            if (! Schema::hasTable($tabla)) {
                continue;
            }

            $faltantes = array_values(array_filter(
                $columnas,
                fn (string $columna): bool => ! Schema::hasColumn($tabla, $columna),
            ));

            if ($faltantes === []) {
                continue;
            }

            Schema::table($tabla, function (Blueprint $table) use ($faltantes): void {
                foreach ($faltantes as $columna) {
                    $table->string($columna, 2048)->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        foreach (self::COLUMNAS as $tabla => $columnas) {
            if (! Schema::hasTable($tabla)) {
                continue;
            }

            $existentes = array_values(array_filter(
                $columnas,
                fn (string $columna): bool => Schema::hasColumn($tabla, $columna),
            ));

            if ($existentes === []) {
                continue;
            }

            Schema::table($tabla, function (Blueprint $table) use ($existentes): void {
                $table->dropColumn($existentes);
            });
        }
    }
};
