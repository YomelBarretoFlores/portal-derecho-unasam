<?php

namespace Tests\Feature;

use App\Models\Docente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class DocenteProfilesTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_verified_and_active_profiles_are_public(): void
    {
        $publico = Docente::query()->create($this->perfil([
            'name' => 'Docente Público',
            'slug' => 'docente-publico',
            'estado_revision' => 'verified',
            'documento_fuente' => 'ficha-oficial.docx',
            'activo' => true,
        ]));

        $pendiente = Docente::query()->create($this->perfil([
            'name' => 'Docente Pendiente',
            'slug' => 'docente-pendiente',
        ]));

        $this->get(route('docentes'))
            ->assertOk()
            ->assertSee($publico->name)
            ->assertDontSee($pendiente->name);

        $this->get(route('docentes.show', $publico))
            ->assertOk()
            ->assertSee($publico->resena)
            ->assertSee('Correo institucional');

        $this->get(route('docentes.show', $pendiente))->assertNotFound();
    }

    public function test_a_pending_profile_cannot_be_activated(): void
    {
        $this->expectException(ValidationException::class);

        Docente::query()->create($this->perfil([
            'name' => 'Perfil sin verificar',
            'slug' => 'perfil-sin-verificar',
            'activo' => true,
        ]));
    }

    public function test_a_manual_slug_is_not_replaced_when_the_name_changes(): void
    {
        $docente = Docente::query()->create($this->perfil([
            'name' => 'Nombre Inicial',
            'slug' => 'slug-editorial',
        ]));

        $docente->update(['name' => 'Nombre Corregido']);

        $this->assertSame('slug-editorial', $docente->fresh()->slug);
    }

    public function test_public_profiles_are_included_in_the_sitemap(): void
    {
        $docente = Docente::query()->create($this->perfil([
            'name' => 'Docente Indexado',
            'slug' => 'docente-indexado',
            'estado_revision' => 'verified',
            'documento_fuente' => 'ficha-oficial.docx',
            'activo' => true,
        ]));

        $this->get(route('sitemap'))
            ->assertOk()
            ->assertSee(route('docentes.show', $docente), false);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function perfil(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Docente de Prueba',
            'slug' => 'docente-de-prueba',
            'grado' => 'Doctor en Derecho',
            'categoria' => 'Docente auxiliar',
            'dedicacion' => 'Tiempo completo',
            'area' => 'Derecho',
            'resena' => 'Reseña académica documental verificada para pruebas.',
            'email_institucional' => 'docente@unasam.edu.pe',
            'estado_revision' => 'pending',
            'activo' => false,
            'orden' => 1,
        ], $overrides);
    }
}
