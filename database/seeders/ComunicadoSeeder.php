<?php

namespace Database\Seeders;

use App\Models\Comunicado;
use App\Support\MockData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ComunicadoSeeder extends Seeder
{
    public function run(): void
    {
        foreach (MockData::comunicados() as $c) {
            Comunicado::create([
                'titulo' => $c->titulo,
                'slug' => Str::slug($c->titulo),
                'resumen' => $c->resumen,
                'contenido' => $c->resumen,
                'publicado' => true,
                'fecha_publicacion' => $c->fecha,
            ]);
        }
    }
}
