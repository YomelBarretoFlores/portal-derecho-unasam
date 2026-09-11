<?php

namespace Tests\Feature;

use App\Enums\EditorialStatus;
use App\Models\BlogPost;
use App\Models\Comunicado;
use App\Models\Docente;
use App\Models\Organigrama;
use App\Models\Revista;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Todo archivo que se ve en el sitio tiene que poder cambiarse desde el panel.
 *
 * Cinco no podían, por ningún medio: el logo de la revista, el PDF de la
 * resolución, la imagen de los comunicados, la del blog y la del organigrama.
 * El caso del logo era el más engañoso, porque el campo SÍ existía en el
 * panel: se podía subir un logo, el formulario lo aceptaba, y la portada de
 * la revista seguía pintando un archivo fijo del repositorio sin mirarlo.
 */
class MediosAdministrablesTest extends TestCase
{
    use RefreshDatabase;

    private const DOMINIO = 'cdn.unasam.edu.pe';

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('media.uploads_enabled', false);
        config()->set('seguridad.csp.img_hosts', [self::DOMINIO]);
    }

    private function revista(array $extra = []): Revista
    {
        return Revista::query()->create([
            'nombre' => 'Derecho y Cultura',
            'nombre_corto' => 'Derecho y Cultura',
            'presentacion' => '<p>Presentación.</p>',
            'unidad_responsable' => 'Unidad de Investigación',
            'resolucion_numero' => '063-2026',
            'resolucion_fecha' => '2026-07-06',
            'resolucion_resumen' => 'Creación aprobada.',
            'periodicidad' => 'Semestral',
            'contacto_email' => 'revista@unasam.edu.pe',
            'estado_editorial' => EditorialStatus::Published->value,
            ...$extra,
        ]);
    }

    public function test_the_journal_logo_comes_from_the_admin_panel(): void
    {
        $logo = 'https://'.self::DOMINIO.'/revista/logo-2026.png';
        $this->revista(['logo_url_respaldo' => $logo]);

        $this->get(route('revista'))
            ->assertOk()
            ->assertSee($logo)
            // El archivo del repositorio deja de imponerse. Sigue existiendo
            // como red de seguridad, pero ya no tapa lo que se administre.
            ->assertDontSee(Revista::LOGO_PUBLIC_PATH);
    }

    public function test_the_repository_logo_is_still_the_safety_net(): void
    {
        $this->revista();

        $this->get(route('revista'))->assertOk()->assertSee(Revista::LOGO_PUBLIC_PATH);
    }

    public function test_the_creation_resolution_can_be_replaced_from_the_panel(): void
    {
        $pdf = 'https://'.self::DOMINIO.'/revista/resolucion-063-2026.pdf';
        $revista = $this->revista(['resolucion_url_respaldo' => $pdf]);

        $this->assertSame($pdf, $revista->resolution_url);
    }

    public function test_the_organisation_chart_can_show_an_image_again(): void
    {
        $imagen = 'https://'.self::DOMINIO.'/organigrama-2026.png';

        Organigrama::singleton()->update([
            'titulo' => 'Organigrama',
            'imagen_url_respaldo' => $imagen,
        ]);

        // Antes devolvía null sin más: con las cargas deshabilitadas la página
        // no tenía forma alguna de mostrar un organigrama.
        $this->get(route('organigrama'))->assertOk()->assertSee($imagen);
    }

    public function test_an_announcement_can_be_illustrated(): void
    {
        $imagen = 'https://'.self::DOMINIO.'/comunicados/matricula.jpg';

        Comunicado::query()->create([
            'titulo' => 'Cronograma de matrícula',
            'slug' => 'cronograma-de-matricula',
            'resumen' => 'Fechas del periodo.',
            'contenido' => '<p>Detalle.</p>',
            'publicado' => true,
            'fecha_publicacion' => now()->subDay(),
            'imagen_url_respaldo' => $imagen,
            'estado_editorial' => 'published',
        ]);

        $this->get(route('comunicados'))->assertOk()->assertSee($imagen);
        $this->get(route('comunicados.show', 'cronograma-de-matricula'))->assertOk()->assertSee($imagen);
    }

    public function test_a_blog_entry_can_be_illustrated(): void
    {
        $imagen = 'https://'.self::DOMINIO.'/blog/congreso.jpg';

        BlogPost::query()->create([
            'tipo' => 'noticia',
            'titulo' => 'Congreso de Derecho Constitucional',
            'slug' => 'congreso-de-derecho-constitucional',
            'extracto' => 'Resumen.',
            'contenido' => '<p>Cuerpo.</p>',
            'autor' => 'Redacción',
            'fecha' => today(),
            'publicado' => true,
            'imagen_url_respaldo' => $imagen,
            'estado_editorial' => 'published',
        ]);

        $this->get(route('blog'))->assertOk()->assertSee($imagen);
        $this->get(route('blog.show', 'congreso-de-derecho-constitucional'))->assertOk()->assertSee($imagen);
    }

    public function test_a_new_lecturer_can_have_a_portrait_without_touching_code(): void
    {
        // Los nueve retratos verificados viven en config/docentes.php. Un
        // docente que no esté en esa lista no tenía manera de tener foto sin
        // editar código y desplegar.
        $retrato = 'https://'.self::DOMINIO.'/docentes/nueva-docente.webp';

        $docente = Docente::query()->create([
            'name' => 'Nueva Docente Incorporada',
            'slug' => 'nueva-docente-incorporada',
            'area' => 'Derecho Civil',
            'categoria' => 'Asociado',
            'dedicacion' => 'Tiempo completo',
            'resena' => 'Reseña académica.',
            'activo' => true,
            'documento_fuente' => 'Resolución de contratación 2026.',
            'foto_url_respaldo' => $retrato,
            'estado_editorial' => 'published',
        ]);

        $this->assertSame($retrato, $docente->fotoPublicaUrl());
        $this->get(route('docentes'))->assertOk()->assertSee($retrato);
    }

    public function test_the_documented_portrait_still_wins_when_nothing_is_administered(): void
    {
        $slug = array_key_first(config('docentes.fotos_institucionales'));

        $docente = Docente::query()->create([
            'name' => 'Docente Documentado',
            'slug' => $slug,
            'area' => 'Derecho Penal',
            'categoria' => 'Asociado',
            'dedicacion' => 'Tiempo completo',
            'resena' => 'Reseña académica.',
            'activo' => true,
            'documento_fuente' => 'Resolución de contratación 2026.',
            'estado_editorial' => 'published',
        ]);

        $this->assertSame(
            '/'.config('docentes.fotos_institucionales.'.$slug),
            $docente->fotoPublicaUrl(),
        );
    }
}
