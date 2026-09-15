<?php

namespace Tests\Feature;

use App\Models\Curso;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * El panel no debe sugerir una duración que contradiga a la malla.
 *
 * El ejemplo del campo «Datos del programa» decía «5 años (10 ciclos)», y el
 * servidor de la universidad tenía registrado exactamente eso. Las dos mallas
 * oficiales —la de 2019 y la de 2023— llegan al ciclo XII: son seis años.
 *
 * El texto de ayuda de un formulario se copia. Quien rellenara ese campo en una
 * instalación nueva tenía delante el número equivocado, así que el error se
 * habría repetido. Por eso se comprueba aquí y no solo se corrige el dato.
 */
class DuracionDeLaCarreraTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_hint_does_not_teach_a_duration_the_curriculum_contradicts(): void
    {
        $fuente = file_get_contents(app_path('Filament/Pages/AjustesSitio.php'));

        $this->assertStringNotContainsString(
            '10 ciclos',
            $fuente,
            'El ejemplo del panel sigue sugiriendo diez ciclos; la malla tiene doce.',
        );

        $this->assertStringContainsString(
            Curso::CICLOS.' ciclos',
            $fuente,
            'El ejemplo del panel debería usar el mismo número de ciclos que declara el modelo.',
        );
    }
}
