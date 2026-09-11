<?php

namespace Tests\Feature;

use App\Models\Acceso;
use App\Models\Competencia;
use App\Models\Hito;
use App\Models\Objetivo;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\ContenidoInstitucionalSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
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
        $this->assertSame(0, Setting::count());
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

    public function test_a_weak_admin_password_is_refused_instead_of_silently_accepted(): void
    {
        config()->set('admin.bootstrap_name', 'Administradora del Portal');
        config()->set('admin.bootstrap_email', 'admin@unasam.edu.pe');
        config()->set('admin.bootstrap_password', 'admin123');

        $this->expectException(ValidationException::class);

        $this->artisan('db:seed', ['--class' => AdminUserSeeder::class]);
    }
}
