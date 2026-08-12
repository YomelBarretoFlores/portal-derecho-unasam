<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\ContentAudit;
use App\Models\User;
use App\Policies\ContentAuditPolicy;
use App\Policies\ContentPolicy;
use App\Policies\UserPolicy;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_roles_control_panel_and_content_permissions(): void
    {
        $editor = User::factory()->create(['role' => User::ROLE_EDITOR]);
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $visitor = User::factory()->create(['role' => null]);
        $panel = Filament::getPanel('admin');

        $this->assertTrue($editor->canAccessPanel($panel));
        $this->assertTrue($superAdmin->canAccessPanel($panel));
        $this->assertFalse($visitor->canAccessPanel($panel));
        $this->assertTrue((new ContentPolicy)->create($editor));
    }

    public function test_strict_filament_policies_expose_every_resource_ability(): void
    {
        $abilities = [
            'viewAny', 'view', 'create', 'update', 'delete', 'deleteAny',
            'restore', 'restoreAny', 'forceDelete', 'forceDeleteAny',
            'replicate', 'reorder',
        ];

        foreach ([ContentPolicy::class, ContentAuditPolicy::class, UserPolicy::class] as $policy) {
            foreach ($abilities as $ability) {
                $this->assertTrue(method_exists($policy, $ability), "{$policy} no implementa {$ability}().");
            }
        }

        $admin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $this->actingAs($admin)->get('/admin')->assertOk();
    }

    public function test_last_super_admin_cannot_be_demoted_or_deleted(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        try {
            $admin->update(['role' => User::ROLE_EDITOR]);
            $this->fail('La degradación debió ser rechazada.');
        } catch (ValidationException) {
            $this->assertSame(User::ROLE_SUPER_ADMIN, $admin->fresh()->role);
        }

        $admin = $admin->fresh();
        $this->expectException(ValidationException::class);
        $admin->delete();
    }

    public function test_content_changes_are_audited_without_secrets(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $this->actingAs($admin);

        BlogPost::create([
            'tipo' => 'noticia', 'titulo' => 'Registro auditable', 'slug' => 'registro-auditable',
            'contenido' => '<p>Contenido</p>', 'fecha' => today(), 'publicado' => true,
        ]);

        $audit = ContentAudit::query()->where('auditable_type', BlogPost::class)->latest('id')->firstOrFail();
        $this->assertSame($admin->id, $audit->user_id);
        $this->assertSame('created', $audit->event);
        $this->assertArrayNotHasKey('password', $audit->new_values ?? []);
    }
}
