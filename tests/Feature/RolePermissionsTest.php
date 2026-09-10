<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionSeeder::class);
    }

    private function userWithRole(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    public function test_admin_can_view_permissions_screen(): void
    {
        $admin = $this->userWithRole('superadmin');

        $this->actingAs($admin)->get('/usuarios/permisos')->assertOk();
    }

    public function test_operador_cannot_view_permissions_screen(): void
    {
        $operador = $this->userWithRole('operador');

        $this->actingAs($operador)->get('/usuarios/permisos')->assertForbidden();
    }

    public function test_mecanico_can_view_but_not_create_buses(): void
    {
        $mecanico = $this->userWithRole('mecanico');

        $this->actingAs($mecanico)->get('/buses')->assertOk();
        $this->actingAs($mecanico)->get('/buses/create')->assertForbidden();
    }

    public function test_operador_cannot_view_inventario(): void
    {
        $operador = $this->userWithRole('operador');

        $this->actingAs($operador)->get('/inventario')->assertForbidden();
    }

    public function test_mecanico_externo_cannot_view_general_reports(): void
    {
        $mecanicoExterno = $this->userWithRole('mecanico_externo');

        $this->actingAs($mecanicoExterno)->get('/reports')->assertForbidden();
        $this->actingAs($mecanicoExterno)->get('/mi-taller')->assertOk();
    }

    public function test_admin_can_revoke_a_permission_and_it_takes_effect_immediately(): void
    {
        $admin = $this->userWithRole('superadmin');
        $operador = $this->userWithRole('operador');

        $this->actingAs($operador)->get('/reports/create')->assertOk();

        $roleId = \Spatie\Permission\Models\Role::where('name', 'operador')->value('id');

        $this->actingAs($admin)->put('/usuarios/permisos', [
            'permissions' => [
                $roleId => ['reportes.ver', 'reportes.editar'],
            ],
        ])->assertRedirect();

        $this->actingAs($operador)->get('/reports/create')->assertForbidden();
        $this->actingAs($operador)->get('/reports')->assertOk();
    }

    public function test_superadmin_role_keeps_all_permissions_even_if_submitted_without_them(): void
    {
        $admin = $this->userWithRole('superadmin');
        $adminRoleId = \Spatie\Permission\Models\Role::where('name', 'superadmin')->value('id');

        $this->actingAs($admin)->put('/usuarios/permisos', [
            'permissions' => [
                $adminRoleId => ['reportes.ver'],
            ],
        ])->assertRedirect();

        $this->actingAs($admin)->get('/users')->assertOk();
        $this->actingAs($admin)->get('/inventario')->assertOk();
    }
}
