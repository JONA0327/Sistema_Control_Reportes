<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IngresoEgresoSelectorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionSeeder::class);
    }

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->assignRole('superadmin');

        return $user;
    }

    public function test_index_without_pais_shows_the_country_selector(): void
    {
        $response = $this->actingAs($this->admin())->get('/ingresos-egresos');

        $response->assertOk();
        $response->assertViewIs('ingresos-egresos.selector');
    }

    public function test_index_with_valid_pais_shows_the_movements_list(): void
    {
        $response = $this->actingAs($this->admin())->get('/ingresos-egresos?pais=usa');

        $response->assertOk();
        $response->assertViewIs('ingresos-egresos.index');
        $response->assertViewHas('pais', 'usa');
    }

    public function test_index_with_invalid_pais_falls_back_to_the_selector(): void
    {
        $response = $this->actingAs($this->admin())->get('/ingresos-egresos?pais=francia');

        $response->assertOk();
        $response->assertViewIs('ingresos-egresos.selector');
    }

    public function test_create_defaults_pais_from_query_string(): void
    {
        $response = $this->actingAs($this->admin())->get('/ingresos-egresos/create?pais=usa');

        $response->assertOk();
        $response->assertViewHas('pais', 'usa');
    }

    public function test_storing_a_movement_redirects_back_into_its_own_country_view(): void
    {
        $response = $this->actingAs($this->admin())->post('/ingresos-egresos', [
            'tipo' => 'ingreso',
            'concepto' => 'Venta de prueba',
            'monto' => '100.00',
            'fecha' => now()->format('Y-m-d'),
            'pais' => 'usa',
        ]);

        $response->assertRedirect('/ingresos-egresos?pais=usa');
        $this->assertDatabaseHas('ingresos_egresos', ['concepto' => 'Venta de prueba', 'pais' => 'usa']);
    }
}
