<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * URL pública de respaldo para los archivos que aún no la tenían.
 *
 * La migración 000013 resolvió números, artículos y avisos. Estos cinco se
 * quedaron fuera, y con las cargas deshabilitadas eso significa que no hay
 * NINGUNA manera de cambiarlos desde el panel: ni subiendo el archivo, que
 * está en gris, ni pegando una dirección, que no existía.
 *
 *   revistas.logo_url_respaldo         el logo salía de un archivo fijo
 *   revistas.resolucion_url_respaldo   ídem
 *   comunicados.imagen_url_respaldo    imposible ilustrar un comunicado
 *   blog_posts.imagen_url_respaldo     ídem en el blog
 *   organigrama.imagen_url_respaldo    la página quedaba sin imagen
 *   docentes.foto_url_respaldo         los retratos vivían en config/docentes.php
 *
 * Las columnas son anulables y nacen vacías: nada cambia de aspecto hasta que
 * alguien rellene una desde el panel.
 */
return new class extends Migration
{
    /** @var array<string, array<int, string>> */
    private const COLUMNAS = [
        'revistas' => ['logo_url_respaldo', 'resolucion_url_respaldo'],
        'comunicados' => ['imagen_url_respaldo'],
        'blog_posts' => ['imagen_url_respaldo'],
        'organigrama' => ['imagen_url_respaldo'],
        'docentes' => ['foto_url_respaldo'],
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
