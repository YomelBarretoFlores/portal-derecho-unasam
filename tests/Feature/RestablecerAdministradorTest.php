<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Sin esto, una contraseña olvidada cierra el panel para siempre.
 *
 * La siembra crea la cuenta inicial una sola vez y no vuelve a tocar la
 * contraseña, y no hay recuperación por correo mientras no haya SMTP. El
 * portal de la UNASAM se quedó exactamente así: la cuenta existe y nadie
 * puede entrar.
 */
class RestablecerAdministradorTest extends TestCase
{
    use RefreshDatabase;

    private const BUENA = 'Clave-Larga-2026#';

    public function test_it_changes_the_password_of_an_existing_account(): void
    {
        $usuario = User::factory()->create([
            'email' => 'admin@example.test',
            'password' => 'la-de-antes',
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        $this->artisan('admin:restablecer', ['correo' => 'admin@example.test'])
            ->expectsQuestion('Contraseña nueva', self::BUENA)
            ->expectsQuestion('Repítala', self::BUENA)
            ->assertSuccessful();

        $this->assertTrue(Hash::check(self::BUENA, $usuario->fresh()->password));
    }

    public function test_it_promotes_the_account_so_it_can_reach_the_panel(): void
    {
        // Una cuenta sin rol pasa el login y después no ve nada, que desde
        // fuera se parece mucho a que el panel esté roto.
        $usuario = User::factory()->create([
            'email' => 'editor@example.test',
            'role' => User::ROLE_EDITOR,
        ]);

        $this->artisan('admin:restablecer', ['correo' => 'editor@example.test'])
            ->expectsQuestion('Contraseña nueva', self::BUENA)
            ->expectsQuestion('Repítala', self::BUENA)
            ->assertSuccessful();

        $this->assertTrue($usuario->fresh()->isSuperAdmin());
    }

    public function test_it_creates_the_account_when_there_is_none(): void
    {
        $this->artisan('admin:restablecer', ['correo' => 'nuevo@example.test', '--nombre' => 'Administrador'])
            ->expectsQuestion('Contraseña nueva', self::BUENA)
            ->expectsQuestion('Repítala', self::BUENA)
            ->assertSuccessful();

        $creado = User::where('email', 'nuevo@example.test')->first();
        $this->assertNotNull($creado);
        $this->assertTrue($creado->isSuperAdmin());
    }

    public function test_it_refuses_two_passwords_that_do_not_match(): void
    {
        $usuario = User::factory()->create(['email' => 'admin@example.test', 'password' => 'la-de-antes']);

        $this->artisan('admin:restablecer', ['correo' => 'admin@example.test'])
            ->expectsQuestion('Contraseña nueva', self::BUENA)
            ->expectsQuestion('Repítala', 'otra-cosa')
            ->assertFailed();

        $this->assertTrue(Hash::check('la-de-antes', $usuario->fresh()->password));
    }

    public function test_it_refuses_a_weak_password_and_changes_nothing(): void
    {
        $usuario = User::factory()->create(['email' => 'admin@example.test', 'password' => 'la-de-antes']);

        $this->artisan('admin:restablecer', ['correo' => 'admin@example.test'])
            ->expectsQuestion('Contraseña nueva', 'corta')
            ->expectsQuestion('Repítala', 'corta')
            ->assertFailed();

        $this->assertTrue(Hash::check('la-de-antes', $usuario->fresh()->password));
    }

    public function test_it_rejects_something_that_is_not_an_email(): void
    {
        $this->artisan('admin:restablecer', ['correo' => 'no-es-un-correo'])
            ->assertFailed();

        $this->assertSame(0, User::count());
    }
}
