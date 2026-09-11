<?php

namespace Tests\Feature;

use App\Enums\EditorialStatus;
use App\Models\Articulo;
use App\Models\Revista;
use App\Models\RevistaAviso;
use App\Models\RevistaContacto;
use App\Models\RevistaDocumento;
use App\Models\RevistaLineaInvestigacion;
use App\Models\RevistaMiembro;
use App\Models\RevistaNumero;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Hoy varias páginas del micrositio responden 200 únicamente porque las tablas
 * están vacías y sus bucles nunca se ejecutan — así es como pasó inadvertido el
 * 500 de /revista. Este test puebla TODAS las entidades y recorre TODAS las
 * páginas para que ningún camino quede sin ejercitar.
 */
class RevistaPaginasConDatosTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_public_page_renders_with_real_content(): void
    {
        $revista = $this->fullJournal();
        $actual = RevistaNumero::query()->where('es_actual', true)->firstOrFail();
        $anterior = RevistaNumero::query()->where('es_actual', false)->firstOrFail();
        $articulo = Articulo::query()->firstOrFail();

        $rutas = [
            'revista' => route('revista'),
            'actual' => route('revista.actual'),
            'archivos' => route('revista.archivos'),
            'politicas' => route('revista.politicas'),
            'comite editorial' => route('revista.comite-editorial'),
            'comite cientifico' => route('revista.comite-cientifico'),
            'avisos' => route('revista.avisos'),
            'envios' => route('revista.envios'),
            'sobre' => route('revista.sobre'),
            'indexacion' => route('revista.indexacion'),
            'contacto' => route('revista.contacto'),
            'privacidad' => route('revista.privacidad'),
            'preservacion' => route('revista.preservacion'),
            'formatos' => route('revista.formatos'),
            'normas' => route('revista.normas'),
            'numero actual' => route('revista.numero', $actual->slug),
            'numero anterior' => route('revista.numero', $anterior->slug),
            'articulo' => route('revista.articulo', [$actual->slug, $articulo->slug]),
            'home' => route('home'),
            'sitemap' => route('sitemap'),
        ];

        foreach ($rutas as $nombre => $url) {
            $this->get($url)->assertOk("La página «{$nombre}» no renderiza con contenido real.");
        }

        // Y que el contenido efectivamente aparezca, no solo que devuelva 200.
        $this->get(route('revista'))->assertSee($actual->titulo);
        $this->get(route('revista.actual'))->assertSee($actual->titulo);
        $this->get(route('revista.archivos'))->assertSee($anterior->titulo)->assertDontSee($actual->titulo);
        $this->get(route('revista.avisos'))->assertSee('Convocatoria abierta');
        $this->get(route('revista.numero', $actual->slug))->assertSee($articulo->titulo);
        $this->get(route('revista.articulo', [$actual->slug, $articulo->slug]))->assertSee($articulo->titulo);
        $this->get(route('revista.comite-editorial'))->assertSee('Directora Fundadora');
        $this->get(route('revista.comite-cientifico'))->assertSee('Revisor Científico');
        $this->get(route('revista.contacto'))->assertSee('Contacto Editorial');
        $this->get(route('revista.formatos'))->assertSee('Plantilla de artículo');
    }

    public function test_the_sitemap_only_lists_reachable_urls(): void
    {
        $revista = $this->fullJournal();
        $actual = RevistaNumero::query()->where('es_actual', true)->firstOrFail();

        // Un número en borrador con su artículo: ninguno debe aparecer ni ser accesible.
        $borrador = RevistaNumero::query()->create([
            'revista_id' => $revista->id, 'volumen' => '9', 'numero' => '9', 'slug' => 'numero-en-borrador',
            'titulo' => 'Número en borrador', 'fecha_publicacion' => today(), 'estado_editorial' => 'draft',
        ]);
        Articulo::query()->create([
            'revista_numero_id' => $borrador->id, 'titulo' => 'Artículo oculto', 'slug' => 'articulo-oculto',
            'autores' => ['Autora'], 'categoria' => 'Ensayo', 'resumen' => 'R.', 'contenido' => '<p>C.</p>',
            'fecha' => today(), 'estado_editorial' => 'draft',
        ]);

        $sitemap = $this->get(route('sitemap'))->assertOk();
        $sitemap->assertSee(route('revista.numero', $actual->slug));
        $sitemap->assertDontSee('numero-en-borrador');
        $sitemap->assertDontSee('articulo-oculto');

        $this->get(route('revista.numero', $borrador->slug))->assertNotFound();
    }

    private function fullJournal(): Revista
    {
        $revista = Revista::query()->create([
            'nombre' => 'Derecho y Cultura', 'nombre_corto' => 'Derecho y Cultura',
            'presentacion' => '<p>Presentación.</p>', 'enfoque_alcance' => '<p>Enfoque.</p>',
            'unidad_responsable' => 'Unidad de Investigación', 'resolucion_numero' => '063-2026',
            'resolucion_fecha' => '2026-07-06', 'resolucion_resumen' => 'Creación aprobada.',
            'periodicidad' => 'Semestral', 'contacto_email' => 'revista@unasam.edu.pe',
            'normas_publicacion' => '<p>Normas oficiales.</p>', 'introduccion_envios' => '<p>Introducción.</p>',
            'contenido_sobre' => '<p>Sobre la revista.</p>', 'contenido_preservacion' => '<p>Preservación.</p>',
            'estado_editorial' => EditorialStatus::Published->value,
        ]);

        foreach ([
            ['director_fundador', 'Directora Fundadora'],
            ['editores', 'Editor Responsable'],
            ['comite_editorial', 'Miembro del Comité'],
            ['correctores_estilo', 'Corrector de Estilo'],
            ['asistentes_editoriales', 'Asistente Editorial'],
            ['consejo_cientifico', 'Consejero Científico'],
            ['consejo_revisores', 'Revisor Científico'],
        ] as [$grupo, $nombre]) {
            RevistaMiembro::query()->create([
                'revista_id' => $revista->id, 'grupo' => $grupo, 'nombre' => $nombre,
                'afiliacion' => 'UNASAM', 'pais' => 'Perú', 'activo' => true,
            ]);
        }

        $actual = RevistaNumero::query()->create([
            'revista_id' => $revista->id, 'volumen' => '1', 'numero' => '2', 'slug' => 'volumen-1-numero-2',
            'titulo' => 'Número actual', 'descripcion' => 'Descripción del número actual.',
            'fecha_publicacion' => today(), 'es_actual' => true, 'estado_editorial' => 'published',
        ]);

        RevistaNumero::query()->create([
            'revista_id' => $revista->id, 'volumen' => '1', 'numero' => '1', 'slug' => 'volumen-1-numero-1',
            'titulo' => 'Número anterior', 'descripcion' => 'Primera edición.',
            'fecha_publicacion' => today()->subMonths(6), 'es_actual' => false, 'estado_editorial' => 'published',
        ]);

        Articulo::query()->create([
            'revista_numero_id' => $actual->id, 'titulo' => 'Pluralismo jurídico en Áncash',
            'slug' => 'pluralismo-juridico-ancash', 'autores' => ['Autora Principal', 'Coautor Segundo'],
            'paginas' => '11-34', 'categoria' => 'Artículo original', 'doi' => '10.0000/dyc.1',
            'resumen' => 'Resumen del artículo.', 'contenido' => '<p>Cuerpo del artículo.</p>',
            'fecha' => today(), 'estado_editorial' => 'published',
        ]);

        RevistaAviso::query()->create([
            'revista_id' => $revista->id, 'titulo' => 'Convocatoria abierta', 'slug' => 'convocatoria-abierta',
            'contenido' => '<p>Recepción de manuscritos.</p>', 'fecha_publicacion' => today(),
            'estado_editorial' => 'published',
        ]);

        RevistaDocumento::query()->create([
            'revista_id' => $revista->id, 'categoria' => 'formato', 'titulo' => 'Plantilla de artículo',
            'url' => '/docs/plantilla.docx', 'visible' => true, 'estado_editorial' => 'published',
        ]);
        RevistaDocumento::query()->create([
            'revista_id' => $revista->id, 'categoria' => 'norma', 'titulo' => 'Normas de publicación',
            'url' => '/docs/normas.pdf', 'visible' => true, 'estado_editorial' => 'published',
        ]);

        RevistaContacto::query()->create([
            'revista_id' => $revista->id, 'tipo' => 'persona', 'nombre' => 'Contacto Editorial',
            'email' => 'contacto@unasam.edu.pe', 'visible' => true,
        ]);

        RevistaLineaInvestigacion::query()->create([
            'revista_id' => $revista->id, 'nombre' => 'Antropología jurídica', 'activa' => true,
        ]);

        return $revista;
    }
}
