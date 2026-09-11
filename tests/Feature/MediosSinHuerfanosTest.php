<?php

namespace Tests\Feature;

use App\Models\Articulo;
use App\Models\BlogPost;
use App\Models\Comunicado;
use App\Models\Docente;
use App\Models\Organigrama;
use App\Models\Revista;
use App\Models\RevistaAviso;
use App\Models\RevistaDocumento;
use App\Models\RevistaNumero;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Spatie\MediaLibrary\HasMedia;
use Tests\TestCase;

/**
 * Ninguna colección de archivos puede quedarse sin salida al sitio.
 *
 * El logo de la revista estuvo así quién sabe cuánto tiempo: el modelo
 * declaraba la colección, el panel ofrecía el campo, el formulario aceptaba el
 * archivo, y la portada de la revista pintaba una imagen fija del repositorio
 * sin mirarlo. Nada fallaba. Simplemente no servía para nada, y la única
 * manera de enterarse era subir un logo y comprobar que no cambiaba nada.
 *
 * Este test recorre el camino entero —colección, resolutor, vista— y falla si
 * se rompe en cualquier punto.
 */
class MediosSinHuerfanosTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Colección de archivos => cómo la resuelve el modelo.
     *
     * Los nombres no siguen una convención («resolucion» se resuelve con
     * resolution_url, «numero_pdf» con pdf_url), así que la correspondencia se
     * declara. Lo que el test comprueba es que esta tabla siga cuadrando con
     * la realidad por los dos lados.
     *
     * @var array<class-string<HasMedia>, array<string, string>>
     */
    private const RESOLUTORES = [
        Revista::class => ['logo' => 'logo_url', 'resolucion' => 'resolution_url'],
        RevistaNumero::class => ['portada' => 'portada_url', 'numero_pdf' => 'pdf_url'],
        Articulo::class => ['pdf' => 'pdf_url'],
        RevistaAviso::class => ['adjunto' => 'adjunto_url'],
        RevistaDocumento::class => ['archivo' => 'download_url'],
        Comunicado::class => ['imagen' => 'imagenUrl'],
        BlogPost::class => ['imagen' => 'imagenUrl'],
        Organigrama::class => ['imagen' => 'imagen_url'],
        Docente::class => ['foto' => 'fotoPublicaUrl'],
    ];

    public function test_every_declared_collection_has_a_resolver(): void
    {
        foreach (self::RESOLUTORES as $modelo => $resolutores) {
            $instancia = new $modelo;
            $instancia->registerMediaCollections();

            $declaradas = array_map(
                fn ($coleccion): string => $coleccion->name,
                $instancia->getRegisteredMediaCollections()->all(),
            );

            sort($declaradas);
            $cubiertas = array_keys($resolutores);
            sort($cubiertas);

            $this->assertSame(
                $declaradas,
                $cubiertas,
                class_basename($modelo).': las colecciones declaradas y las resueltas no coinciden. '
                .'Una colección sin resolutor es un campo del panel que no cambia nada en el sitio.',
            );
        }
    }

    public function test_every_resolver_is_actually_used_somewhere(): void
    {
        $fuentes = collect([
            ...File::allFiles(resource_path('views')),
            ...File::allFiles(app_path('Services')),
            ...File::allFiles(app_path('Http/Controllers')),
        ])->map(fn ($fichero): string => $fichero->getContents())->implode("\n");

        foreach (self::RESOLUTORES as $modelo => $resolutores) {
            foreach ($resolutores as $coleccion => $resolutor) {
                $this->assertStringContainsString(
                    $resolutor,
                    $fuentes,
                    class_basename($modelo)."/{$coleccion}: «{$resolutor}» no aparece en ninguna vista, "
                    .'servicio ni controlador. El archivo se puede administrar pero no se ve.',
                );
            }
        }
    }

    public function test_every_collection_can_be_administered(): void
    {
        // De poco sirve que el sitio sepa pintar un archivo si no hay forma de
        // cambiarlo. Con las cargas deshabilitadas, el campo de subida sale en
        // gris: sin una URL de respaldo, la colección es inadministrable.
        $sinRespaldo = [];

        foreach (self::RESOLUTORES as $modelo => $resolutores) {
            $instancia = new $modelo;
            $rellenables = $instancia->getFillable();

            foreach (array_keys($resolutores) as $coleccion) {
                $candidatos = [
                    $coleccion.'_url_respaldo',
                    str_replace('_pdf', '', $coleccion).'_url_respaldo',
                    'pdf_url_respaldo',
                    'url',
                ];

                if (array_intersect($candidatos, $rellenables) === []) {
                    $sinRespaldo[] = class_basename($modelo).'/'.$coleccion;
                }
            }
        }

        $this->assertSame([], $sinRespaldo, 'Colecciones que no se pueden cambiar desde el panel: '.implode(', ', $sinRespaldo));
    }
}
