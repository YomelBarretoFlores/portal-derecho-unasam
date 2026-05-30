<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Support\MockData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        foreach (MockData::blogPosts() as $p) {
            BlogPost::create([
                'tipo' => $p->tipo,
                'titulo' => $p->titulo,
                'slug' => Str::slug($p->titulo),
                'extracto' => $p->extracto,
                'autor' => $p->autor,
                'tiempo_lectura' => $p->tiempo_lectura,
                'fecha' => $p->fecha,
                'publicado' => true,
            ]);
        }
    }
}
