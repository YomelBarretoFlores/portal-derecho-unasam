<?php

namespace Database\Seeders;

use App\Models\AreaLaboral;
use Illuminate\Database\Seeder;

class AreaLaboralSeeder extends Seeder
{
    public function run(): void
    {
        $areas = [
            ['Ejercicio libre de la abogacía', 'Representación y defensa de personas naturales o jurídicas ante tribunales de justicia, autoridades policiales y administrativas.'],
            ['Magistratura y Ministerio Público', 'Desempeño como jueces o fiscales en el sistema judicial peruano.'],
            ['Asesoría legal', 'Consultoría jurídica para entidades públicas y privadas, incluyendo la participación en equipos multidisciplinarios.'],
            ['Docencia e investigación', 'Participación en la enseñanza universitaria y en proyectos de investigación jurídica.'],
            ['Participación política', 'Desempeño en funciones políticas, especialmente en la elaboración e interpretación de leyes conforme a principios democráticos.'],
            ['Resolución de conflictos', 'Intervención en la solución de conflictos individuales y colectivos, promoviendo la paz social y la equidad.'],
        ];

        foreach ($areas as $i => [$titulo, $descripcion]) {
            AreaLaboral::create([
                'titulo' => $titulo,
                'descripcion' => $descripcion,
                'orden' => $i,
            ]);
        }
    }
}
