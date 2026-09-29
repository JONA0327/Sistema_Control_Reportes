<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    const PERMISOS = [
        'usuarios' => ['ver', 'crear', 'editar', 'eliminar'],
        'unidades' => ['ver', 'crear', 'editar', 'eliminar'],
        'reportes' => ['ver', 'crear', 'editar', 'eliminar', 'estado'],
        'viajes' => ['ver', 'editar', 'eliminar'],
        'gastos' => ['ver', 'registrar', 'aprobar'],
        'inventario' => ['ver', 'crear', 'editar', 'eliminar', 'movimientos', 'aprobar'],
        'contratos' => ['ver', 'crear', 'editar', 'eliminar', 'pagos'],
        'anticipos' => ['ver', 'crear', 'editar', 'eliminar'],
        'contratos_historicos' => ['ver', 'editar', 'eliminar'],
        'egresos_ingresos' => ['ver', 'crear', 'editar', 'eliminar'],
        'ordenes_trabajo' => ['ver', 'gestionar', 'piezas'],
        'taller_externo' => ['ver'],
        'bitacora' => ['ver'],
    ];

    const ASIGNACIONES = [
        'operador' => [
            'reportes.ver', 'reportes.crear', 'reportes.editar',
            'viajes.ver',
            'gastos.ver', 'gastos.registrar',
        ],
        'mecanico' => [
            'reportes.ver', 'reportes.crear',
            'unidades.ver',
            'inventario.ver', 'inventario.movimientos',
            'ordenes_trabajo.ver', 'ordenes_trabajo.gestionar', 'ordenes_trabajo.piezas',
        ],
        'mecanico_externo' => [
            'ordenes_trabajo.piezas',
            'taller_externo.ver',
        ],
    ];

    public function run(): void
    {
        $todos = [];

        foreach (self::PERMISOS as $modulo => $acciones) {
            foreach ($acciones as $accion) {
                $nombre = "{$modulo}.{$accion}";
                Permission::firstOrCreate(['name' => $nombre, 'guard_name' => 'web']);
                $todos[] = $nombre;
            }
        }

        foreach (['superadmin', 'administracion'] as $rolAdmin) {
            Role::firstOrCreate(['name' => $rolAdmin, 'guard_name' => 'web'])->syncPermissions($todos);
        }

        foreach (self::ASIGNACIONES as $rolNombre => $permisos) {
            Role::firstOrCreate(['name' => $rolNombre, 'guard_name' => 'web'])->syncPermissions($permisos);
        }

        // Los anticipos se separaron del módulo de contratos y ahora tienen
        // su propio permiso (anticipos.*), así que el antiguo se elimina.
        Permission::where('name', 'contratos.anticipos')->delete();
    }
}
