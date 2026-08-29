<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-gray-500">
            <span>Panel</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-white">Unidades</span>
        </div>
    </x-slot>

    {{-- Encabezado --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Unidades</h1>
            <p class="text-sm text-gray-500 mt-0.5">Gestión de autobuses y asignación de operadores</p>
        </div>
        <a href="{{ route('buses.create') }}"
           class="brand-gradient inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-white text-sm font-semibold shadow-lg shadow-red-950/40 hover:opacity-90 transition-opacity">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nueva unidad
        </a>
    </div>

    {{-- Alerta --}}
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
            <form method="GET" action="{{ route('buses.index') }}" class="flex-1 flex gap-2">
                <div class="relative flex-1 max-w-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ $search }}"
                           placeholder="Buscar por número, placa o estado..."
                           class="w-full pl-9 pr-4 py-2 bg-gray-900/80 border border-gray-700 rounded-xl text-sm text-white placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"/>
                </div>
                <button type="submit"
                        class="px-4 py-2 bg-gray-700/60 border border-gray-600/40 text-gray-300 text-sm rounded-xl hover:bg-gray-700 transition-colors">
                    Buscar
                </button>
                @if($search)
                    <a href="{{ route('buses.index') }}"
                       class="px-4 py-2 bg-gray-700/30 border border-gray-600/30 text-gray-500 text-sm rounded-xl hover:text-gray-300 transition-colors">
                        Limpiar
                    </a>
                @endif
            </form>
            <p class="text-xs text-gray-600">{{ $buses->total() }} unidad(es)</p>
        </div>

        {{-- Tabla --}}
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-700/40">
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-5 py-3">Unidad</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-5 py-3">Placa</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-5 py-3 hidden md:table-cell">Operador asignado</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-5 py-3">Estado</th>
                        <th class="text-right text-xs font-medium text-gray-500 uppercase tracking-wider px-5 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/30">
                    @forelse($buses as $bus)
                    <tr class="hover:bg-gray-700/20 transition-colors">
                        {{-- Foto + número --}}
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                @if($bus->foto)
                                    <img src="{{ Storage::url($bus->foto) }}"
                                         alt="Bus #{{ $bus->num_bus }}"
                                         class="w-12 h-10 rounded-xl object-cover border border-gray-700/50 flex-shrink-0"/>
                                @else
                                    <div class="w-12 h-10 rounded-xl bg-gray-700/60 border border-gray-600/40 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                        </svg>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-sm font-semibold text-white">Bus #{{ $bus->num_bus }}</p>
                                </div>
                            </div>
                        </td>
                        {{-- Placa --}}
                        <td class="px-5 py-3">
                            <span class="text-sm font-mono font-medium text-gray-200 tracking-wider uppercase">{{ $bus->placa }}</span>
                        </td>
                        {{-- Operador --}}
                        <td class="px-5 py-3 hidden md:table-cell">
                            @if($bus->operator)
                                <div class="flex items-center gap-2.5">
                                    @if($bus->operator->foto)
                                        <img src="{{ Storage::url($bus->operator->foto) }}"
                                             class="w-7 h-7 rounded-lg object-cover flex-shrink-0"/>
                                    @else
                                        <div class="brand-gradient w-7 h-7 rounded-lg flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                            {{ strtoupper(substr($bus->operator->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="text-sm text-white truncate">{{ $bus->operator->name }} {{ $bus->operator->last_name }}</p>
                                        <p class="text-xs text-gray-500 truncate">
                                            {{ $bus->operator->carnet }}
                                            @if($bus->copiloto)
                                                · Copiloto: {{ $bus->copiloto->name }} {{ $bus->copiloto->last_name }}
                                            @endif
                                        </p>
                                    </div>
                                    @if($bus->pending_loans_count > 0)
                                        <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-lg border font-medium bg-amber-500/10 text-amber-400 border-amber-500/20 flex-shrink-0"
                                              title="Herramientas/refacciones pendientes de devolver">
                                            {{ $bus->pending_loans_count }} pendiente(s)
                                        </span>
                                    @endif
                                </div>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-xs text-gray-500 bg-gray-800/50 border border-gray-700/40 px-2.5 py-1 rounded-lg">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Sin asignar
                                </span>
                            @endif
                        </td>
                        {{-- Estado --}}
                        <td class="px-5 py-3">
                            @php
                                $badge = match($bus->status) {
                                    'activo'        => 'bg-green-500/10 text-green-400 border-green-500/20',
                                    'mantenimiento' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                    default         => 'bg-gray-700/50 text-gray-400 border-gray-600/40',
                                };
                                $dot = match($bus->status) {
                                    'activo'        => 'bg-green-400',
                                    'mantenimiento' => 'bg-amber-400',
                                    default         => 'bg-gray-500',
                                };
                            @endphp
                            <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-lg border font-medium {{ $badge }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $dot }}"></span>
                                {{ ucfirst($bus->status) }}
                            </span>
                        </td>
                        {{-- Acciones --}}
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('buses.show', $bus) }}"
                                   class="p-2 rounded-lg text-gray-500 hover:text-white hover:bg-gray-700 transition-all"
                                   title="Ver">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('buses.edit', $bus) }}"
                                   class="p-2 rounded-lg text-gray-500 hover:text-white hover:bg-gray-700 transition-all"
                                   title="Editar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('buses.destroy', $bus) }}"
                                      onsubmit="return confirm('¿Eliminar la unidad #{{ $bus->num_bus }}? Esta acción no se puede deshacer.')">
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
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-12 h-12 rounded-2xl bg-gray-800 border border-gray-700 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-500">No se encontraron unidades</p>
                                @if($search)
                                    <a href="{{ route('buses.index') }}" class="text-xs text-red-500 hover:text-red-400">Limpiar búsqueda</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($buses->hasPages())
            <div class="px-5 py-4 border-t border-gray-700/40 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <p class="text-xs text-gray-600">
                    Mostrando {{ $buses->firstItem() }}–{{ $buses->lastItem() }} de {{ $buses->total() }}
                </p>
                <div class="flex items-center flex-wrap gap-1 justify-center sm:justify-end">
                    @if($buses->onFirstPage())
                        <span class="px-3 py-1.5 text-xs text-gray-700 bg-gray-800/40 border border-gray-700/40 rounded-lg cursor-not-allowed">&lsaquo;</span>
                    @else
                        <a href="{{ $buses->previousPageUrl() }}" class="px-3 py-1.5 text-xs text-gray-400 bg-gray-800/40 border border-gray-700/40 rounded-lg hover:bg-gray-700 transition-colors">&lsaquo;</a>
                    @endif
                    @foreach($buses->getUrlRange(max(1, $buses->currentPage()-2), min($buses->lastPage(), $buses->currentPage()+2)) as $page => $url)
                        @if($page == $buses->currentPage())
                            <span class="brand-gradient px-3 py-1.5 text-xs text-white rounded-lg font-medium">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="px-3 py-1.5 text-xs text-gray-400 bg-gray-800/40 border border-gray-700/40 rounded-lg hover:bg-gray-700 transition-colors">{{ $page }}</a>
                        @endif
                    @endforeach
                    @if($buses->hasMorePages())
                        <a href="{{ $buses->nextPageUrl() }}" class="px-3 py-1.5 text-xs text-gray-400 bg-gray-800/40 border border-gray-700/40 rounded-lg hover:bg-gray-700 transition-colors">&rsaquo;</a>
                    @else
                        <span class="px-3 py-1.5 text-xs text-gray-700 bg-gray-800/40 border border-gray-700/40 rounded-lg cursor-not-allowed">&rsaquo;</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

</x-app-layout>
