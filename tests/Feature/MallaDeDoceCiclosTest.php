<?php

namespace Tests\Feature;

use App\Filament\Resources\Cursos\Schemas\CursoForm;
use App\Models\Curso;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * La malla de Derecho llega al ciclo XII, no al X.
 *
 * El panel solo ofrecía diez ciclos y la etiqueta en romanos se detenía en X,
 * así que los dos últimos ciclos de la carrera no se podían registrar: el
 * desplegable no los listaba. Nadie lo notó porque la tabla de cursos estaba
 * vacía —el único seeder que existió traía tres ciclos inventados de
 * demostración, y se purgó a propósito—, de modo que el tope nunca se tocó.
 *
 * Se comprueban los dos lados a la vez. Cuando discrepaban, el formulario
 * aceptaba menos ciclos de los que el modelo sabía nombrar, y eso es
 * exactamente lo que dejó la malla sin sus dos últimos ciclos.
 */
class MallaDeDoceCiclosTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_panel_offers_every_cycle_the_programme_has(): void
    {
        $opciones = $this->opcionesDeCiclo();

        $this->assertSame(
            range(1, Curso::CICLOS),
            array_keys($opciones),
            'El desplegable del panel no ofrece los mismos ciclos que declara el modelo.',
        );
    }

    public function test_the_last_two_cycles_get_a_roman_label_and_not_a_bare_number(): void
    {
        foreach ([11 => 'XI', 12 => 'XII'] as $ciclo => $esperado) {
            $curso = Curso::query()->create([
                'plan' => '2023',
                'ciclo' => $ciclo,
                'nombre' => 'Curso de prueba del ciclo '.$ciclo,
                'creditos' => 3,
                'tipo' => 'Específico',
            ]);

            $this->assertSame(
                $esperado,
                $curso->ciclo_romano,
                "El ciclo {$ciclo} cae al número árabe en medio de una lista de romanos.",
            );
        }
    }

    public function test_the_roman_label_covers_the_whole_range_without_gaps(): void
    {
        foreach (range(1, Curso::CICLOS) as $ciclo) {
            $curso = new Curso(['ciclo' => $ciclo]);

            $this->assertNotSame(
                (string) $ciclo,
                $curso->ciclo_romano,
                "El ciclo {$ciclo} no tiene etiqueta en romanos y muestra el número tal cual.",
            );
        }
    }

    /** @return array<int, mixed> */
    private function opcionesDeCiclo(): array
    {
        $schema = CursoForm::configure(
            Schema::make(new CreateRecord),
        );

        foreach ($schema->getComponents() as $componente) {
            if ($componente instanceof Select && $componente->getName() === 'ciclo') {
                return $componente->getOptions();
            }
        }

        $this->fail('El formulario de cursos ya no tiene un desplegable de ciclo.');
    }
}
