<?php

namespace Database\Seeders;

use App\Models\Articulo;
use App\Support\MockData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticuloSeeder extends Seeder
{
    public function run(): void
    {
        foreach (MockData::revistaArticulos() as $a) {
            Articulo::create([
                'titulo' => $a->titulo,
                'slug' => Str::slug($a->titulo),
                'autores' => $a->autores,
                'paginas' => $a->paginas,
                'categoria' => $a->categoria,
                'resumen' => $a->resumen,
                'fecha' => $a->fecha,
                'doi' => $a->doi,
                'descargas' => $a->descargas,
                'publicado' => true,
            ]);
        }
    }
}
