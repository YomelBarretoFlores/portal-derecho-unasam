<?php

namespace Tests\Feature;

use App\Models\Articulo;
use App\Models\BlogPost;
use App\Models\Comunicado;
use App\Models\Curso;
use App\Models\Docente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class DemoContentPurgeTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_is_a_dry_run_by_default_and_purges_only_known_demo_content_with_force(): void
    {
        $demoBlog = BlogPost::query()->create([
            'tipo' => 'noticia',
            'titulo' => 'Demo',
            'slug' => 'estudiantes-de-derecho-unasam-destacan-en-concurso-nacional-de-litigacion-oral',
            'publicado' => false,
        ]);
        $demoArticle = Articulo::query()->create([
            'titulo' => 'Demo', 'slug' => 'demo-articulo', 'doi' => '10.37249/rjunasam.v1i1.001', 'publicado' => false,
        ]);
        $demoTeacher = Docente::query()->create([
            'name' => 'Dr. Carlos Javier Rojas Meza', 'estado_revision' => 'pending', 'activo' => false,
        ]);
        $officialTeacher = Docente::query()->create([
            'name' => 'Fabel Bernabé Robles Espinoza', 'documento_fuente' => 'Fabel.docx', 'estado_revision' => 'pending', 'activo' => false,
        ]);
        $demoNotice = Comunicado::query()->create([
            'titulo' => 'Demo', 'slug' => 'cronograma-de-matricula-2026-i', 'contenido' => 'Demo', 'publicado' => false,
        ]);
        $welcome = Comunicado::query()->create([
            'titulo' => 'Bienvenida', 'slug' => 'bienvenida-nuevo-portal', 'contenido' => 'Contenido oficial',
            'fecha_publicacion' => now(), 'publicado' => true,
        ]);
        $demoCourse = Curso::query()->create([
            'plan' => '2023', 'ciclo' => 1, 'nombre' => 'Filosofía', 'orden' => 0, 'publicado' => false,
        ]);
        $manifest = storage_path('framework/testing/demo-purge.json');

        try {
            $this->artisan('content:purge-demo', ['--manifest' => $manifest])
                ->expectsOutputToContain('Total identificado: 5')
                ->assertSuccessful();
            $this->assertDatabaseHas('blog_posts', ['id' => $demoBlog->id]);

            $this->artisan('content:purge-demo', ['--force' => true, '--manifest' => $manifest])
                ->expectsOutputToContain('Se eliminaron 5 registros')
                ->assertSuccessful();

            foreach ([$demoBlog, $demoArticle, $demoTeacher, $demoNotice, $demoCourse] as $record) {
                $this->assertDatabaseMissing($record->getTable(), ['id' => $record->id]);
            }
            $this->assertDatabaseHas('docentes', ['id' => $officialTeacher->id]);
            $this->assertDatabaseHas('comunicados', ['id' => $welcome->id]);

            $this->artisan('content:purge-demo', ['--force' => true, '--manifest' => $manifest])
                ->expectsOutputToContain('Se eliminaron 0 registros')
                ->assertSuccessful();
        } finally {
            File::delete($manifest);
        }
    }
}
