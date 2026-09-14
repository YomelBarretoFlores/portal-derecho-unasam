<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * Ningún PDF incrustado puede quedar visible en un teléfono.
 *
 * Chrome en Android y Safari en iPhone no dibujan un PDF dentro de un iframe:
 * dejan el recuadro vacío. En «Normas para autores» eso daba medio metro de
 * gris con un icono de archivo roto, y desde el móvil parecía que la página
 * había fallado —cuando el navegador nunca iba a poder mostrarlo—.
 *
 * Se revisa sobre el marcado y no pidiendo la página porque el fallo es del
 * navegador, no del servidor: el HTML llega perfecto y aun así no se ve nada.
 * Lo único que se puede comprobar aquí es que no se le ofrezca al teléfono.
 */
class VisorDePdfTest extends TestCase
{
    public function test_no_embedded_pdf_is_offered_to_a_phone(): void
    {
        $sinOcultar = [];

        foreach (File::allFiles(resource_path('views')) as $archivo) {
            if ($archivo->getExtension() !== 'php') {
                continue;
            }

            foreach (File::lines($archivo->getPathname()) as $n => $linea) {
                if (! str_contains($linea, '<iframe') && ! str_contains($linea, '<embed')) {
                    continue;
                }

                // El contenedor lleva la clase; la etiqueta puede ir en otra
                // línea, así que se mira el bloque entero del fichero.
                $contenido = File::get($archivo->getPathname());

                if (! str_contains($contenido, 'hidden') || ! preg_match('/(lg|xl):block/', $contenido)) {
                    $sinOcultar[] = $archivo->getFilename().':'.($n + 1);
                }
            }
        }

        $this->assertSame([], $sinOcultar, 'PDF incrustado sin ocultar en móvil: '.implode(', ', $sinOcultar)
            .'. Envuélvalo en «hidden lg:block» y deje los botones de abrir y descargar, que sí funcionan en el teléfono.');
    }

    public function test_the_rules_page_always_offers_a_way_to_reach_the_pdf(): void
    {
        // Ocultar el visor solo es aceptable porque el documento sigue siendo
        // alcanzable. Si alguien quitara los botones, el móvil se quedaría sin
        // ninguna forma de leer las normas oficiales.
        $vista = File::get(resource_path('views/revista/normas.blade.php'));

        $this->assertStringContainsString('Abrir PDF', $vista);
        $this->assertStringContainsString('Descargar PDF', $vista);
    }
}
