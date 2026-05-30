<?php

namespace Database\Seeders;

use App\Models\Docente;
use App\Support\MockData;
use Illuminate\Database\Seeder;

class DocenteSeeder extends Seeder
{
    public function run(): void
    {
        foreach (MockData::docentes()->values() as $i => $d) {
            Docente::create([
                'name' => $d->name,
                'area' => $d->area,
                'grado' => $d->grado,
                'orden' => $i,
                'activo' => true,
            ]);
        }
    }
}
