<?php

namespace Tests\Feature;

use App\Models\Acceso;
use App\Models\AreaLaboral;
use App\Models\BlogPost;
use App\Models\Competencia;
use App\Models\Comunicado;
use App\Models\Curso;
use App\Models\Docente;
use App\Models\Documento;
use App\Models\Estadistica;
use App\Models\Hito;
use App\Models\Objetivo;
use App\Models\Organigrama;
use App\Models\PerfilIngresoArea;
use App\Models\Setting;
use App\Services\ObjetivoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Equivalente de RevistaPaginasConDatosTest para el resto del portal.
 *
 * Muchas de estas páginas responden 200 en producción solo porque sus tablas
 * están vacías y los bucles nunca se ejecutan: así se ocultó el 500 de /revista.
 * Aquí se recorren en los dos extremos — con contenido y sin él.
 */
class PortalPaginasConDatosTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, string> */
    private function rutasPublicas(): array
    {
        return [
            'inicio' => route('home'),
            'presentación' => route('presentacion'),
            'resumen' => route('resumen'),
            'historia' => route('historia'),
            'misión' => route('mision'),
            'campo laboral' => route('campo-laboral'),
            'objetivos' => route('objetivos'),
            'plan 2023' => route('plan-2023'),
            'plan 2019' => route('plan-2019'),
            'competencias' => route('competencias'),
            'perfil de ingreso' => route('perfil-ingreso'),
            'perfil de egreso' => route('perfil-egreso'),
            'blog' => route('blog'),
            'docentes' => route('docentes'),
            'comunicados' => route('comunicados'),
            'organigrama' => route('organigrama'),
            'documentos' => route('documentos'),
            'sitemap' => route('sitemap'),
        ];
    }

    public function test_every_public_page_renders_with_real_content(): void
    {
        $this->poblarPortal();

        foreach ($this->rutasPublicas() as $nombre => $url) {
            $this->get($url)->assertOk("La página «{$nombre}» no renderiza con contenido real.");
        }

        foreach (['matriculados', 'egresados', 'graduados', 'titulados'] as $tipo) {
            $this->get(route('estadisticas', $tipo))->assertOk("Estadística «{$tipo}» rota.");
        }

        // Y que el contenido llegue de verdad a la página, no solo un 200.
        $this->get(route('historia'))->assertSee('Creación del programa');
        $this->get(route('campo-laboral'))->assertSee('Litigio');
        $this->get(route('objetivos'))->assertSee('Formar juristas');
        $this->get(route('competencias'))->assertSee('Argumentación jurídica');
        $this->get(route('perfil-ingreso'))->assertSee('Razonamiento verbal');
        $this->get(route('plan-2023'))->assertSee('Derecho Civil')->assertSee('Plan de Estudios 2019');
        $this->get(route('organigrama'))->assertSee('Estructura del programa');
        $this->get(route('documentos'))->assertSee('Reglamento general');
        $this->get(route('docentes'))->assertSee('Docente Verificada');
        $this->get(route('blog'))->assertSee('Nota de prueba');
        $this->get(route('comunicados'))->assertSee('Comunicado de prueba');
    }

    public function test_every_public_page_degrades_gracefully_with_no_content_at_all(): void
    {
        // Ni una sola fila de contenido: ninguna página debe romperse.
        foreach ($this->rutasPublicas() as $nombre => $url) {
            $this->get($url)->assertOk("La página «{$nombre}» se rompe sin contenido.");
        }
    }

    public function test_a_teacher_publication_link_is_rendered(): void
    {
        $this->poblarPortal();

        $this->get(route('docentes.show', 'docente-verificada'))
            ->assertOk()
            ->assertSee('https://doi.org/10.0000/publicacion', false);
    }

    public function test_a_post_without_reading_time_does_not_print_a_dangling_label(): void
    {
        $this->poblarPortal();
        BlogPost::query()->update(['tiempo_lectura' => null]);

        $this->get(route('blog'))->assertOk()->assertDontSee('de lectura');
    }

    public function test_settings_cache_is_invalidated_by_any_write_path(): void
    {
        Setting::query()->create(['clave' => 'footer_marca', 'valor' => 'Valor inicial']);
        $this->assertSame('Valor inicial', Setting::get('footer_marca'));

        // Escritura directa, sin pasar por Setting::set(): la caché rememberForever
        // dejaba el pie de página y el SEO congelados de forma indefinida.
        Setting::query()->where('clave', 'footer_marca')->first()->update(['valor' => 'Valor corregido']);
        $this->assertSame('Valor corregido', Setting::get('footer_marca'));

        Setting::query()->where('clave', 'footer_marca')->first()->delete();
        $this->assertNull(Setting::get('footer_marca'));
    }

    public function test_the_current_plan_is_flagged_from_any_marked_row(): void
    {
        Objetivo::query()->create(['plan' => 'Plan de Estudios 2023', 'texto' => 'Primero', 'orden' => 1, 'vigente' => false]);
        Objetivo::query()->create(['plan' => 'Plan de Estudios 2023', 'texto' => 'Segundo', 'orden' => 2, 'vigente' => true]);

        // El panel ofrece el interruptor en cada fila: marcar la segunda debe contar.
        $planes = app(ObjetivoService::class)->planes();

        $this->assertTrue($planes->firstWhere('titulo', 'Plan de Estudios 2023')['destacado']);
    }

    public function test_homepage_headings_are_driven_by_the_settings_panel(): void
    {
        $this->poblarPortal();
        Setting::set('home_accesos_titulo', 'Encabezado editable uno');
        Setting::set('home_accesos_eyebrow', 'Encabezado editable dos');
        Setting::set('home_blog_eyebrow', 'Antetítulo de blog editable');

        // Estos tres ajustes eran editables y se guardaban, pero ninguna vista
        // los renderizaba: el editor creía estar cambiando la portada.
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Encabezado editable uno')
            ->assertSee('Encabezado editable dos')
            ->assertSee('Antetítulo de blog editable');
    }

    private function poblarPortal(): void
    {
        Hito::query()->create(['anio' => '1986', 'titulo' => 'Creación del programa', 'descripcion' => 'Resolución fundacional.', 'orden' => 1]);
        AreaLaboral::query()->create(['titulo' => 'Litigio', 'descripcion' => 'Ejercicio libre de la profesión.', 'orden' => 1]);
        Objetivo::query()->create(['plan' => 'Plan de Estudios 2023', 'texto' => 'Formar juristas íntegros.', 'orden' => 1, 'vigente' => true]);
        Competencia::query()->create(['plan' => 'Plan de Estudios 2023', 'grupo' => 'Generales', 'nombre' => 'Argumentación jurídica', 'texto' => 'Sustenta posiciones.', 'orden' => 1, 'vigente' => true]);
        PerfilIngresoArea::query()->create(['titulo' => 'Razonamiento verbal', 'items' => ['Comprensión lectora'], 'orden' => 1]);
        Acceso::query()->create(['titulo' => 'Biblioteca', 'descripcion' => 'Catálogo en línea.', 'url' => 'https://unasam.edu.pe', 'activo' => true, 'orden' => 1]);

        foreach (['2023', '2019'] as $plan) {
            Curso::query()->create(['plan' => $plan, 'ciclo' => 'I', 'nombre' => 'Derecho Civil', 'creditos' => 4, 'orden' => 1]);
        }

        foreach (['matriculados', 'egresados', 'graduados', 'titulados'] as $tipo) {
            Estadistica::query()->create(['tipo' => $tipo, 'anio' => 2024, 'total' => 120]);
        }

        Organigrama::query()->create(['titulo' => 'Estructura del programa', 'descripcion' => 'Organización interna.']);
        Documento::query()->create(['titulo' => 'Reglamento general', 'categoria' => 'Reglamentos', 'fecha' => today(), 'url' => 'https://unasam.edu.pe/reglamento.pdf', 'orden' => 1]);

        Docente::query()->create([
            'name' => 'Docente Verificada', 'slug' => 'docente-verificada', 'categoria' => 'Principal',
            'grado' => 'Dra.', 'documento_fuente' => 'Resolución 001', 'activo' => true, 'orden' => 1,
            'dedicacion' => 'Tiempo completo', 'resena' => 'Docente investigadora del programa.',
            'publicaciones' => [['titulo' => 'Estudio sobre pluralismo', 'anio' => '2024', 'url' => 'https://doi.org/10.0000/publicacion']],
            'estado_editorial' => 'published',
        ]);

        BlogPost::query()->create([
            'titulo' => 'Nota de prueba', 'slug' => 'nota-de-prueba', 'tipo' => 'noticia',
            'resumen' => 'Resumen.', 'contenido' => '<p>Cuerpo.</p>', 'autor' => 'Redacción',
            'tiempo_lectura' => '3 min', 'fecha' => today(), 'estado_editorial' => 'published',
        ]);

        Comunicado::query()->create([
            'titulo' => 'Comunicado de prueba', 'slug' => 'comunicado-de-prueba',
            'resumen' => 'Resumen.', 'contenido' => '<p>Cuerpo.</p>',
            'fecha_publicacion' => today(), 'estado_editorial' => 'published',
        ]);
    }
}
