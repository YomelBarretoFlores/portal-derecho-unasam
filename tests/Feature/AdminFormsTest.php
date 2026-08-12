<?php

namespace Tests\Feature;

use App\Filament\Pages\AjustesSitio;
use App\Filament\Resources\Accesos\Pages\CreateAcceso;
use App\Filament\Resources\Accesos\Pages\EditAcceso;
use App\Filament\Resources\AreasLaborales\Pages\CreateAreaLaboral;
use App\Filament\Resources\AreasLaborales\Pages\EditAreaLaboral;
use App\Filament\Resources\Articulos\Pages\CreateArticulo;
use App\Filament\Resources\Articulos\Pages\EditArticulo;
use App\Filament\Resources\BlogPosts\Pages\CreateBlogPost;
use App\Filament\Resources\BlogPosts\Pages\EditBlogPost;
use App\Filament\Resources\Competencias\Pages\CreateCompetencia;
use App\Filament\Resources\Competencias\Pages\EditCompetencia;
use App\Filament\Resources\Comunicados\Pages\CreateComunicado;
use App\Filament\Resources\Comunicados\Pages\EditComunicado;
use App\Filament\Resources\ContentAudits\ContentAuditResource;
use App\Filament\Resources\ContentAudits\Pages\ListContentAudits;
use App\Filament\Resources\Cursos\Pages\CreateCurso;
use App\Filament\Resources\Cursos\Pages\EditCurso;
use App\Filament\Resources\Docentes\Pages\CreateDocente;
use App\Filament\Resources\Docentes\Pages\EditDocente;
use App\Filament\Resources\Documentos\Pages\CreateDocumento;
use App\Filament\Resources\Documentos\Pages\EditDocumento;
use App\Filament\Resources\Estadisticas\Pages\CreateEstadistica;
use App\Filament\Resources\Estadisticas\Pages\EditEstadistica;
use App\Filament\Resources\Hitos\Pages\CreateHito;
use App\Filament\Resources\Hitos\Pages\EditHito;
use App\Filament\Resources\Objetivos\Pages\CreateObjetivo;
use App\Filament\Resources\Objetivos\Pages\EditObjetivo;
use App\Filament\Resources\Organigramas\Pages\EditOrganigrama;
use App\Filament\Resources\PerfilIngresoAreas\Pages\CreatePerfilIngresoArea;
use App\Filament\Resources\PerfilIngresoAreas\Pages\EditPerfilIngresoArea;
use App\Filament\Resources\RevistaMiembros\Pages\CreateRevistaMiembro;
use App\Filament\Resources\RevistaMiembros\Pages\EditRevistaMiembro;
use App\Filament\Resources\RevistaNumeros\Pages\CreateRevistaNumero;
use App\Filament\Resources\RevistaNumeros\Pages\EditRevistaNumero;
use App\Filament\Resources\Revistas\Pages\CreateRevista;
use App\Filament\Resources\Revistas\Pages\EditRevista;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Models\Acceso;
use App\Models\AreaLaboral;
use App\Models\Articulo;
use App\Models\BlogPost;
use App\Models\Competencia;
use App\Models\Comunicado;
use App\Models\Curso;
use App\Models\Docente;
use App\Models\Documento;
use App\Models\Estadistica;
use App\Models\Hito;
use App\Models\Objetivo;
use App\Models\Organigrama;
use App\Models\PerfilIngresoArea;
use App\Models\Revista;
use App\Models\RevistaMiembro;
use App\Models\RevistaNumero;
use App\Models\Setting;
use App\Models\User;
use Filament\Auth\Pages\EditProfile as FilamentEditProfile;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class AdminFormsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $this->actingAs($this->admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Filament::bootCurrentPanel();
    }

    public function test_institutional_content_forms_create_and_edit_records(): void
    {
        $this->assertCreateAndEdit(
            CreateAcceso::class,
            EditAcceso::class,
            Acceso::class,
            ['titulo' => 'Acceso QA', 'descripcion' => 'Descripción QA', 'url' => '/qa', 'orden' => 91, 'activo' => true],
            ['titulo' => 'Acceso QA editado', 'url' => 'https://www.unasam.edu.pe'],
            ['titulo' => 'Acceso QA editado', 'url' => 'https://www.unasam.edu.pe'],
        );

        $this->assertCreateAndEdit(
            CreateAreaLaboral::class,
            EditAreaLaboral::class,
            AreaLaboral::class,
            ['titulo' => 'Área laboral QA', 'descripcion' => 'Descripción del área QA', 'orden' => 92],
            ['titulo' => 'Área laboral QA editada'],
            ['titulo' => 'Área laboral QA editada'],
        );

        $this->assertCreateAndEdit(
            CreateCompetencia::class,
            EditCompetencia::class,
            Competencia::class,
            ['grupo' => 'Generales', 'plan' => 'Plan QA 2099', 'vigente' => false, 'texto' => 'Competencia QA', 'orden' => 93],
            ['texto' => 'Competencia QA editada', 'vigente' => true],
            ['texto' => 'Competencia QA editada', 'vigente' => true],
        );

        $this->assertCreateAndEdit(
            CreateDocumento::class,
            EditDocumento::class,
            Documento::class,
            ['titulo' => 'Documento QA', 'categoria' => 'Resoluciones', 'fecha' => '2099-01-02', 'url' => '/documentos/qa.pdf', 'orden' => 94],
            ['titulo' => 'Documento QA editado', 'url' => 'https://www.unasam.edu.pe/documentos'],
            ['titulo' => 'Documento QA editado', 'url' => 'https://www.unasam.edu.pe/documentos'],
        );

        $this->assertCreateAndEdit(
            CreateEstadistica::class,
            EditEstadistica::class,
            Estadistica::class,
            ['tipo' => 'matriculados', 'anio' => 2099, 'total' => 123],
            ['total' => 456],
            ['total' => 456],
        );

        $this->assertCreateAndEdit(
            CreateHito::class,
            EditHito::class,
            Hito::class,
            ['anio' => 2099, 'titulo' => 'Hito QA', 'descripcion' => 'Descripción del hito QA', 'orden' => 95],
            ['titulo' => 'Hito QA editado'],
            ['titulo' => 'Hito QA editado'],
        );

        $this->assertCreateAndEdit(
            CreateObjetivo::class,
            EditObjetivo::class,
            Objetivo::class,
            ['plan' => 'Plan QA 2099', 'vigente' => false, 'texto' => 'Objetivo QA', 'orden' => 96],
            ['texto' => 'Objetivo QA editado', 'vigente' => true],
            ['texto' => 'Objetivo QA editado', 'vigente' => true],
        );

        $perfil = $this->assertCreateAndEdit(
            CreatePerfilIngresoArea::class,
            EditPerfilIngresoArea::class,
            PerfilIngresoArea::class,
            ['titulo' => 'Área de ingreso QA', 'items' => ['Capacidad QA', 'Actitud QA'], 'orden' => 97],
            ['titulo' => 'Área de ingreso QA editada', 'items' => ['Capacidad editada']],
            ['titulo' => 'Área de ingreso QA editada'],
        );

        $this->assertSame(['Capacidad editada'], $perfil->items);

        $organigrama = Organigrama::singleton();
        Livewire::test(EditOrganigrama::class, ['record' => $organigrama->getRouteKey()])
            ->fillForm(['titulo' => 'Organigrama QA', 'descripcion' => 'Descripción QA'])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $this->assertDatabaseHas(Organigrama::class, [
            'id' => $organigrama->id,
            'titulo' => 'Organigrama QA',
            'descripcion' => 'Descripción QA',
        ]);
    }

    public function test_editorial_and_academic_forms_create_and_edit_records(): void
    {
        $this->assertCreateAndEdit(
            CreateBlogPost::class,
            EditBlogPost::class,
            BlogPost::class,
            [
                'tipo' => 'noticia', 'titulo' => 'Entrada QA', 'slug' => 'entrada-qa',
                'extracto' => 'Extracto QA', 'contenido' => '<p>Contenido QA</p>',
                'autor' => 'Equipo QA', 'tiempo_lectura' => '2 min', 'fecha' => '2099-02-01',
                'estado_editorial' => 'draft',
            ],
            ['titulo' => 'Entrada QA editada', 'slug' => 'entrada-qa-editada'],
            ['titulo' => 'Entrada QA editada', 'slug' => 'entrada-qa-editada'],
        );

        $this->assertCreateAndEdit(
            CreateComunicado::class,
            EditComunicado::class,
            Comunicado::class,
            [
                'titulo' => 'Comunicado QA', 'slug' => 'comunicado-qa',
                'resumen' => 'Resumen QA', 'contenido' => '<p>Contenido QA</p>',
                'fecha_publicacion' => '2099-02-02 10:00:00', 'estado_editorial' => 'draft',
            ],
            ['titulo' => 'Comunicado QA editado', 'slug' => 'comunicado-qa-editado'],
            ['titulo' => 'Comunicado QA editado', 'slug' => 'comunicado-qa-editado'],
        );

        $this->assertCreateAndEdit(
            CreateCurso::class,
            EditCurso::class,
            Curso::class,
            [
                'plan' => '2023', 'ciclo' => 10, 'nombre' => 'Curso QA',
                'creditos' => 3, 'tipo' => 'Electivo', 'orden' => 98,
                'estado_editorial' => 'draft',
            ],
            ['nombre' => 'Curso QA editado', 'creditos' => 4],
            ['nombre' => 'Curso QA editado', 'creditos' => 4],
        );

        $this->assertCreateAndEdit(
            CreateDocente::class,
            EditDocente::class,
            Docente::class,
            [
                'name' => 'Docente QA', 'slug' => 'docente-qa', 'grado' => 'Doctor en QA',
                'categoria' => 'Docente QA', 'dedicacion' => 'Tiempo completo',
                'area' => 'Área QA', 'resena' => 'Reseña académica QA',
                'email_institucional' => 'docente.qa@unasam.edu.pe',
                'orcid' => 'https://orcid.org/0000-0000-0000-0001',
                'publicaciones' => [['titulo' => 'Publicación QA', 'anio' => 2026, 'url' => 'https://example.com/publicacion']],
                'orden' => 99, 'estado_editorial' => 'draft',
            ],
            ['name' => 'Docente QA editado', 'slug' => 'docente-qa-editado'],
            ['name' => 'Docente QA editado', 'slug' => 'docente-qa-editado'],
        );
    }

    public function test_magazine_forms_create_and_edit_the_complete_relationship_chain(): void
    {
        $revista = $this->assertCreateAndEdit(
            CreateRevista::class,
            EditRevista::class,
            Revista::class,
            [
                'nombre' => 'Revista QA', 'nombre_corto' => 'RQA',
                'presentacion' => '<p>Presentación QA</p>', 'enfoque_alcance' => '<p>Alcance QA</p>',
                'unidad_responsable' => 'Unidad QA', 'contacto_email' => 'revista.qa@unasam.edu.pe',
                'periodicidad' => 'Anual', 'modalidad' => 'Digital', 'idiomas' => ['es'],
                'tipos_contribucion' => ['articulo_original'], 'sistema_arbitraje' => 'Doble ciego',
                'norma_citacion' => 'APA 7', 'normas_publicacion' => '<p>Normas QA</p>',
                'resolucion_numero' => 'QA-001', 'resolucion_fecha' => '2099-03-01',
                'resolucion_resumen' => 'Resumen de resolución QA', 'estado_editorial' => 'draft',
            ],
            ['nombre_corto' => 'RQA Editada'],
            ['nombre_corto' => 'RQA Editada'],
        );

        $this->assertCreateAndEdit(
            CreateRevistaMiembro::class,
            EditRevistaMiembro::class,
            RevistaMiembro::class,
            [
                'revista_id' => $revista->id, 'grupo' => 'editores', 'grado' => 'Dra.',
                'nombre' => 'Editora QA', 'afiliacion' => 'UNASAM', 'pais' => 'Perú',
                'orcid' => '0000-0000-0000-0002', 'email' => 'editora.qa@unasam.edu.pe',
                'orden' => 1, 'activo' => true,
            ],
            ['nombre' => 'Editora QA actualizada'],
            ['nombre' => 'Editora QA actualizada'],
        );

        $numero = $this->assertCreateAndEdit(
            CreateRevistaNumero::class,
            EditRevistaNumero::class,
            RevistaNumero::class,
            [
                'revista_id' => $revista->id, 'volumen' => '1', 'numero' => '1',
                'titulo' => 'Número QA', 'slug' => 'numero-qa', 'subtitulo' => 'Subtítulo QA',
                'descripcion' => 'Descripción QA', 'fecha_publicacion' => '2099-03-02',
                'orden' => 1, 'estado_editorial' => 'draft',
            ],
            ['titulo' => 'Número QA editado', 'slug' => 'numero-qa-editado'],
            ['titulo' => 'Número QA editado', 'slug' => 'numero-qa-editado'],
        );

        $this->assertCreateAndEdit(
            CreateArticulo::class,
            EditArticulo::class,
            Articulo::class,
            [
                'revista_numero_id' => $numero->id, 'titulo' => 'Artículo QA', 'slug' => 'articulo-qa',
                'autores' => ['Autora QA'], 'categoria' => 'Derecho Constitucional',
                'paginas' => '1-10', 'doi' => '10.0000/qa', 'descargas' => 0,
                'resumen' => 'Resumen QA', 'contenido' => '<p>Artículo QA</p>',
                'fecha' => '2099-03-03', 'estado_editorial' => 'draft',
            ],
            ['titulo' => 'Artículo QA editado', 'slug' => 'articulo-qa-editado'],
            ['titulo' => 'Artículo QA editado', 'slug' => 'articulo-qa-editado'],
        );
    }

    public function test_user_and_site_settings_forms_persist_changes(): void
    {
        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Editor QA',
                'email' => 'editor.qa@unasam.edu.pe',
                'password' => 'Clave-Segura-QA-2026!',
                'role' => User::ROLE_EDITOR,
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $editor = User::query()->where('email', 'editor.qa@unasam.edu.pe')->firstOrFail();
        $this->assertTrue(Hash::check('Clave-Segura-QA-2026!', $editor->password));

        Livewire::test(EditUser::class, ['record' => $editor->getRouteKey()])
            ->fillForm(['name' => 'Editor QA actualizado', 'password' => null, 'role' => User::ROLE_EDITOR])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $editor->refresh();
        $this->assertSame('Editor QA actualizado', $editor->name);
        $this->assertTrue(Hash::check('Clave-Segura-QA-2026!', $editor->password));

        Livewire::test(AjustesSitio::class)
            ->fillForm([
                'footer_marca' => 'Marca QA',
                'footer_cta_url' => '/qa',
                'plan_pdf_url' => '/documentos/plan-qa.pdf',
                'plan_sga_url' => 'https://www.unasam.edu.pe',
                'seo_title' => 'SEO QA',
            ])
            ->call('guardar')
            ->assertHasNoFormErrors()
            ->assertNotified('Ajustes guardados');

        $this->assertSame('Marca QA', Setting::get('footer_marca'));
        $this->assertSame('/qa', Setting::get('footer_cta_url'));
        $this->assertSame('SEO QA', Setting::get('seo_title'));

        Livewire::test(FilamentEditProfile::class)
            ->fillForm([
                'name' => 'Administrador QA actualizado',
                'email' => $this->admin->email,
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $this->assertSame('Administrador QA actualizado', $this->admin->refresh()->name);
    }

    public function test_all_create_forms_enforce_their_required_fields(): void
    {
        $cases = [
            [CreateAcceso::class, ['titulo', 'descripcion', 'url']],
            [CreateAreaLaboral::class, ['titulo', 'descripcion']],
            [CreateArticulo::class, ['titulo', 'slug']],
            [CreateBlogPost::class, ['titulo', 'slug']],
            [CreateCompetencia::class, ['grupo', 'plan', 'texto']],
            [CreateComunicado::class, ['titulo', 'slug']],
            [CreateCurso::class, ['ciclo', 'nombre']],
            [CreateDocente::class, ['name', 'slug']],
            [CreateDocumento::class, ['titulo', 'categoria']],
            [CreateEstadistica::class, ['tipo', 'anio']],
            [CreateHito::class, ['anio', 'titulo', 'descripcion']],
            [CreateObjetivo::class, ['plan', 'texto']],
            [CreatePerfilIngresoArea::class, ['titulo']],
            [CreateRevistaMiembro::class, ['revista_id', 'grupo', 'nombre']],
            [CreateRevistaNumero::class, ['revista_id', 'volumen', 'numero', 'titulo', 'slug']],
            [CreateRevista::class, ['nombre', 'nombre_corto']],
            [CreateUser::class, ['name', 'email', 'password']],
        ];

        foreach ($cases as [$page, $requiredFields]) {
            Livewire::test($page)
                ->call('create')
                ->assertHasFormErrors($requiredFields);
        }
    }

    public function test_content_audit_is_an_authorized_read_only_resource(): void
    {
        $this->assertSame(['index'], array_keys(ContentAuditResource::getPages()));

        Livewire::test(ListContentAudits::class)
            ->assertOk();
    }

    public function test_all_media_upload_fields_store_their_files(): void
    {
        config()->set('media.uploads_enabled', true);
        Storage::fake('public');

        Livewire::test(CreateBlogPost::class)
            ->fillForm([
                'titulo' => 'Blog con imagen QA', 'slug' => 'blog-imagen-qa',
                'imagen' => UploadedFile::fake()->image('blog-qa.jpg'),
            ])
            ->call('create')
            ->assertHasNoFormErrors();
        $this->assertTrue(BlogPost::query()->latest('id')->firstOrFail()->hasMedia('imagen'));

        Livewire::test(CreateComunicado::class)
            ->fillForm([
                'titulo' => 'Comunicado con imagen QA', 'slug' => 'comunicado-imagen-qa',
                'imagen' => UploadedFile::fake()->image('comunicado-qa.jpg'),
            ])
            ->call('create')
            ->assertHasNoFormErrors();
        $this->assertTrue(Comunicado::query()->latest('id')->firstOrFail()->hasMedia('imagen'));

        Livewire::test(CreateDocente::class)
            ->fillForm([
                'name' => 'Docente con foto QA', 'slug' => 'docente-foto-qa',
                'foto' => UploadedFile::fake()->image('docente-qa.jpg'),
            ])
            ->call('create')
            ->assertHasNoFormErrors();
        $this->assertTrue(Docente::query()->latest('id')->firstOrFail()->hasMedia('foto'));

        Livewire::test(CreateDocumento::class)
            ->fillForm([
                'titulo' => 'Documento con PDF QA', 'categoria' => 'Resoluciones',
                'archivo' => $this->fakePdf('documento-qa.pdf'),
            ])
            ->call('create')
            ->assertHasNoFormErrors();
        $this->assertTrue(Documento::query()->latest('id')->firstOrFail()->hasMedia('archivo'));

        $organigrama = Organigrama::singleton();
        Livewire::test(EditOrganigrama::class, ['record' => $organigrama->getRouteKey()])
            ->fillForm(['imagen' => UploadedFile::fake()->image('organigrama-qa.jpg')])
            ->call('save')
            ->assertHasNoFormErrors();
        $this->assertTrue($organigrama->refresh()->hasMedia('imagen'));

        Livewire::test(CreateRevista::class)
            ->fillForm([
                'nombre' => 'Revista con medios QA', 'nombre_corto' => 'RMQA',
                'resolucion' => $this->fakePdf('resolucion-qa.pdf'),
                'logo' => UploadedFile::fake()->image('revista-qa.png'),
            ])
            ->call('create')
            ->assertHasNoFormErrors();
        $revista = Revista::query()->latest('id')->firstOrFail();
        $this->assertTrue($revista->hasMedia('resolucion'));
        $this->assertTrue($revista->hasMedia('logo'));

        Livewire::test(CreateRevistaNumero::class)
            ->fillForm([
                'revista_id' => $revista->id, 'volumen' => '1', 'numero' => '1',
                'titulo' => 'Número con medios QA', 'slug' => 'numero-medios-qa',
                'portada' => UploadedFile::fake()->image('portada-qa.jpg'),
                'numero_pdf' => $this->fakePdf('numero-qa.pdf'),
            ])
            ->call('create')
            ->assertHasNoFormErrors();
        $numero = RevistaNumero::query()->latest('id')->firstOrFail();
        $this->assertTrue($numero->hasMedia('portada'));
        $this->assertTrue($numero->hasMedia('numero_pdf'));

        Livewire::test(CreateArticulo::class)
            ->fillForm([
                'revista_numero_id' => $numero->id, 'titulo' => 'Artículo con PDF QA',
                'slug' => 'articulo-pdf-qa',
                'pdf' => $this->fakePdf('articulo-qa.pdf'),
            ])
            ->call('create')
            ->assertHasNoFormErrors();
        $this->assertTrue(Articulo::query()->latest('id')->firstOrFail()->hasMedia('pdf'));
    }

    /**
     * @param  class-string  $createPage
     * @param  class-string  $editPage
     * @param  class-string<Model>  $modelClass
     * @param  array<string, mixed>  $createData
     * @param  array<string, mixed>  $editData
     * @param  array<string, mixed>  $expectedData
     */
    private function assertCreateAndEdit(
        string $createPage,
        string $editPage,
        string $modelClass,
        array $createData,
        array $editData,
        array $expectedData,
    ): Model {
        Livewire::test($createPage)
            ->fillForm($createData)
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $record = $modelClass::query()->latest('id')->firstOrFail();

        Livewire::test($editPage, ['record' => $record->getRouteKey()])
            ->fillForm($editData)
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $this->assertDatabaseHas($modelClass, ['id' => $record->getKey(), ...$expectedData]);

        return $record->refresh();
    }

    private function fakePdf(string $name): UploadedFile
    {
        return UploadedFile::fake()->createWithContent(
            $name,
            "%PDF-1.4\n1 0 obj<</Type/Catalog>>endobj\ntrailer<</Root 1 0 R>>\n%%EOF",
        );
    }
}
