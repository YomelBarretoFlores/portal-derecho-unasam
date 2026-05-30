<?php

namespace Database\Seeders;

use App\Models\Objetivo;
use Illuminate\Database\Seeder;

class ObjetivoSeeder extends Seeder
{
    public function run(): void
    {
        $planes = [
            ['Plan de Estudios 2023', true, [
                'Emplea pertinentemente las diversas fuentes del Derecho para emitir opiniones técnicas y absolver consultas jurídicas fundamentadas a las instituciones públicas y privadas, con responsabilidad y eficiencia.',
                'Aplica la dogmática y las fuentes jurídicas congruentemente para el patrocinio de intereses individuales y colectivos, así como la prevención y solución de conflictos ante órganos jurisdiccionales y no jurisdiccionales, con aptitud destacada y ética profesional.',
                'Aplica con coherencia y correspondencia argumentativa la dogmática jurídica para su desempeño en el sector público, con transparencia y compromiso ético.',
                'Evidencia un manejo teórico, metodológico y tecnológico destacado de la investigación jurídica para la solución de problemas en el campo dogmático, empírico e interdisciplinario, cumpliendo los parámetros exigidos por la comunidad jurídica y la ética de la investigación para su posterior publicación.',
            ]],
            ['Plan de Estudios 2019', false, [
                'Participa en la construcción, reconstrucción e innovación del sistema jurídico peruano en el contexto del derecho comparado, con rigor iusfilosófico y científico jurídico, para confrontar los ordenamientos e instituciones jurídicas existentes en el mundo.',
                'Aplica las normas jurídicas con eficacia, empleando pertinentemente la doctrina y jurisprudencia nacional y comparada, para resolver los casos de carácter jurídico que se presenten.',
                'Desarrolla criterios científicos y humanísticos en la aplicación de la ciencia jurídica, proponiendo alternativas de solución pertinentes a la problemática, para atender los casos que se presentan en el ámbito de desempeño laboral.',
            ]],
        ];

        $orden = 0;
        foreach ($planes as [$plan, $vigente, $objetivos]) {
            foreach ($objetivos as $texto) {
                Objetivo::create([
                    'plan' => $plan,
                    'vigente' => $vigente,
                    'texto' => $texto,
                    'orden' => $orden++,
                ]);
            }
        }
    }
}
