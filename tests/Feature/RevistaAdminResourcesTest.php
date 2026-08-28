<?php

namespace Tests\Feature;

use App\Filament\Resources\RevistaAvisos\Pages\CreateRevistaAviso;
use App\Filament\Resources\RevistaContactos\Pages\CreateRevistaContacto;
use App\Filament\Resources\RevistaDocumentos\Pages\CreateRevistaDocumento;
use App\Filament\Resources\RevistaEnvios\Pages\EditRevistaEnvio;
use App\Filament\Resources\RevistaEnvios\RevistaEnvioResource;
use App\Filament\Resources\RevistaEnvioVersiones\Pages\EditRevistaEnvioVersion;
use App\Filament\Resources\RevistaLineasInvestigacion\Pages\CreateRevistaLineaInvestigacion;
use App\Models\Revista;
use App\Models\RevistaEnvio;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RevistaAdminResourcesTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_can_create_new_editorial_resources_and_manage_a_submission(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_EDITOR]));
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Filament::bootCurrentPanel();
        $this->assertTrue(Filament::getCurrentPanel()->hasDatabaseNotifications());
        $revista = Revista::query()->create(['nombre' => 'Revista QA', 'nombre_corto' => 'RQA', 'estado_editorial' => 'draft']);

        Livewire::test(CreateRevistaDocumento::class)->fillForm(['revista_id' => $revista->id, 'categoria' => 'formato', 'titulo' => 'Plantilla QA', 'url' => '/qa.docx', 'visible' => true, 'estado_editorial' => 'published'])->call('create')->assertHasNoFormErrors();
        Livewire::test(CreateRevistaContacto::class)->fillForm(['revista_id' => $revista->id, 'tipo' => 'persona', 'nombre' => 'Contacto QA', 'email' => 'qa@unasam.edu.pe', 'visible' => true])->call('create')->assertHasNoFormErrors();
        Livewire::test(CreateRevistaLineaInvestigacion::class)->fillForm(['revista_id' => $revista->id, 'nombre' => 'Derecho QA', 'activa' => true])->call('create')->assertHasNoFormErrors();
        Livewire::test(CreateRevistaAviso::class)->fillForm(['revista_id' => $revista->id, 'titulo' => 'Aviso QA', 'slug' => 'aviso-qa', 'estado_editorial' => 'draft'])->call('create')->assertHasNoFormErrors();

        $envio = RevistaEnvio::query()->create(['revista_id' => $revista->id, 'codigo_seguimiento' => 'DYC-ABCDEFGHIJKL', 'nombres' => 'Autora QA', 'documento_identidad' => '12345678', 'afiliacion' => 'UNASAM', 'ciudad' => 'Huaraz', 'pais' => 'Perú', 'email_institucional' => 'autora@unasam.edu.pe', 'whatsapp' => '+51999999999', 'tipo_contribucion' => 'ensayo', 'titulo' => 'Ensayo QA', 'resumen' => 'Resumen', 'manuscrito_path' => 'a.docx', 'carta_path' => 'b.docx', 'declaracion_path' => 'c.docx', 'constancia_estilo_path' => 'd.pdf', 'estado' => 'recibido', 'consentimiento_at' => now()]);
        $this->assertSame('1', RevistaEnvioResource::getNavigationBadge());
        Livewire::test(EditRevistaEnvio::class, ['record' => $envio->getRouteKey()])->fillForm(['estado' => 'revision_editorial', 'observaciones_internas' => 'Revisión iniciada.'])->call('save')->assertHasNoFormErrors();
        $this->assertSame('revision_editorial', $envio->refresh()->estado);

        $version = $envio->versiones()->create(['numero' => 1, 'archivo_path' => 'correccion.docx', 'nota_autor' => 'Nota inicial', 'recibida_at' => now()]);
        Livewire::test(EditRevistaEnvioVersion::class, ['record' => $version->getRouteKey()])->fillForm(['nota_autor' => 'Nota revisada'])->call('save')->assertHasNoFormErrors();
        $this->assertSame('Nota revisada', $version->refresh()->nota_autor);
    }
}
