<?php

namespace Tests\Feature;

use App\Filament\Pages\AjustesSitio;
use App\Models\Acceso;
use App\Models\Competencia;
use App\Models\Hito;
use App\Models\Objetivo;
use App\Models\Revista;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\ContenidoInstitucionalSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Un despliegue desde cero tiene que quedar utilizable.
 *
 * Esto reproduce lo que le pasó al área de TI: clonaron el repositorio, el
 * despliegue terminó sin errores, el sitio respondió, y no mostraba nada. No
 * fue un fallo suyo. El arranque ejecutaba las migraciones y nada más, así
 * que las tablas quedaban creadas y vacías, y encima no existía ninguna
 * cuenta con la que entrar al panel a cargar el contenido a mano.
 *
 * Desde fuera parecía un problema de base de datos. La base de datos estaba
 * perfectamente; lo que faltaba era la siembra inicial.
 */
class InstalacionLimpiaTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_database_with_only_migrations_leaves_the_site_empty(): void
    {
        // El punto de partida del incidente, escrito para que quede claro que
        // no basta con migrar.
        $this->assertSame(0, Hito::count());
        $this->assertSame(0, Objetivo::count());
        $this->assertSame(0, Competencia::count());
        $this->assertSame(0, Acceso::count());

        // Los textos del sitio tampoco existen. No se mira el total de la tabla
        // «settings»: la migración 000015 deja ahí la ruta de la mascota, que
        // es una referencia a un archivo de la aplicación, no contenido
        // redactado. Lo que importa es que la prosa no está.
        $this->assertNull(Setting::get('mision'));
        $this->assertNull(Setting::get('presentacion_titulo'));
    }

    public function test_the_institutional_seeder_fills_the_site(): void
    {
        $this->artisan('db:seed', ['--class' => ContenidoInstitucionalSeeder::class])
            ->assertSuccessful();

        $this->assertGreaterThan(0, Hito::count(), 'Sin hitos, la página de historia sale vacía.');
        $this->assertGreaterThan(0, Objetivo::count());
        $this->assertGreaterThan(0, Competencia::count());
        $this->assertGreaterThan(0, Acceso::count(), 'Sin accesos, la portada pierde su navegación por audiencias.');
        $this->assertGreaterThan(0, Setting::count(), 'Sin ajustes, todos los textos caen a sus valores por defecto.');

        $this->get('/')->assertOk();
        $this->get(route('historia'))->assertOk();
        $this->get(route('objetivos'))->assertOk();
        $this->get(route('competencias'))->assertOk();
    }

    /**
     * Responder 200 no es lo mismo que tener contenido.
     *
     * En el despliegue anterior el sitio se publicó y varias secciones salieron
     * en blanco. Una comprobación de códigos de estado lo habría dado por bueno:
     * una página vacía responde 200 tan contenta. Aquí se mira lo que de verdad
     * llega al visitante.
     */
    public function test_after_seeding_the_fixed_pages_actually_show_their_content(): void
    {
        $this->artisan('db:seed', ['--class' => ContenidoInstitucionalSeeder::class]);

        $paginas = [
            'presentacion' => 'presentacion_titulo',
            'mision' => 'mision',
            'resumen' => 'resumen_titulo',
            'historia' => 'historia_trayectoria_titulo',
            'perfil-egreso' => 'perfil_egreso_2023',
            'perfil-ingreso' => 'perfil_ingreso_especifico',
        ];

        foreach ($paginas as $ruta => $clave) {
            $esperado = (string) Setting::get($clave);

            $this->assertNotSame('', $esperado, "La clave «{$clave}» quedó vacía tras sembrar.");

            // Se compara contra las primeras palabras: el texto puede llevar
            // saltos de línea o acentos que la vista escapa de otra manera.
            $trozo = Str::of($esperado)->stripTags()->squish()->limit(40, '')->toString();

            $this->get(route($ruta))
                ->assertOk()
                ->assertSee($trozo, escape: false);
        }
    }

    public function test_after_seeding_the_list_pages_are_not_empty(): void
    {
        $this->artisan('db:seed', ['--class' => ContenidoInstitucionalSeeder::class]);

        // Estas páginas se alimentan de tablas, no de ajustes: si el bloque
        // correspondiente del seeder no corriera, saldrían con su mensaje de
        // «sin contenido» y el sitio parecería a medio hacer.
        foreach (['objetivos', 'competencias', 'campo-laboral', 'organigrama', 'documentos'] as $ruta) {
            $respuesta = $this->get(route($ruta))->assertOk();

            $this->assertDoesNotMatchRegularExpression(
                '/a[úu]n no hay|sin contenido|en revisi[óo]n/i',
                $respuesta->getContent(),
                "La página «{$ruta}» sale vacía tras sembrar.",
            );
        }

        $this->get(route('estadisticas', ['tipo' => 'matriculados']))->assertOk();
    }

    /**
     * Lo sembrado tiene que aparecer ya escrito en el panel.
     *
     * Si «Ajustes del sitio» abriera con los campos en blanco, quien administra
     * concluiría que no hay nada cargado —y al guardar, borraría de verdad el
     * contenido que el sitio estaba mostrando. Que el dato exista en la base no
     * basta: el formulario tiene que traerlo.
     */
    public function test_the_seeded_texts_appear_already_written_in_the_admin_form(): void
    {
        $this->artisan('db:seed', ['--class' => ContenidoInstitucionalSeeder::class]);

        $this->actingAs(User::query()->create([
            'name' => 'Administradora',
            'email' => 'admin@unasam.edu.pe',
            'password' => 'Clave-Inicial-2026!',
            'role' => User::ROLE_SUPER_ADMIN,
            'is_admin' => true,
        ]));

        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Filament::bootCurrentPanel();

        $formulario = Livewire::test(AjustesSitio::class);

        foreach (['mision', 'vision', 'presentacion_titulo', 'resumen_titulo', 'perfil_egreso_2023'] as $clave) {
            $valor = (string) Setting::get($clave);

            // Sin esta comprobación el test sería vacuo: sin sembrar, la clave
            // vale cadena vacía y el campo del formulario también, así que
            // coincidirían y pasaría igual con la base a medio instalar.
            $this->assertNotSame('', $valor, "La clave «{$clave}» no quedó sembrada.");

            $formulario->assertFormSet([$clave => $valor]);
        }
    }

    public function test_the_mascot_is_configured_on_a_fresh_install(): void
    {
        // El archivo viaja con la aplicación, pero el ajuste que lo señala
        // tiene que quedar puesto o la portada abriría sin ella.
        $this->artisan('db:seed', ['--class' => ContenidoInstitucionalSeeder::class]);

        $this->assertSame('/img/mascota-derecho.webp', Setting::get('home_hero_mascota_url'));
        $this->assertNotSame('', (string) Setting::get('home_hero_mascota_alt'));

        $this->get('/')->assertOk()->assertSee('/img/mascota-derecho.webp');
    }

    public function test_running_the_seeder_twice_does_not_duplicate_content(): void
    {
        // El arranque lo ejecuta en CADA despliegue, no solo en el primero.
        $this->artisan('db:seed', ['--class' => ContenidoInstitucionalSeeder::class]);
        $primera = [Hito::count(), Objetivo::count(), Competencia::count(), Setting::count()];

        $this->artisan('db:seed', ['--class' => ContenidoInstitucionalSeeder::class]);
        $segunda = [Hito::count(), Objetivo::count(), Competencia::count(), Setting::count()];

        $this->assertSame($primera, $segunda, 'La siembra duplicó contenido al repetirse.');
    }

    public function test_the_seeder_never_overwrites_what_was_edited_in_the_panel(): void
    {
        $this->artisan('db:seed', ['--class' => ContenidoInstitucionalSeeder::class]);

        Setting::query()->where('clave', 'lema')->update(['valor' => 'Texto editado desde el panel']);

        $this->artisan('db:seed', ['--class' => ContenidoInstitucionalSeeder::class]);

        $this->assertSame('Texto editado desde el panel', Setting::query()->where('clave', 'lema')->value('valor'));
    }

    public function test_the_seeder_never_republishes_a_journal_the_editors_unpublished(): void
    {
        // RevistaContentSeeder es una importación autoritativa: reescribe el
        // nombre de la revista y fuerza su estado a «publicado». Ejecutarlo en
        // cada despliegue revertiría una decisión editorial deliberada, y sin
        // que nadie se enterara hasta ver la revista publicada de nuevo.
        $this->artisan('db:seed', ['--class' => ContenidoInstitucionalSeeder::class]);

        Revista::query()->first()->forceFill([
            'nombre_corto' => 'Nombre corregido por el editor',
            'estado_editorial' => 'draft',
        ])->save();

        $this->artisan('db:seed', ['--class' => ContenidoInstitucionalSeeder::class]);

        $revista = Revista::query()->first();
        $this->assertSame('Nombre corregido por el editor', $revista->nombre_corto);
        $this->assertSame('draft', $revista->estado_editorial);
    }

    public function test_clearing_query_plans_is_harmless_outside_postgresql(): void
    {
        // El arranque lo ejecuta siempre, y en desarrollo o en la suite la base
        // es SQLite. Tiene que salir sin hacer nada y sin fallar, o rompería el
        // despliegue por un problema que ahí no existe.
        $this->artisan('db:limpiar-planes')
            ->expectsOutputToContain('No es PostgreSQL')
            ->assertSuccessful();
    }

    public function test_the_admin_account_is_created_from_environment_variables(): void
    {
        config()->set('admin.bootstrap_name', 'Administradora del Portal');
        config()->set('admin.bootstrap_email', 'admin@unasam.edu.pe');
        config()->set('admin.bootstrap_password', 'Clave-Inicial-2026!');

        $this->artisan('db:seed', ['--class' => AdminUserSeeder::class])->assertSuccessful();

        $admin = User::query()->where('email', 'admin@unasam.edu.pe')->first();

        $this->assertNotNull($admin, 'Sin cuenta creada no hay forma de entrar a /admin.');
        $this->assertTrue($admin->isSuperAdmin());
    }

    public function test_an_existing_admin_makes_the_variables_unnecessary(): void
    {
        // Un portal ya instalado no necesita crear nada. Antes reclamaba las
        // variables igualmente y dejaba un volcado de pila en el registro de
        // errores en cada despliegue, enterrando los errores de verdad.
        User::query()->create([
            'name' => 'Administradora existente',
            'email' => 'existente@unasam.edu.pe',
            'password' => 'Clave-Existente-2026!',
            'role' => User::ROLE_SUPER_ADMIN,
            'is_admin' => true,
        ]);

        config()->set('admin.bootstrap_name', null);
        config()->set('admin.bootstrap_email', null);
        config()->set('admin.bootstrap_password', null);

        $this->artisan('db:seed', ['--class' => AdminUserSeeder::class])
            ->expectsOutputToContain('Ya hay un superadministrador')
            ->assertSuccessful();
    }

    public function test_a_fresh_install_without_the_variables_says_nobody_could_log_in(): void
    {
        config()->set('admin.bootstrap_name', null);
        config()->set('admin.bootstrap_email', null);
        config()->set('admin.bootstrap_password', null);
        $this->app['env'] = 'production';

        $this->expectExceptionMessage('nadie podría entrar a /admin');

        $this->artisan('db:seed', ['--class' => AdminUserSeeder::class, '--force' => true]);
    }

    public function test_a_weak_admin_password_is_refused_instead_of_silently_accepted(): void
    {
        config()->set('admin.bootstrap_name', 'Administradora del Portal');
        config()->set('admin.bootstrap_email', 'admin@unasam.edu.pe');
        config()->set('admin.bootstrap_password', 'admin123');

        $this->expectException(ValidationException::class);

        $this->artisan('db:seed', ['--class' => AdminUserSeeder::class]);
    }
}
