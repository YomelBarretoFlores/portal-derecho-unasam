<?php

namespace Database\Seeders;

use App\Models\PerfilIngresoArea;
use Illuminate\Database\Seeder;

class PerfilIngresoAreaSeeder extends Seeder
{
    public function run(): void
    {
        $cols = [
            ['Ciencias Sociales', [
                'Interpreta con pertinencia los hechos históricos a nivel regional, nacional y mundial.',
                'Gestiona responsablemente el espacio y el ambiente de su entorno.',
            ]],
            ['Comunicación', [
                'Lee y escribe con pertinencia textos de diverso tipo y complejidad.',
            ]],
            ['Matemática', [
                'Resuelve problemas contextualizados de cantidad con orden y precisión.',
                'Resuelve problemas contextualizados de gestión de datos e incertidumbre.',
            ]],
            ['Ciencia y Tecnología', [
                'Explica el mundo físico basándose en conocimientos sobre los seres vivos, la materia, la energía y la biodiversidad.',
            ]],
        ];

        foreach ($cols as $i => [$titulo, $items]) {
            PerfilIngresoArea::create([
                'titulo' => $titulo,
                'items' => $items,
                'orden' => $i,
            ]);
        }
    }
}
