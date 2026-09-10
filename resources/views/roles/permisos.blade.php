<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-gray-500">
            <span>Panel</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <a href="{{ route('users.index') }}" class="hover:text-gray-300 transition-colors">Usuarios</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-white">Permisos</span>
        </div>
    </x-slot>

    @php
        $moduloLabels = [
            'usuarios' => 'Usuarios',
            'unidades' => 'Unidades',
            'reportes' => 'Reportes',
            'viajes' => 'Viajes',
            'gastos' => 'Gastos',
            'inventario' => 'Inventario',
            'contratos' => 'Contratos',
            'contratos_historicos' => 'Contratos históricos',
            'egresos_ingresos' => 'Egresos e Ingresos',
            'ordenes_trabajo' => 'Órdenes de trabajo',
            'taller_externo' => 'Taller externo',
        ];
    @endphp

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white">Permisos por rol</h1>
        <p class="text-sm text-gray-500 mt-0.5">Marca qué puede hacer cada rol en cada módulo del sistema.</p>
    </div>

    @if (session('success'))
        <div class="mb-4 flex items-center gap-3 px-4 py-3 bg-green-900/30 border border-green-700/50 rounded-xl text-green-300 text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('roles.permisos.update') }}">
        @csrf
        @method('PUT')

        <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-700/40">
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-5 py-3 sticky left-0 bg-gray-800/95">Permiso</th>
                            @foreach($roles as $role)
                                <th class="text-center text-xs font-medium text-gray-500 uppercase tracking-wider px-4 py-3 whitespace-nowrap">
                                    {{ \App\Models\User::ROLE_LABELS[$role->name] ?? ucfirst(str_replace('_', ' ', $role->name)) }}
                                    @if($role->name === 'superadmin')
                                        <span class="block normal-case text-[10px] text-gray-600 font-normal">(siempre todos)</span>
                                    @endif
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/30">
                        @foreach($modulos as $modulo => $permisos)
                            <tr class="bg-gray-900/40">
                                <td colspan="{{ $roles->count() + 1 }}" class="px-5 py-2 text-xs font-semibold text-red-400 uppercase tracking-wider sticky left-0 bg-gray-900/95">
                                    {{ $moduloLabels[$modulo] ?? ucfirst(str_replace('_', ' ', $modulo)) }}
                                </td>
                            </tr>
                            @foreach($permisos as $permiso)
                                <tr class="hover:bg-gray-700/20 transition-colors">
                                    <td class="px-5 py-2.5 text-sm text-gray-300 sticky left-0 bg-gray-800/95">
                                        {{ ucfirst(str_replace('_', ' ', explode('.', $permiso->name)[1])) }}
                                    </td>
                                    @foreach($roles as $role)
                                        @php $marcado = $role->permissions->contains('name', $permiso->name); @endphp
                                        <td class="px-4 py-2.5 text-center">
                                            @if($role->name === 'superadmin')
                                                <input type="checkbox" checked disabled
                                                       class="w-4 h-4 rounded border-gray-600 bg-gray-900 text-gray-600 opacity-60 cursor-not-allowed"/>
                                            @else
                                                <input type="checkbox"
                                                       name="permissions[{{ $role->id }}][]"
                                                       value="{{ $permiso->name }}"
                                                       {{ $marcado ? 'checked' : '' }}
                                                       class="w-4 h-4 rounded border-gray-600 bg-gray-900 text-red-600 focus:ring-red-500 focus:ring-offset-gray-800 cursor-pointer"/>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-5 py-4 border-t border-gray-700/40 flex justify-end">
                <button type="submit"
                        class="brand-gradient px-5 py-2.5 text-white text-sm font-semibold rounded-xl shadow-lg shadow-red-950/40 hover:opacity-90 transition-opacity">
                    Guardar cambios
                </button>
            </div>
        </div>
    </form>

</x-app-layout>
