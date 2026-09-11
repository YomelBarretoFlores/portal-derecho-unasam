<?php

namespace Tests\Feature;

use App\Enums\EditorialStatus;
use App\Models\Revista;
use App\Models\RevistaMiembro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RevistaDocumentalTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_verified_journal_created_by_the_cms_is_public_without_inventing_an_issn(): void
    {
        $revista = $this->createJournal();

        $this->assertSame('Derecho y Cultura', $revista->nombre_corto);
        $this->assertSame('N.° 063-2026-UNASAM-FDCCPP/D.', $revista->resolucion_numero);
        $this->assertSame('2026-07-06', $revista->resolucion_fecha->toDateString());
        $this->assertNull($revista->issn);
        $this->assertTrue($revista->activo);
        $this->assertTrue($revista->publicado);
        $this->assertSame(EditorialStatus::Published->value, $revista->estado_editorial);
    }

    public function test_verified_journal_pages_expose_source_based_content_without_residual_copy(): void
    {
        $revista = $this->createJournal();
        RevistaMiembro::query()->create([
            'revista_id' => $revista->id,
            'grupo' => 'director_fundador',
            'grado' => 'PhD.',
            'nombre' => 'Félix Claudio Julca Guerrero',
            'afiliacion' => 'Universidad Nacional Santiago Antúnez de Mayolo',
            'pais' => 'Perú',
            'activo' => true,
        ]);

        $this->get(route('revista'))->assertOk()->assertSee('Revista Científica de Derecho y Antropología Jurídica')->assertDontSee('ISSN en línea en proceso de gestión');
        $this->get(route('revista.comite-editorial'))->assertOk()->assertSee('Félix Claudio Julca Guerrero');
        $this->get(route('revista.normas'))->assertOk()->assertSee('Política de envío y evaluación')->assertDontSee('LLALLIQ');
    }

    public function test_homepage_shows_the_verified_journal_profile_before_the_first_issue_exists(): void
    {
        $this->createJournal();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Revista científica institucional')
            ->assertSee('Información institucional')
            ->assertSee('N.° 063-2026-UNASAM-FDCCPP/D.')
            ->assertSee('Conocer la revista')
            ->assertSee('resolucion-creacion-v1.pdf')
            ->assertDontSee('Primer número pendiente')
            ->assertDontSee('La información oficial se incorporará después de validar la resolución de creación.');
    }

    public function test_editorial_subpages_are_not_public_without_a_published_journal(): void
    {
        $this->get(route('revista.comite-editorial'))->assertNotFound();
        $this->get(route('revista.normas'))->assertNotFound();
    }

    private function createJournal(): Revista
    {
        return Revista::query()->create([
            'nombre' => 'Derecho y Cultura, Revista Científica de Derecho y Antropología Jurídica',
            'nombre_corto' => 'Derecho y Cultura',
            'presentacion' => '<p>Revista científica digital especializada.</p>',
            'unidad_responsable' => 'Unidad de Investigación de la Facultad de Derecho y Ciencia Política',
            'resolucion_numero' => 'N.° 063-2026-UNASAM-FDCCPP/D.',
            'resolucion_fecha' => '2026-07-06',
            'resolucion_resumen' => 'Aprueba la creación y las normas de publicación.',
            'periodicidad' => 'Semestral',
            'modalidad' => 'Digital',
            'idiomas' => ['es', 'en'],
            'sistema_arbitraje' => 'Revisión por pares doble ciego',
            'norma_citacion' => 'APA 7.ª edición',
            'normas_publicacion' => '<h2>Política de envío y evaluación</h2><p>Correo: revista-dc@unasam.edu.pe</p>',
            'contacto_email' => 'revista-dc@unasam.edu.pe',
            'issn' => null,
            'estado_editorial' => EditorialStatus::Published->value,
        ]);
    }
}
