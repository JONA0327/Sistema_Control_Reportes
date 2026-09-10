<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-gray-500">
            <span>Panel</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-white">Reportes</span>
        </div>
    </x-slot>

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Reportes</h1>
            <p class="text-sm text-gray-500 mt-0.5">Gestión de reportes de unidades</p>
        </div>
        @can('reportes.crear')
        <a href="{{ route('reports.create') }}"
           class="brand-gradient inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-white text-sm font-semibold shadow-lg shadow-red-950/40 hover:opacity-90 transition-opacity">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo reporte
        </a>
        @endcan
    </div>

    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/60" @click="show = false"></div>
            <div class="relative bg-gray-800 border border-gray-700/50 rounded-2xl shadow-2xl max-w-sm w-full p-6 text-center"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">
                <div class="w-14 h-14 bg-green-500/10 border border-green-500/30 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white mb-1">¡Listo!</h3>
                <p class="text-sm text-gray-400 mb-5">{{ session('success') }}</p>
                <button @click="show = false"
                        class="w-full brand-gradient text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-lg shadow-red-950/40 hover:opacity-90 transition-opacity">
                    Aceptar
                </button>
            </div>
        </div>
    @endif

    <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl overflow-hidden">

        {{-- Filtros --}}
        <div class="px-5 py-4 border-b border-gray-700/40 flex flex-col sm:flex-row gap-3">
            <form method="GET" action="{{ route('reports.index') }}" class="flex flex-wrap gap-2 flex-1">
                <div class="relative flex-1 min-w-48">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ $search }}"
                           placeholder="Buscar por unidad o descripción..."
                           class="w-full pl-9 pr-4 py-2 bg-gray-900/80 border border-gray-700 rounded-xl text-sm text-white placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"/>
                </div>
                <select name="status"
                        class="px-3 py-2 bg-gray-900/80 border border-gray-700 rounded-xl text-sm text-white focus:outline-none focus:border-red-500 transition-colors">
                    <option value="" class="bg-gray-900">Todos los estados</option>
                    <option value="nuevo"      class="bg-gray-900" {{ $status === 'nuevo'      ? 'selected' : '' }}>Nuevo</option>
                    <option value="en_proceso" class="bg-gray-900" {{ $status === 'en_proceso' ? 'selected' : '' }}>En proceso</option>
                    <option value="resuelto"   class="bg-gray-900" {{ $status === 'resuelto'   ? 'selected' : '' }}>Resuelto</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-gray-700/60 border border-gray-600/40 text-gray-300 text-sm rounded-xl hover:bg-gray-700 transition-colors">
                    Filtrar
                </button>
                @if($search || $status)
                    <a href="{{ route('reports.index') }}" class="px-4 py-2 bg-gray-700/30 border border-gray-600/30 text-gray-500 text-sm rounded-xl hover:text-gray-300 transition-colors">
                        Limpiar
                    </a>
                @endif
            </form>
            <p class="text-xs text-gray-600 self-center">{{ $reports->total() }} reporte(s)</p>
        </div>

        {{-- Tabla --}}
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-700/40">
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-5 py-3">Reporte</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-5 py-3 hidden md:table-cell">Unidad</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-5 py-3 hidden lg:table-cell">Urgencia</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-5 py-3">Estado</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-5 py-3 hidden lg:table-cell">Fotos</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-5 py-3 hidden sm:table-cell">Fecha</th>
                        <th class="text-right text-xs font-medium text-gray-500 uppercase tracking-wider px-5 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/30">
                    @forelse($reports as $report)
                    <tr class="hover:bg-gray-700/20 transition-colors">
                        {{-- Folio + operador + descripción --}}
                        <td class="px-5 py-3">
                            <p class="text-sm font-medium text-white">{{ $report->folio }}</p>
                            <p class="text-xs text-gray-500">{{ $report->operador->name }} {{ $report->operador->last_name }}</p>
                            @if($report->description)
                                <p class="text-xs text-gray-600 truncate max-w-xs">{{ $report->description }}</p>
                            @endif
                        </td>
                        {{-- Bus --}}
                        <td class="px-5 py-3 hidden md:table-cell">
                            @if($report->bus)
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-gray-700/60 border border-gray-600/40 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-white">Bus #{{ $report->bus->num_bus }}</p>
                                        <p class="text-xs text-gray-500 font-mono">{{ $report->bus->placa }}</p>
                                    </div>
                                </div>
                            @else
                                <span class="text-xs text-gray-600">—</span>
                            @endif
                        </td>
                        {{-- Urgencia --}}
                        <td class="px-5 py-3 hidden lg:table-cell">
                            @php
                                $urgBadge = match($report->urgencia) {
                                    'verde'    => 'bg-green-500/10 text-green-400 border-green-500/20',
                                    'amarillo' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                    'rojo'     => 'bg-red-500/10 text-red-400 border-red-500/20',
                                    default    => 'bg-gray-700/50 text-gray-400 border-gray-600/40',
                                };
                                $urgLabel = match($report->urgencia) {
                                    'verde'    => 'Ruta normal',
                                    'amarillo' => 'Revisión prioritaria',
                                    'rojo'     => 'Unidad detenida',
                                    default    => '—',
                                };
                            @endphp
                            <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-lg border font-medium whitespace-nowrap {{ $urgBadge }}">
                                {{ $urgLabel }}
                            </span>
                        </td>
                        {{-- Estado --}}
                        <td class="px-5 py-3">
                            @php
                                $badge = match($report->status) {
                                    'nuevo'      => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                    'en_proceso' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                    'resuelto'   => 'bg-green-500/10 text-green-400 border-green-500/20',
                                    default      => 'bg-gray-700/50 text-gray-400 border-gray-600/40',
                                };
                                $dot = match($report->status) {
                                    'nuevo'      => 'bg-blue-400',
                                    'en_proceso' => 'bg-amber-400 animate-pulse',
                                    'resuelto'   => 'bg-green-400',
                                    default      => 'bg-gray-500',
                                };
                            @endphp
                            <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-lg border font-medium {{ $badge }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $dot }}"></span>
                                {{ ucfirst(str_replace('_', ' ', $report->status)) }}
                            </span>
                        </td>
                        {{-- Fotos --}}
                        <td class="px-5 py-3 hidden lg:table-cell">
                            @if($report->photos->count() > 0)
                                <div class="flex items-center gap-1.5">
                                    {{-- Miniaturas --}}
                                    <div class="flex -space-x-2">
                                        @foreach($report->photos->take(3) as $photo)
                                            <img src="{{ Storage::url($photo->evidence_path) }}"
                                                 class="w-8 h-8 rounded-lg object-cover border-2 border-gray-800"
                                                 alt="Evidencia"/>
                                        @endforeach
                                    </div>
                                    @if($report->photos->count() > 3)
                                        <span class="text-xs text-gray-500">+{{ $report->photos->count() - 3 }}</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-xs text-gray-600">Sin fotos</span>
                            @endif
                        </td>
                        {{-- Fecha --}}
                        <td class="px-5 py-3 hidden sm:table-cell">
                            <span class="text-xs text-gray-500">{{ $report->created_at->format('d/m/Y H:i') }}</span>
                        </td>
                        {{-- Acciones --}}
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-end gap-1.5">
                                @can('ordenes_trabajo.gestionar')
                                <a href="{{ route('reports.orden.show', $report) }}"
                                   class="p-2 rounded-lg text-gray-500 hover:text-amber-400 hover:bg-amber-500/10 transition-all" title="Ver detalles">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                @endcan
                                @can('reportes.editar')
                                <a href="{{ route('reports.edit', $report) }}"
                                   class="p-2 rounded-lg text-gray-500 hover:text-white hover:bg-gray-700 transition-all" title="Editar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                @endcan
                                @can('reportes.eliminar')
                                <form method="POST" action="{{ route('reports.destroy', $report) }}"
                                      onsubmit="return confirm('¿Eliminar el reporte {{ $report->folio }}? También se eliminarán sus fotos.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg text-gray-500 hover:text-red-400 hover:bg-red-500/10 transition-all" title="Eliminar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-12 h-12 rounded-2xl bg-gray-800 border border-gray-700 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-500">No se encontraron reportes</p>
                                @if($search || $status)
                                    <a href="{{ route('reports.index') }}" class="text-xs text-red-500 hover:text-red-400">Limpiar filtros</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reports->hasPages())
            <div class="px-5 py-4 border-t border-gray-700/40 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <p class="text-xs text-gray-600">
                    Mostrando {{ $reports->firstItem() }}–{{ $reports->lastItem() }} de {{ $reports->total() }}
                </p>
                <div class="flex items-center flex-wrap gap-1 justify-center sm:justify-end">
                    @if($reports->onFirstPage())
                        <span class="px-3 py-1.5 text-xs text-gray-700 bg-gray-800/40 border border-gray-700/40 rounded-lg cursor-not-allowed">&lsaquo;</span>
                    @else
                        <a href="{{ $reports->previousPageUrl() }}" class="px-3 py-1.5 text-xs text-gray-400 bg-gray-800/40 border border-gray-700/40 rounded-lg hover:bg-gray-700 transition-colors">&lsaquo;</a>
                    @endif
                    @foreach($reports->getUrlRange(max(1, $reports->currentPage()-2), min($reports->lastPage(), $reports->currentPage()+2)) as $page => $url)
                        @if($page == $reports->currentPage())
                            <span class="brand-gradient px-3 py-1.5 text-xs text-white rounded-lg font-medium">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="px-3 py-1.5 text-xs text-gray-400 bg-gray-800/40 border border-gray-700/40 rounded-lg hover:bg-gray-700 transition-colors">{{ $page }}</a>
                        @endif
                    @endforeach
                    @if($reports->hasMorePages())
                        <a href="{{ $reports->nextPageUrl() }}" class="px-3 py-1.5 text-xs text-gray-400 bg-gray-800/40 border border-gray-700/40 rounded-lg hover:bg-gray-700 transition-colors">&rsaquo;</a>
                    @else
                        <span class="px-3 py-1.5 text-xs text-gray-700 bg-gray-800/40 border border-gray-700/40 rounded-lg cursor-not-allowed">&rsaquo;</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

</x-app-layout>
