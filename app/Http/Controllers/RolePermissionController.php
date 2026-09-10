<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    public function edit()
    {
        $roles = Role::with('permissions')->orderBy('name')->get();
        $modulos = Permission::orderBy('name')->get()->groupBy(fn ($permiso) => explode('.', $permiso->name)[0]);

        return view('roles.permisos', compact('roles', 'modulos'));
    }

    public function update(Request $request)
    {
        $roles = Role::all();

        foreach ($roles as $role) {
            if ($role->name === 'superadmin') {
                $role->syncPermissions(Permission::all());
                continue;
            }

            $role->syncPermissions($request->input("permissions.{$role->id}", []));
        }

        return redirect()->route('roles.permisos.edit')
            ->with('success', 'Permisos actualizados correctamente.');
    }
}
