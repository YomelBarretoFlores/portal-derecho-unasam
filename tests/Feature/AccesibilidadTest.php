<?php

namespace Tests\Feature;

use App\Models\Revista;
use App\Models\RevistaLineaInvestigacion;
use App\Models\RevistaMiembro;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * PRODUCT.md fija WCAG 2.1 AA como piso obligatorio para una institución
 * pública. Estas comprobaciones cubren lo que se puede verificar sobre el HTML
 * renderizado, sin meter un navegador headless en un repo que solo usa PHPUnit:
 * alt en imágenes, etiquetas en formularios, jerarquía de encabezados e idioma.
 *
 * No sustituyen a una revisión manual de contraste y navegación por teclado.
 */
class AccesibilidadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $revista = Revista::query()->create([
            'nombre' => 'Derecho y Cultura', 'nombre_corto' => 'Derecho y Cultura',
            'presentacion' => '<p>Presentación.</p>', 'unidad_responsable' => 'Unidad de Investigación',
            'resolucion_numero' => '063-2026', 'resolucion_fecha' => '2026-07-06',
            'resolucion_resumen' => 'Creación aprobada.', 'periodicidad' => 'Semestral',
            'contacto_email' => 'revista@unasam.edu.pe', 'normas_publicacion' => '<p>Normas.</p>',
            'estado_editorial' => 'published',
        ]);

        RevistaMiembro::query()->create([
            'revista_id' => $revista->id, 'grupo' => 'editores', 'nombre' => 'Editora',
            'afiliacion' => 'UNASAM', 'pais' => 'Perú', 'activo' => true,
        ]);

        RevistaLineaInvestigacion::query()->create([
            'revista_id' => $revista->id, 'nombre' => 'Antropología jurídica', 'activa' => true,
        ]);

        // El formulario de envíos solo se renderiza con la recepción abierta,
        // y es la superficie de formulario más grande del sitio.
        config()->set('submissions.enabled', true);
        config()->set('submissions.privacy_approved', true);
        config()->set('submissions.storage_persistent', true);
        config()->set('submissions.disk', 'local');
    }

    /** @return array<string, array{0: string}> */
    public static function rutasPublicas(): array
    {
        return array_map(fn (string $url): array => [$url], [
            'inicio' => '/',
            'presentación' => '/presentacion',
            'historia' => '/historia',
            'misión' => '/mision',
            'campo laboral' => '/campo-laboral',
            'objetivos' => '/objetivos',
            'plan 2023' => '/plan-2023',
            'competencias' => '/competencias',
            'perfil de ingreso' => '/perfil-ingreso',
            'blog' => '/blog',
            'docentes' => '/docentes',
            'comunicados' => '/comunicados',
            'documentos' => '/documentos',
            'organigrama' => '/organigrama',
            'revista' => '/revista',
            'revista · envíos' => '/revista/envios',
            'revista · consulta' => '/revista/envios/consulta',
            'revista · normas' => '/revista/normas-para-autores',
            'revista · comité editorial' => '/revista/comite-editorial',
        ]);
    }

    #[DataProvider('rutasPublicas')]
    public function test_every_image_carries_an_alt_attribute(string $url): void
    {
        $xpath = $this->dom($url);

        $sinAlt = [];
        /** @var DOMElement $img */
        foreach ($xpath->query('//img') as $img) {
            if (! $img->hasAttribute('alt')) {
                $sinAlt[] = $img->getAttribute('src') ?: '(sin src)';
            }
        }

        $this->assertSame([], $sinAlt, "Imágenes sin alt en {$url}: ".implode(', ', $sinAlt));
    }

    #[DataProvider('rutasPublicas')]
    public function test_every_form_control_has_an_accessible_name(string $url): void
    {
        $xpath = $this->dom($url);

        $sinNombre = [];
        /** @var DOMElement $control */
        foreach ($xpath->query('//input | //select | //textarea') as $control) {
            $tipo = strtolower($control->getAttribute('type'));
            if (in_array($tipo, ['hidden', 'submit', 'button', 'reset'], true)) {
                continue;
            }

            $id = $control->getAttribute('name') ?: $control->getAttribute('id') ?: $control->nodeName;

            // Un control tiene nombre accesible si lo envuelve un <label>, si hay
            // un <label for> que lo apunte, o si trae aria-label/aria-labelledby.
            if ($control->getAttribute('aria-label') !== '' || $control->getAttribute('aria-labelledby') !== '') {
                continue;
            }
            if ($control->getAttribute('aria-hidden') === 'true') {
                continue;
            }

            $envuelto = false;
            for ($padre = $control->parentNode; $padre !== null; $padre = $padre->parentNode) {
                if ($padre instanceof DOMElement && $padre->nodeName === 'label') {
                    $envuelto = true;
                    break;
                }
            }
            if ($envuelto) {
                continue;
            }

            $idAttr = $control->getAttribute('id');
            if ($idAttr !== '' && $xpath->query(sprintf('//label[@for="%s"]', $idAttr))->length > 0) {
                continue;
            }

            $sinNombre[] = $id;
        }

        $this->assertSame([], $sinNombre, "Controles sin etiqueta en {$url}: ".implode(', ', $sinNombre));
    }

    #[DataProvider('rutasPublicas')]
    public function test_each_page_has_exactly_one_level_one_heading(string $url): void
    {
        $xpath = $this->dom($url);
        $h1 = $xpath->query('//h1');

        $this->assertSame(1, $h1->length, "Se esperaba un único <h1> en {$url}, hay {$h1->length}.");
    }

    #[DataProvider('rutasPublicas')]
    public function test_the_document_declares_its_language_and_a_skip_link(string $url): void
    {
        $xpath = $this->dom($url);

        $this->assertSame('es', $xpath->query('//html')->item(0)?->getAttribute('lang'), "Falta lang=\"es\" en {$url}.");
        $this->assertGreaterThan(0, $xpath->query('//a[@href="#main-content"]')->length, "Falta el enlace de salto en {$url}.");
        $this->assertGreaterThan(0, $xpath->query('//*[@id="main-content"]')->length, "Falta el destino #main-content en {$url}.");
    }

    public function test_every_link_that_opens_a_new_tab_warns_assistive_technology(): void
    {
        // Abrir una pestaña nueva sin avisar rompe la expectativa de quien navega
        // con lector de pantalla (WCAG 3.2.5, cambio de contexto inesperado).
        $xpath = $this->dom('/revista/normas-para-autores');

        /** @var DOMElement $enlace */
        foreach ($xpath->query('//a[@target="_blank"]') as $enlace) {
            $rel = $enlace->getAttribute('rel');
            $this->assertStringContainsString(
                'noopener',
                $rel,
                'Un enlace con target="_blank" debe llevar rel="noopener": '.$enlace->getAttribute('href'),
            );
        }
    }

    public function test_the_journal_dropdown_is_operable_by_keyboard(): void
    {
        // DESIGN.md exige que los desplegables cierren con Escape. El <details>
        // nativo que había antes no lo hacía.
        $html = $this->get('/revista/envios')->assertOk()->getContent();

        $this->assertStringContainsString('keydown.escape.stop', $html, 'El desplegable debe cerrarse con Escape.');
        $this->assertStringContainsString('aria-haspopup="true"', $html);
        $this->assertStringContainsString(':aria-expanded="open"', $html);
        $this->assertStringNotContainsString('<details', $html, 'El <details> nativo no cumple el patrón de teclado exigido.');
    }

    public function test_the_active_navigation_item_is_announced(): void
    {
        $xpath = $this->dom('/revista/envios');

        $this->assertGreaterThan(
            0,
            $xpath->query('//a[@aria-current="page"]')->length,
            'La página actual debe anunciarse con aria-current.',
        );
    }

    public function test_honeypot_fields_are_hidden_from_assistive_technology(): void
    {
        // Un campo trampa visible para un lector de pantalla haría que alguien
        // lo rellenara y su envío se rechazara como spam.
        $xpath = $this->dom('/revista/envios');

        foreach (['website', 'website_correction'] as $campo) {
            $nodo = $xpath->query(sprintf('//input[@name="%s"]', $campo))->item(0);
            $this->assertNotNull($nodo, "Falta el campo trampa {$campo}.");
            $this->assertSame('true', $nodo->getAttribute('aria-hidden'), "El campo trampa {$campo} debe llevar aria-hidden.");
            $this->assertSame('-1', $nodo->getAttribute('tabindex'), "El campo trampa {$campo} debe quedar fuera del orden de tabulación.");
        }
    }

    public function test_no_view_uses_a_text_colour_that_fails_aa_on_light_backgrounds(): void
    {
        // Contrastes medidos sobre el papel de marca (#faf8f4):
        //   stone-400 → 2.38:1    gold-500 → 3.16:1   ← incumplen AA
        //   stone-500 → 4.52:1    gold-600 → 4.72:1    navy-700 → 8.28:1
        //
        // Solo se vigilan estas dos clases porque son las verificadas sobre fondo
        // claro. Los tonos claros de dorado (gold-300/400) se usan sobre navy-950,
        // donde rinden 8.77:1, así que prohibirlos en bloque daría falsos positivos.
        // Los fondos oscuros los cubre el test siguiente.
        $prohibidas = ['text-stone-400', 'text-gold-500'];
        $exentas = ['acceso-card.blade.php'];  // el § dorado es decorativo (aria-hidden)

        $infractores = [];
        foreach (glob(resource_path('views').'/{,*/,*/*/}*.blade.php', GLOB_BRACE) as $vista) {
            if (in_array(basename($vista), $exentas, true)) {
                continue;
            }

            $contenido = (string) file_get_contents($vista);
            foreach ($prohibidas as $clase) {
                if (str_contains($contenido, $clase)) {
                    $infractores[] = basename($vista).' → '.$clase;
                }
            }
        }

        $this->assertSame([], $infractores, "Colores de texto por debajo de 4.5:1 sobre fondo claro:\n".implode("\n", $infractores));
    }

    private function dom(string $url): DOMXPath
    {
        $html = $this->get($url)->assertOk()->getContent();

        $documento = new DOMDocument;
        $anterior = libxml_use_internal_errors(true);
        $documento->loadHTML('<?xml encoding="utf-8" ?>'.$html);
        libxml_clear_errors();
        libxml_use_internal_errors($anterior);

        return new DOMXPath($documento);
    }

    public function test_the_mobile_menu_fallback_never_relies_on_noscript(): void
    {
        // Al navegar con wire:navigate, Livewire reconstruye el documento a partir
        // del HTML recibido y en ese análisis la bandera de scripting está apagada:
        // el contenido de <noscript> se convierte en DOM real. Un <style> ahí dentro
        // pasaba a ser una hoja viva que ocultaba el botón de menú, así que el ícono
        // de hamburguesa desaparecía en todas las páginas posteriores a la primera.
        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringNotContainsString('<noscript', $html,
            'El layout volvió a usar <noscript>: wire:navigate activará su contenido al navegar.');
        $this->assertStringContainsString('class="scroll-smooth sin-js"', $html,
            'Falta la clase «sin-js» en <html>: el respaldo sin JavaScript no se puede decidir en CSS.');
        $this->assertStringContainsString('data-sin-js', $html,
            'Falta la navegación de respaldo para quien no tiene JavaScript.');
        $this->assertStringContainsString('data-mobile-menu-toggle', $html);
    }

    public function test_no_view_uses_a_text_colour_that_fails_aa_on_dark_backgrounds(): void
    {
        // Blanco con opacidad sobre los dos navy de marca (medido):
        //   navy-900 #17335c   white/40 3.36  white/45 3.83  white/50 4.37  ← incumplen
        //                      white/55 4.94  white/60 5.56  white/65 6.29  ← cumplen
        //   navy-950 #0f2240   white/45 4.26  ← incumple ·  white/55 5.72  ← cumple
        //
        // El mínimo seguro para texto es 55 %. Por debajo solo caben elementos
        // decorativos, que van marcados con aria-hidden y a los que WCAG pide
        // 3:1 por ser contenido no textual.
        $prohibidas = ['text-white/25', 'text-white/30', 'text-white/35',
            'text-white/40', 'text-white/45', 'text-white/50'];

        $infractores = [];
        foreach (glob(resource_path('views').'/{,*/,*/*/}*.blade.php', GLOB_BRACE) as $vista) {
            foreach (file($vista) ?: [] as $numero => $linea) {
                if (str_contains($linea, 'aria-hidden')) {
                    continue;  // decorativo: no lo lee nadie
                }

                foreach ($prohibidas as $clase) {
                    if (str_contains($linea, $clase)) {
                        $infractores[] = basename($vista).':'.($numero + 1).' → '.$clase;
                    }
                }
            }
        }

        $this->assertSame([], $infractores,
            "Texto por debajo de 4.5:1 sobre navy (o decorativo sin aria-hidden):\n".implode("\n", $infractores));
    }
}
