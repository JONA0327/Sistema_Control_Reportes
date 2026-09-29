<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-gray-500">
            <span>Panel</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-white">Movimientos</span>
        </div>
    </x-slot>

    {{-- Encabezado --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white">Movimientos</h1>
        <p class="text-sm text-gray-500 mt-0.5">Historial de creaciones, ediciones, bajas y cancelaciones de anticipos, inventario, reportes y contratos</p>
    </div>

    {{-- Tarjeta principal --}}
    <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl overflow-hidden">

        {{-- Filtros --}}
        <div class="px-5 py-4 border-b border-gray-700/40 flex flex-col lg:flex-row lg:items-center gap-3">
            <form method="GET" action="{{ route('movimientos.index') }}" class="flex-1 flex flex-col sm:flex-row gap-2">
                <div class="relative flex-1 max-w-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ $search }}"
                           placeholder="Buscar en la descripción..."
                           class="w-full pl-9 pr-4 py-2 bg-gray-900/80 border border-gray-700 rounded-xl text-sm text-white placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"/>
                </div>
                <select name="modulo" onchange="this.form.submit()"
                        class="px-3 py-2 bg-gray-900/80 border border-gray-700 rounded-xl text-sm text-white focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                    <option value="" class="bg-gray-900">Todos los módulos</option>
                    @foreach(\App\Models\Movimiento::MODULOS as $key => $label)
                        <option value="{{ $key }}" class="bg-gray-900" {{ $modulo === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="accion" onchange="this.form.submit()"
                        class="px-3 py-2 bg-gray-900/80 border border-gray-700 rounded-xl text-sm text-white focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                    <option value="" class="bg-gray-900">Todas las acciones</option>
                    @foreach(\App\Models\Movimiento::ACCIONES as $key => $label)
                        <option value="{{ $key }}" class="bg-gray-900" {{ $accion === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit"
                        class="px-4 py-2 bg-gray-700/60 border border-gray-600/40 text-gray-300 text-sm rounded-xl hover:bg-gray-700 transition-colors">
                    Buscar
                </button>
                @if($search || $modulo || $accion)
                    <a href="{{ route('movimientos.index') }}"
                       class="px-4 py-2 bg-gray-700/30 border border-gray-600/30 text-gray-500 text-sm rounded-xl hover:text-gray-300 transition-colors text-center">
                        Limpiar
                    </a>
                @endif
            </form>
            <p class="text-xs text-gray-600 whitespace-nowrap">
                {{ $movimientos->total() }} movimiento(s)
            </p>
        </div>

        {{-- Tabla --}}
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-700/40">
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-5 py-3">Fecha</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-5 py-3">Módulo</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-5 py-3">Acción</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-5 py-3">Descripción</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-5 py-3 hidden lg:table-cell">Usuario</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/30">
                    @forelse($movimientos as $mov)
                        @php
                            $badge = match($mov->accion) {
                                'creado' => 'bg-green-500/10 text-green-400 border-green-500/20',
                                'editado' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                'eliminado' => 'bg-red-500/10 text-red-400 border-red-500/20',
                                'cancelado' => 'bg-red-500/10 text-red-400 border-red-500/20',
                                'estado_cambiado' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                'rechazado' => 'bg-red-500/10 text-red-400 border-red-500/20',
                                'aprobado' => 'bg-green-500/10 text-green-400 border-green-500/20',
                                default => 'bg-gray-700/50 text-gray-400 border-gray-600/40',
                            };
                        @endphp
                        <tr class="hover:bg-gray-700/20 transition-colors">
                            <td class="px-5 py-3">
                                <span class="text-xs text-gray-400 whitespace-nowrap">{{ $mov->created_at->format('d/m/Y H:i') }}</span>
                            </td>
                            <td class="px-5 py-3">
                                <span class="text-xs px-2 py-0.5 rounded-lg bg-gray-700/50 text-gray-300">{{ $mov->modulo_label }}</span>
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center text-xs px-2.5 py-1 rounded-lg font-medium border {{ $badge }}">
                                    {{ $mov->accion_label }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <p class="text-sm text-gray-200">{{ $mov->descripcion }}</p>
                                @if($mov->motivo)
                                    <p class="text-xs text-gray-500 mt-0.5">Motivo: {{ $mov->motivo }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-3 hidden lg:table-cell">
                                <span class="text-sm text-gray-400">{{ $mov->user->name ?? 'Sistema' }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-12 h-12 rounded-2xl bg-gray-800 border border-gray-700 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm text-gray-500">No se encontraron movimientos</p>
                                    @if($search || $modulo || $accion)
                                        <a href="{{ route('movimientos.index') }}" class="text-xs text-red-500 hover:text-red-400">Limpiar filtros</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($movimientos->hasPages())
            <div class="px-5 py-4 border-t border-gray-700/40 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <p class="text-xs text-gray-600">
                    Mostrando {{ $movimientos->firstItem() }}–{{ $movimientos->lastItem() }} de {{ $movimientos->total() }}
                </p>
                <div class="flex items-center flex-wrap gap-1 justify-center sm:justify-end">
                    @if($movimientos->onFirstPage())
                        <span class="px-3 py-1.5 text-xs text-gray-700 bg-gray-800/40 border border-gray-700/40 rounded-lg cursor-not-allowed">&lsaquo;</span>
                    @else
                        <a href="{{ $movimientos->previousPageUrl() }}" class="px-3 py-1.5 text-xs text-gray-400 bg-gray-800/40 border border-gray-700/40 rounded-lg hover:bg-gray-700 transition-colors">&lsaquo;</a>
                    @endif

                    @foreach($movimientos->getUrlRange(max(1, $movimientos->currentPage()-2), min($movimientos->lastPage(), $movimientos->currentPage()+2)) as $page => $url)
                        @if($page == $movimientos->currentPage())
                            <span class="brand-gradient px-3 py-1.5 text-xs text-white rounded-lg font-medium">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="px-3 py-1.5 text-xs text-gray-400 bg-gray-800/40 border border-gray-700/40 rounded-lg hover:bg-gray-700 transition-colors">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if($movimientos->hasMorePages())
                        <a href="{{ $movimientos->nextPageUrl() }}" class="px-3 py-1.5 text-xs text-gray-400 bg-gray-800/40 border border-gray-700/40 rounded-lg hover:bg-gray-700 transition-colors">&rsaquo;</a>
                    @else
                        <span class="px-3 py-1.5 text-xs text-gray-700 bg-gray-800/40 border border-gray-700/40 rounded-lg cursor-not-allowed">&rsaquo;</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

</x-app-layout>
