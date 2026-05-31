<?php

namespace Database\Seeders;

use App\Models\Curso;
use Illuminate\Database\Seeder;

/**
 * Cursos de ejemplo (primeros ciclos). El usuario completará la malla real
 * desde el panel; esto solo demuestra cómo se verá la sección.
 */
class CursoSeeder extends Seeder
{
    public function run(): void
    {
        $malla = [
            1 => [
                ['Introducción a las Ciencias Jurídicas', 4, 'General'],
                ['Filosofía', 3, 'General'],
                ['Comunicación', 3, 'General'],
                ['Realidad Nacional y Regional', 3, 'General'],
            ],
            2 => [
                ['Teoría General del Derecho', 4, 'Específico'],
                ['Derecho Romano', 3, 'Específico'],
                ['Metodología del Trabajo Universitario', 3, 'General'],
                ['Economía Política', 3, 'General'],
            ],
            3 => [
                ['Derecho Constitucional General', 4, 'Específico'],
                ['Acto Jurídico', 4, 'Específico'],
                ['Derecho Penal — Parte General', 4, 'Específico'],
            ],
        ];

        foreach ($malla as $ciclo => $cursos) {
            foreach ($cursos as $i => [$nombre, $creditos, $tipo]) {
                Curso::create([
                    'ciclo' => $ciclo,
                    'nombre' => $nombre,
                    'creditos' => $creditos,
                    'tipo' => $tipo,
                    'orden' => $i,
                ]);
            }
        }
    }
}
