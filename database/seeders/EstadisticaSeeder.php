<?php

namespace Database\Seeders;

use App\Models\Estadistica;
use Illuminate\Database\Seeder;

class EstadisticaSeeder extends Seeder
{
    public function run(): void
    {
        $series = [
            'matriculados' => [2020 => 766, 2021 => 835, 2022 => 906, 2023 => 480, 2024 => 1049],
            'egresados' => [2020 => 57, 2021 => 54, 2022 => 72, 2023 => 11, 2024 => 38],
            'graduados' => [2020 => 6, 2021 => 86, 2022 => 37, 2023 => 85, 2024 => 29],
            'titulados' => [2020 => 13, 2021 => 10, 2022 => 55, 2023 => 23, 2024 => 68],
        ];

        foreach ($series as $tipo => $puntos) {
            foreach ($puntos as $anio => $total) {
                Estadistica::create([
                    'tipo' => $tipo,
                    'anio' => $anio,
                    'total' => $total,
                ]);
            }
        }
    }
}
