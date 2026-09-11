<?php

namespace Tests\Feature;

use App\Models\Competencia;
use App\Services\CompetenciaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Todo lo que entra en la caché pública tiene que ser dato plano.
 *
 * La página de competencias guardaba una Collection anidada dentro del array
 * cacheado. Con el almacén de ficheros —el que usa el sitio— volvía como
 * __PHP_Incomplete_Class, y la vista recibía la cadena «Illuminate\Support\
 * Collection» donde esperaba un array. El efecto era difícil de ver y fácil de
 * dar por casualidad: la página respondía 200 la primera vez tras vaciar la
 * caché y 500 en todas las demás, hasta el siguiente vaciado.
 *
 * Un test que solo pidiera la página una vez habría pasado siempre.
 */
class CacheSerializableTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<int, string> */
    private function objetosEn(mixed $valor, string $ruta = ''): array
    {
        if (is_object($valor)) {
            return [$ruta.' => '.$valor::class];
        }

        if (! is_array($valor)) {
            return [];
        }

        $hallazgos = [];
        foreach ($valor as $clave => $hijo) {
            $hallazgos = [...$hallazgos, ...$this->objetosEn($hijo, $ruta.'['.$clave.']')];
        }

        return $hallazgos;
    }

    private function sembrarCompetencias(): void
    {
        // Sin filas, grupos() devuelve una colección vacía y cualquier
        // comprobación sobre su forma pasa por no tener nada que mirar.
        foreach ([['Generales', 'CG1'], ['Específicas', 'CE1']] as [$grupo, $nombre]) {
            Competencia::query()->create([
                'grupo' => $grupo, 'plan' => 'Plan 2023', 'vigente' => true,
                'nombre' => $nombre, 'texto' => 'Descripción de la competencia.', 'orden' => 1,
            ]);
        }
    }

    public function test_the_competencies_payload_is_plain_data(): void
    {
        $this->sembrarCompetencias();

        $grupos = app(CompetenciaService::class)->grupos()->all();

        $this->assertNotSame([], $grupos, 'El sembrado no produjo grupos: la comprobación sería vacía.');

        $this->assertSame([], $this->objetosEn($grupos),
            'Hay objetos dentro de lo que se cachea: al volver de la caché serializada dejarán de ser recorribles.');
    }

    public function test_the_competencies_page_survives_a_second_request(): void
    {
        // La primera petición calcula; la segunda lee de la caché. El fallo solo
        // aparecía en la segunda.
        $this->sembrarCompetencias();

        $this->get(route('competencias'))->assertOk()->assertSee('CG1');
        $this->get(route('competencias'))->assertOk()->assertSee('CG1');
    }
}
