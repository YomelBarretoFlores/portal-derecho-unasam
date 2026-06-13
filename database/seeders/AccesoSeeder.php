<?php

namespace Database\Seeders;

use App\Models\Acceso;
use Illuminate\Database\Seeder;

class AccesoSeeder extends Seeder
{
    public function run(): void
    {
        $accesos = [
            ['Plan de Estudios', 'Malla curricular 2023 y cursos por ciclo.', route('plan-2023', [], false)],
            ['Perfil de Egreso', 'Competencias del abogado que formamos.', route('perfil-egreso', [], false)],
            ['Competencias', 'Generales y específicas del programa.', route('competencias', [], false)],
            ['Personal Docente', 'Plana docente por especialidad.', route('docentes', [], false)],
        ];

        foreach ($accesos as $i => [$titulo, $descripcion, $url]) {
            Acceso::create([
                'titulo' => $titulo,
                'descripcion' => $descripcion,
                'url' => $url,
                'orden' => $i + 1,
                'activo' => true,
            ]);
        }
    }
}
