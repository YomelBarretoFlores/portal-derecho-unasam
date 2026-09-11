<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * Quien administra el portal es profesora de Derecho, no informática.
 *
 * Los campos «dirección web de respaldo» nacieron con el texto escrito siete
 * veces, y acabó diciendo siete cosas distintas: unos hablaban de «entorno» y
 * «dominio autorizado», y uno —el PDF del número— se quedó directamente sin
 * ninguna explicación. Por eso existe UrlDeRespaldo. Este test impide que el
 * próximo campo se vuelva a escribir a mano.
 */
class TextosParaNoTecnicosTest extends TestCase
{
    /** Palabras que no significan nada para quien no programa. */
    private const JERGA = ['entorno', 'deshabilitad', 'dominio autorizado', 'URL pública', 'CSP', 'disco'];

    /** @return array<int, string> */
    private function archivosDelPanel(): array
    {
        return collect(File::allFiles(app_path('Filament')))
            ->filter(fn ($f): bool => $f->getExtension() === 'php')
            ->map(fn ($f): string => $f->getPathname())
            ->values()
            ->all();
    }

    public function test_no_backup_url_field_is_declared_by_hand(): void
    {
        // Declararlo a mano es lo que produce campos sin ayuda: la fábrica la
        // pone siempre, un TextInput suelto solo si alguien se acuerda.
        $sueltos = [];

        foreach ($this->archivosDelPanel() as $ruta) {
            if (str_ends_with($ruta, 'UrlDeRespaldo.php')) {
                continue;
            }

            foreach (File::lines($ruta) as $n => $linea) {
                if (str_contains($linea, "TextInput::make('") && str_contains($linea, '_url_respaldo')) {
                    $sueltos[] = basename($ruta).':'.($n + 1);
                }
            }
        }

        $this->assertSame([], $sueltos, 'Campos de respaldo escritos a mano en vez de con UrlDeRespaldo: '
            .implode(', ', $sueltos).'. Use UrlDeRespaldo::imagen() o ::archivo().');
    }

    public function test_the_panel_help_texts_avoid_jargon(): void
    {
        $hallazgos = [];

        foreach ($this->archivosDelPanel() as $ruta) {
            foreach (File::lines($ruta) as $n => $linea) {
                if (! str_contains($linea, 'helperText') && ! str_contains($linea, "->label('")) {
                    continue;
                }

                foreach (self::JERGA as $palabra) {
                    if (stripos($linea, $palabra) !== false) {
                        $hallazgos[] = basename($ruta).':'.($n + 1).' («'.$palabra.'»)';
                    }
                }
            }
        }

        $this->assertSame([], $hallazgos, "Jerga técnica en textos que lee quien administra:\n- "
            .implode("\n- ", $hallazgos));
    }
}
