<?php

namespace Tests\Feature;

use App\Models\Revista;
use App\Models\RevistaEnvio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * El diagrama público y los estados internos tienen que contar lo mismo.
 *
 * La tira del flujo editorial salía de una lista escrita a mano. Anunciaba
 * «Respuesta por correo», que no es una fase sino algo que ocurre entre fases,
 * y se dejaba fuera «Revisión editorial», que sí lo es: un autor en esa fase
 * consultaba su envío, leía ese nombre, y no lo encontraba en el diagrama.
 */
class FlujoEditorialCoherenteTest extends TestCase
{
    use RefreshDatabase;

    private function revistaPublicada(): void
    {
        Revista::query()->create([
            'nombre' => 'Derecho y Cultura', 'nombre_corto' => 'Derecho y Cultura',
            'presentacion' => '<p>Presentación.</p>', 'unidad_responsable' => 'Unidad de Investigación',
            'resolucion_numero' => '063-2026', 'resolucion_fecha' => '2026-07-06',
            'resolucion_resumen' => 'Creación aprobada.', 'periodicidad' => 'Semestral',
            'contacto_email' => 'revista@unasam.edu.pe', 'normas_publicacion' => '<p>Normas oficiales.</p>',
            'contenido_politicas' => '<p>Recibe trabajos originales sometidos a revisión por pares doble ciego según las normas para autores.</p>',
            'contenido_indexacion' => '<p>Revista científica digital semestral de la Universidad Nacional Santiago Antúnez de Mayolo.</p>',
            'contenido_privacidad' => '<p>Protege la información de autores y colaboradores durante la gestión editorial de cada manuscrito recibido.</p>',
            'estado_editorial' => 'published',
        ]);
    }

    public function test_every_internal_state_belongs_to_exactly_one_public_phase(): void
    {
        $cubiertos = array_merge(...array_values(RevistaEnvio::FASES_PUBLICAS));

        $this->assertSame(
            [],
            array_diff(array_keys(RevistaEnvio::ESTADOS), $cubiertos),
            'Hay estados que el autor puede ver y que no aparecen en el diagrama público.',
        );

        $this->assertSame(
            [],
            array_diff($cubiertos, array_keys(RevistaEnvio::ESTADOS)),
            'El diagrama público anuncia fases que no corresponden a ningún estado real.',
        );

        $this->assertSame(count($cubiertos), count(array_unique($cubiertos)),
            'Un estado está repetido en dos fases: el autor no sabría en cuál está.');
    }

    public function test_the_public_page_draws_the_real_phases(): void
    {
        $this->revistaPublicada();

        $html = $this->get(route('revista.envios'))->assertOk()->getContent();

        foreach (array_keys(RevistaEnvio::FASES_PUBLICAS) as $fase) {
            $this->assertStringContainsString($fase, $html, "Falta la fase «{$fase}» en el diagrama.");
        }

        $this->assertStringNotContainsString('Respuesta por correo', $html,
            'Vuelve a anunciarse como fase algo que no lo es.');
    }

    public function test_the_page_does_not_promise_automatic_notifications(): void
    {
        // El portal no manda ningún correo al autor: la única notificación que
        // existe va al panel de los editores. Prometer avisos automáticos
        // dejaría a un autor esperando un mensaje que nunca llega.
        $this->revistaPublicada();

        $html = $this->get(route('revista.envios'))->assertOk()->getContent();

        $this->assertStringContainsString('no envía avisos automáticos', $html);
    }
}
