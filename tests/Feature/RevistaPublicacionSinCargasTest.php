<?php

namespace Tests\Feature;

use App\Enums\EditorialStatus;
use App\Models\Articulo;
use App\Models\Revista;
use App\Models\RevistaAviso;
use App\Models\RevistaNumero;
use App\Services\PublicContentCache;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Escenario de Render hoy: MEDIA_UPLOADS_ENABLED=false, sin almacenamiento
 * persistente. Todo el circuito de publicación debe funcionar solo con URLs
 * públicas de respaldo, igual que ya hacía RevistaDocumento.
 */
class RevistaPublicacionSinCargasTest extends TestCase
{
    use RefreshDatabase;

    private const PORTADA = 'https://cdn.unasam.edu.pe/revista/portada-v1n1.jpg';

    private const NUMERO_PDF = 'https://cdn.unasam.edu.pe/revista/derecho-y-cultura-v1n1.pdf';

    private const ARTICULO_PDF = 'https://cdn.unasam.edu.pe/revista/pluralismo-juridico.pdf';

    private const ADJUNTO = 'https://cdn.unasam.edu.pe/revista/convocatoria.pdf';

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('media.uploads_enabled', false);

        // El dominio tiene que estar autorizado o la cabecera CSP bloquearía
        // la portada en el navegador. Sin esta línea el test comprobaría un
        // escenario que sale bien en el HTML y mal en la pantalla.
        config()->set('seguridad.csp.img_hosts', ['cdn.unasam.edu.pe']);
    }

    public function test_a_full_issue_publishes_and_renders_with_url_fallbacks_only(): void
    {
        [$revista, $numero, $articulo] = $this->publishedIssue();

        $this->get(route('revista'))->assertOk()->assertSee(self::PORTADA);
        $this->get(route('revista.actual'))->assertOk()
            ->assertSee(self::PORTADA)
            ->assertSee(self::NUMERO_PDF)
            ->assertSee('Descargar número completo (PDF)');
        $this->get(route('revista.numero', $numero->slug))->assertOk()
            ->assertSee(self::NUMERO_PDF)
            ->assertSee($articulo->titulo);
        $this->get(route('revista.articulo', [$numero->slug, $articulo->slug]))->assertOk()
            ->assertSee(self::ARTICULO_PDF)
            ->assertSee('Descargar PDF');
        $this->get(route('revista.avisos'))->assertOk()->assertSee(self::ADJUNTO);
    }

    public function test_an_article_without_web_content_is_public_thanks_to_its_fallback_url(): void
    {
        [, $numero] = $this->publishedIssue();

        // Sin contenido web, sin PDF subido: solo la URL de respaldo.
        $soloPdf = Articulo::query()->create([
            'revista_numero_id' => $numero->id, 'titulo' => 'Solo PDF', 'slug' => 'solo-pdf',
            'autores' => ['Autora'], 'categoria' => 'Reseña', 'resumen' => 'Resumen.',
            'contenido' => null, 'pdf_url_respaldo' => self::ARTICULO_PDF,
            'fecha' => today(), 'estado_editorial' => 'published',
        ]);

        $this->assertTrue(
            Articulo::query()->publicados()->whereKey($soloPdf->getKey())->exists(),
            'Un artículo con URL de respaldo debe contar como publicado.',
        );

        $this->get(route('revista.numero', $numero->slug))->assertOk()->assertSee('Solo PDF');
        $this->get(route('revista.articulo', [$numero->slug, 'solo-pdf']))->assertOk();
    }

    public function test_an_article_with_neither_content_nor_any_file_stays_hidden(): void
    {
        [, $numero] = $this->publishedIssue();

        $vacio = Articulo::query()->create([
            'revista_numero_id' => $numero->id, 'titulo' => 'Sin nada', 'slug' => 'sin-nada',
            'autores' => ['Autora'], 'categoria' => 'Reseña', 'resumen' => 'Resumen.',
            'contenido' => null, 'fecha' => today(), 'estado_editorial' => 'published',
        ]);

        $this->assertFalse(Articulo::query()->publicados()->whereKey($vacio->getKey())->exists());
        $this->get(route('revista.numero', $numero->slug))->assertDontSee('Sin nada');
    }

    public function test_uploaded_files_still_win_when_uploads_are_enabled(): void
    {
        config()->set('media.uploads_enabled', true);
        [, $numero] = $this->publishedIssue();

        // Sin archivo subido, el accesor cae en el respaldo aunque las cargas estén activas.
        $this->assertSame(self::PORTADA, $numero->fresh()->portada_url);
        $this->assertSame(self::NUMERO_PDF, $numero->fresh()->pdf_url);
    }

    public function test_the_public_cache_serves_the_resolved_url(): void
    {
        [, $numero] = $this->publishedIssue();

        // El índice se sirve desde PublicContentCache, que entrega stdClass:
        // la URL debe venir ya resuelta desde el servicio, no desde la vista.
        $datos = app(PublicContentCache::class)->revista(null);
        $primero = $datos['numeros']->items()[0];

        $this->assertSame(self::PORTADA, $primero->_portada_url);
    }

    /** @return array{0: Revista, 1: RevistaNumero, 2: Articulo} */
    private function publishedIssue(): array
    {
        $revista = Revista::query()->create([
            'nombre' => 'Derecho y Cultura', 'nombre_corto' => 'Derecho y Cultura',
            'presentacion' => '<p>P.</p>', 'unidad_responsable' => 'Unidad de Investigación',
            'resolucion_numero' => '063-2026', 'resolucion_fecha' => '2026-07-06',
            'resolucion_resumen' => 'Creación aprobada.', 'periodicidad' => 'Semestral',
            'contacto_email' => 'revista@unasam.edu.pe',
            'estado_editorial' => EditorialStatus::Published->value,
        ]);

        $numero = RevistaNumero::query()->create([
            'revista_id' => $revista->id, 'volumen' => '1', 'numero' => '1',
            'slug' => 'volumen-1-numero-1', 'titulo' => 'Primer número',
            'descripcion' => 'Edición inaugural.', 'fecha_publicacion' => today(),
            'es_actual' => true, 'estado_editorial' => 'published',
            'portada_url_respaldo' => self::PORTADA,
            'pdf_url_respaldo' => self::NUMERO_PDF,
        ]);

        $articulo = Articulo::query()->create([
            'revista_numero_id' => $numero->id, 'titulo' => 'Pluralismo jurídico',
            'slug' => 'pluralismo-juridico', 'autores' => ['Autora Principal'],
            'paginas' => '11-34', 'categoria' => 'Artículo original',
            'resumen' => 'Resumen.', 'contenido' => '<p>Cuerpo.</p>', 'fecha' => today(),
            'pdf_url_respaldo' => self::ARTICULO_PDF, 'estado_editorial' => 'published',
        ]);

        RevistaAviso::query()->create([
            'revista_id' => $revista->id, 'titulo' => 'Convocatoria abierta',
            'slug' => 'convocatoria-abierta', 'contenido' => '<p>Recepción.</p>',
            'fecha_publicacion' => today(), 'adjunto_url_respaldo' => self::ADJUNTO,
            'estado_editorial' => 'published',
        ]);

        return [$revista, $numero, $articulo];
    }
}
