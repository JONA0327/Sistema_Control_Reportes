<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $admin           = Role::firstOrCreate(['name' => 'superadmin',  'guard_name' => 'web']);
        $operador        = Role::firstOrCreate(['name' => 'operador',      'guard_name' => 'web']);
        $mecanico        = Role::firstOrCreate(['name' => 'mecanico',      'guard_name' => 'web']);
        $administracion  = Role::firstOrCreate(['name' => 'administracion', 'guard_name' => 'web']);
        $mecanicoExterno = Role::firstOrCreate(['name' => 'mecanico_externo', 'guard_name' => 'web']);

        $user = User::firstOrCreate(
            ['email' => 'soportemerlotransportes@gmail.com'],
            [
                'name'      => 'Administrador',
                'last_name' => 'Sistema',
                'username'  => 'admin',
                'carnet'    => 'ADMIN-001',
                'password'  => Hash::make('password'),
                'is_active' => true,
            ]
        );

        $user->syncRoles($admin);
    }
}
