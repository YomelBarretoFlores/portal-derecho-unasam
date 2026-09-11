<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Comunicado;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * El hero de la portada muestra una tira con lo último publicado. Es la única
 * parte del sitio que mezcla tres fuentes —comunicados, blog y avisos de la
 * revista—, así que conviene fijar su comportamiento: qué aparece, en qué orden
 * y qué pasa cuando no hay nada que mostrar.
 */
class HeroTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    private function comunicado(string $titulo, string $fecha): Comunicado
    {
        return Comunicado::query()->create([
            'titulo' => $titulo, 'resumen' => 'R.', 'contenido' => '<p>C.</p>',
            'publicado' => true, 'fecha_publicacion' => $fecha, 'estado_editorial' => 'published',
        ]);
    }

    public function test_the_newest_item_leads_the_hero(): void
    {
        $this->comunicado('Convocatoria de prácticas', '2026-01-10');
        BlogPost::query()->create([
            'tipo' => 'noticia', 'titulo' => 'Nueva biblioteca jurídica', 'slug' => 'nueva-biblioteca',
            'extracto' => 'La facultad estrena sala de lectura.', 'contenido' => '<p>C.</p>',
            'autor' => 'Redacción', 'fecha' => '2026-02-20', 'publicado' => true,
            'estado_editorial' => 'published',
        ]);

        $html = $this->get(route('home'))->assertOk()->getContent();

        // El hero cuenta lo último publicado, con su resumen y su enlace: es la
        // diferencia entre abrir diciendo qué pasa y abrir repitiendo el nombre
        // de la facultad, que ya está en la barra de arriba.
        $this->assertStringContainsString('Nueva biblioteca jurídica', $html);
        $this->assertStringContainsString('La facultad estrena sala de lectura.', $html);
        $this->assertStringContainsString('Leer más', $html);

        // Y la tira empieza en el siguiente: repetir el mismo titular a diez
        // centímetros haría parecer que hay menos contenido del que hay.
        // (Más abajo sí reaparece, en la sección de blog, y ahí corresponde.)
        // Str::between() corta por el ÚLTIMO delimitador, así que se llevaba la
        // página entera. Aquí hace falta el primer cierre de sección.
        $tira = Str::before(Str::after($html, 'Lo último'), '</section>');

        $this->assertStringNotContainsString('Nueva biblioteca jurídica', $tira);
        $this->assertStringContainsString('Convocatoria de prácticas', $tira);
    }

    public function test_without_published_content_the_hero_falls_back_to_the_identity(): void
    {
        // Instalación nueva: todavía no hay nada publicado. Abrir con un hueco
        // sería peor que abrir con el nombre de la facultad.
        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringContainsString('Derecho y Ciencias Políticas', $html);
        $this->assertStringContainsString('Conoce el programa', $html);
    }

    public function test_the_programme_figures_left_the_hero_for_the_text_that_explains_them(): void
    {
        $html = $this->get(route('home'))->assertOk()->getContent();

        $hero = strpos($html, 'hero-title');
        $cifras = strpos($html, 'AÑOS DE TRAYECTORIA') ?: strpos($html, 'Años de trayectoria');
        $programa = strpos($html, 'home_about_titulo') ?: strpos($html, 'Formando profesionales');

        $this->assertNotFalse($cifras, 'Las cifras del programa desaparecieron de la portada.');
        $this->assertGreaterThan($hero, $cifras, 'Las cifras siguen dentro del hero.');
        $this->assertGreaterThan($programa, $cifras, 'Las cifras deben acompañar al texto que las explica.');
    }

    public function test_the_hero_strip_disappears_when_there_is_nothing_published(): void
    {
        // El sitio arrancó sin contenido y volverá a estar así en una instalación
        // nueva: una tira vacía con flechas que no llevan a ninguna parte sería
        // peor que no tener tira.
        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringNotContainsString('Lo último', $html);
    }

    public function test_a_single_published_item_leaves_no_empty_strip(): void
    {
        // Con una sola publicación, esa encabeza el hero y no queda nada para
        // la tira. Mostrarla vacía, o repetir ahí el mismo titular, haría
        // parecer que el sitio tiene menos contenido del que tiene.
        $this->comunicado('Único aviso', '2026-03-01');

        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertSame(1, substr_count($html, 'Único aviso'));
        $this->assertStringNotContainsString('Lo último', $html);
        $this->assertStringNotContainsString('aria-label="Siguiente"', $html);
    }

    public function test_the_mascot_is_absent_until_the_faculty_provides_one(): void
    {
        // Sin imagen el hero queda exactamente como estaba: ni hueco reservado,
        // ni una etiqueta vacía que el navegador pinte como imagen rota.
        Setting::set('home_hero_mascota_url', '');

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringNotContainsString('object-bottom', $html);
    }

    public function test_the_mascot_is_shown_when_it_is_configured(): void
    {
        config()->set('seguridad.csp.img_hosts', ['cdn.unasam.edu.pe']);

        Setting::set('home_hero_mascota_url', 'https://cdn.unasam.edu.pe/mascota.webp');
        Setting::set('home_hero_mascota_alt', 'Mascota de la Facultad de Derecho');

        $this->get('/')->assertOk()
            ->assertSee('https://cdn.unasam.edu.pe/mascota.webp')
            ->assertSee('Mascota de la Facultad de Derecho');
    }

    public function test_a_mascot_without_description_is_hidden_from_screen_readers(): void
    {
        // Una ilustración sin descripción no aporta nada a quien no la ve;
        // anunciarla antes del titular estorbaría en vez de ayudar.
        Setting::set('home_hero_mascota_url', '/img/mascota.webp');
        Setting::set('home_hero_mascota_alt', '');

        $this->get('/')->assertOk()
            ->assertSee('/img/mascota.webp')
            ->assertSee('aria-hidden="true"', escape: false);
    }

    public function test_the_hero_photograph_is_described(): void
    {
        // Es la única fotografía institucional del sitio y ocupa el hero entero:
        // sin alt, quien usa lector de pantalla no recibe nada de la portada.
        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringContainsString('campus-derecho', $html);
        $this->assertMatchesRegularExpression('/alt="Patio de la Facultad[^"]+Cordillera Blanca[^"]*"/u', $html);
    }
}
