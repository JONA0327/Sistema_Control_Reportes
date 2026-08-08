<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-gray-500">
            <span>Panel</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-white">Usuarios</span>
        </div>
    </x-slot>

    {{-- Encabezado --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Usuarios</h1>
            <p class="text-sm text-gray-500 mt-0.5">Gestión de cuentas del sistema</p>
        </div>
        <a href="{{ route('users.create') }}"
           class="brand-gradient inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-white text-sm font-semibold shadow-lg shadow-red-950/40 hover:opacity-90 transition-opacity">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo usuario
        </a>
    </div>

    {{-- Alertas --}}
    @if (session('success'))
        <div class="mb-4 flex items-center gap-3 px-4 py-3 bg-green-900/30 border border-green-700/50 rounded-xl text-green-300 text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Tarjeta principal --}}
    <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl overflow-hidden">

        {{-- Barra de búsqueda --}}
        <div class="px-5 py-4 border-b border-gray-700/40 flex flex-col sm:flex-row sm:items-center gap-3">
            <form method="GET" action="{{ route('users.index') }}" class="flex-1 flex gap-2">
                <div class="relative flex-1 max-w-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ $search }}"
                           placeholder="Buscar por nombre, usuario, carnet..."
                           class="w-full pl-9 pr-4 py-2 bg-gray-900/80 border border-gray-700 rounded-xl text-sm text-white placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"/>
                </div>
                <button type="submit"
                        class="px-4 py-2 bg-gray-700/60 border border-gray-600/40 text-gray-300 text-sm rounded-xl hover:bg-gray-700 transition-colors">
                    Buscar
                </button>
                @if($search)
                    <a href="{{ route('users.index') }}"
                       class="px-4 py-2 bg-gray-700/30 border border-gray-600/30 text-gray-500 text-sm rounded-xl hover:text-gray-300 transition-colors">
                        Limpiar
                    </a>
                @endif
            </form>
            <p class="text-xs text-gray-600">
                {{ $users->total() }} usuario(s) encontrado(s)
            </p>
        </div>

        {{-- Tabla --}}
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-700/40">
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-5 py-3">Usuario</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-5 py-3 hidden md:table-cell">Carnet</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-5 py-3 hidden lg:table-cell">Correo</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-5 py-3">Rol</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-5 py-3 hidden xl:table-cell">Estado</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-5 py-3 hidden sm:table-cell">Registrado</th>
                        <th class="text-right text-xs font-medium text-gray-500 uppercase tracking-wider px-5 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/30">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-700/20 transition-colors group">
                        {{-- Avatar + nombre --}}
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                @if($user->photo)
                                    <img src="{{ Storage::url($user->photo) }}"
                                         alt="{{ $user->name }}"
                                         class="w-9 h-9 rounded-xl object-cover border border-gray-700/50 flex-shrink-0"/>
                                @else
                                    <div class="brand-gradient w-9 h-9 rounded-xl flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-white truncate">{{ $user->name }} {{ $user->last_name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ '@' . $user->username }}</p>
                                </div>
                            </div>
                        </td>
                        {{-- Carnet --}}
                        <td class="px-5 py-3 hidden md:table-cell">
                            <span class="text-sm text-gray-400 font-mono">{{ $user->carnet }}</span>
                        </td>
                        {{-- Email --}}
                        <td class="px-5 py-3 hidden lg:table-cell">
                            <span class="text-sm text-gray-400 truncate">{{ $user->email }}</span>
                        </td>
                        {{-- Rol --}}
                        <td class="px-5 py-3">
                            @php $role = $user->roles->first(); @endphp
                            @if($role)
                                <span class="inline-flex items-center text-xs px-2.5 py-1 rounded-lg font-medium
                                    {{ $role->name === 'administrador'
                                        ? 'bg-red-500/10 text-red-400 border border-red-500/20'
                                        : 'bg-gray-700/50 text-gray-400 border border-gray-600/40' }}">
                                    {{ $user->role_label }}
                                </span>
                            @else
                                <span class="text-xs text-gray-600">Sin rol</span>
                            @endif
                        </td>
                        {{-- Estado --}}
                        <td class="px-5 py-3 hidden xl:table-cell">
                            @if($user->is_active)
                                <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-lg font-medium bg-green-500/10 text-green-400 border border-green-500/20">
                                    <span class="w-1.5 h-1.5 bg-green-400 rounded-full"></span>
                                    Activo
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-lg font-medium bg-gray-700/50 text-gray-500 border border-gray-600/40">
                                    <span class="w-1.5 h-1.5 bg-gray-500 rounded-full"></span>
                                    Inactivo
                                </span>
                            @endif
                        </td>
                        {{-- Fecha --}}
                        <td class="px-5 py-3 hidden sm:table-cell">
                            <span class="text-xs text-gray-500">{{ $user->created_at->format('d/m/Y') }}</span>
                        </td>
                        {{-- Acciones --}}
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('users.edit', $user) }}"
                                   class="p-2 rounded-lg text-gray-500 hover:text-white hover:bg-gray-700 transition-all"
                                   title="Editar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('users.destroy', $user) }}"
                                          onsubmit="return confirm('¿Eliminar al usuario {{ addslashes($user->name) }}? Esta acción no se puede deshacer.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-2 rounded-lg text-gray-500 hover:text-red-400 hover:bg-red-500/10 transition-all"
                                                title="Eliminar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                @else
                                    <div class="w-8 h-8"></div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-12 h-12 rounded-2xl bg-gray-800 border border-gray-700 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-500">No se encontraron usuarios</p>
                                @if($search)
                                    <a href="{{ route('users.index') }}" class="text-xs text-red-500 hover:text-red-400">Limpiar búsqueda</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($users->hasPages())
            <div class="px-5 py-4 border-t border-gray-700/40 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <p class="text-xs text-gray-600">
                    Mostrando {{ $users->firstItem() }}–{{ $users->lastItem() }} de {{ $users->total() }}
                </p>
                <div class="flex items-center flex-wrap gap-1 justify-center sm:justify-end">
                    {{-- Anterior --}}
                    @if($users->onFirstPage())
                        <span class="px-3 py-1.5 text-xs text-gray-700 bg-gray-800/40 border border-gray-700/40 rounded-lg cursor-not-allowed">
                            &lsaquo;
                        </span>
                    @else
                        <a href="{{ $users->previousPageUrl() }}"
                           class="px-3 py-1.5 text-xs text-gray-400 bg-gray-800/40 border border-gray-700/40 rounded-lg hover:bg-gray-700 transition-colors">
                            &lsaquo;
                        </a>
                    @endif

                    @foreach($users->getUrlRange(max(1, $users->currentPage()-2), min($users->lastPage(), $users->currentPage()+2)) as $page => $url)
                        @if($page == $users->currentPage())
                            <span class="brand-gradient px-3 py-1.5 text-xs text-white rounded-lg font-medium">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}"
                               class="px-3 py-1.5 text-xs text-gray-400 bg-gray-800/40 border border-gray-700/40 rounded-lg hover:bg-gray-700 transition-colors">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach

                    {{-- Siguiente --}}
                    @if($users->hasMorePages())
                        <a href="{{ $users->nextPageUrl() }}"
                           class="px-3 py-1.5 text-xs text-gray-400 bg-gray-800/40 border border-gray-700/40 rounded-lg hover:bg-gray-700 transition-colors">
                            &rsaquo;
                        </a>
                    @else
                        <span class="px-3 py-1.5 text-xs text-gray-700 bg-gray-800/40 border border-gray-700/40 rounded-lg cursor-not-allowed">
                            &rsaquo;
                        </span>
                    @endif
                </div>
            </div>
        @endif
    </div>

</x-app-layout>
