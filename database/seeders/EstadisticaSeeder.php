<?php

namespace Database\Seeders;

use App\Models\Estadistica;
use App\Support\MockData;
use Illuminate\Database\Seeder;

class EstadisticaSeeder extends Seeder
{
    public function run(): void
    {
        foreach (array_keys(MockData::tiposEstadistica()) as $tipo) {
            foreach (MockData::estadisticas($tipo) as $punto) {
                Estadistica::create([
                    'tipo' => $tipo,
                    'anio' => $punto->anio,
                    'total' => $punto->total,
                ]);
            }
        }
    }
}
