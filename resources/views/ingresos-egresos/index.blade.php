<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-gray-500">
            <span>Panel</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-white">Egresos y Ingresos</span>
        </div>
    </x-slot>

    {{-- Encabezado --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Egresos y Ingresos</h1>
            <p class="text-sm text-gray-500 mt-0.5">Control de movimientos financieros de la empresa</p>
        </div>
        <a href="{{ route('ingresos-egresos.create') }}"
           class="brand-gradient inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-white text-sm font-semibold shadow-lg shadow-red-950/40 hover:opacity-90 transition-opacity">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo movimiento
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

    {{-- Tarjetas resumen general --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
        <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider font-medium">Total ingresos</p>
            <p class="text-2xl font-bold text-green-400 mt-1">${{ number_format($totalIngresos, 2) }}</p>
        </div>
        <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider font-medium">Total egresos</p>
            <p class="text-2xl font-bold text-red-400 mt-1">${{ number_format($totalEgresos, 2) }}</p>
        </div>
        <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider font-medium">Balance</p>
            <p class="text-2xl font-bold {{ $balance >= 0 ? 'text-white' : 'text-red-400' }} mt-1">${{ number_format($balance, 2) }}</p>
        </div>
    </div>

    {{-- Dividido por país (la IA clasifica automáticamente según el destino) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        @foreach($resumenPorPais as $codigoPais => $totales)
            @php $balancePais = $totales['ingresos'] - $totales['egresos']; @endphp
            <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl p-5">
                <p class="text-sm font-semibold text-white mb-3 flex items-center gap-2">
                    <span>{{ $codigoPais === 'usa' ? '🇺🇸' : '🇲🇽' }}</span>
                    {{ \App\Models\IngresoEgreso::PAISES[$codigoPais] }}
                </p>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <p class="text-xs text-gray-500">Ingresos</p>
                        <p class="text-sm font-bold text-green-400 mt-0.5">${{ number_format($totales['ingresos'], 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Egresos</p>
                        <p class="text-sm font-bold text-red-400 mt-0.5">${{ number_format($totales['egresos'], 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Balance</p>
                        <p class="text-sm font-bold {{ $balancePais >= 0 ? 'text-white' : 'text-red-400' }} mt-0.5">${{ number_format($balancePais, 2) }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Tarjeta principal --}}
    <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl overflow-hidden">

        {{-- Barra de búsqueda y filtro --}}
        <div class="px-5 py-4 border-b border-gray-700/40 flex flex-col sm:flex-row sm:items-center gap-3">
            <form method="GET" action="{{ route('ingresos-egresos.index') }}" class="flex-1 flex flex-wrap gap-2">
                <div class="relative flex-1 max-w-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ $search }}"
                           placeholder="Buscar por concepto o categoría..."
                           class="w-full pl-9 pr-4 py-2 bg-gray-900/80 border border-gray-700 rounded-xl text-sm text-white placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"/>
                </div>
                <select name="tipo" onchange="this.form.submit()"
                        class="px-3 py-2 bg-gray-900/80 border border-gray-700 rounded-xl text-sm text-white focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                    <option value="" class="bg-gray-900">Todos los tipos</option>
                    <option value="ingreso" class="bg-gray-900" {{ $tipo === 'ingreso' ? 'selected' : '' }}>Ingresos</option>
                    <option value="egreso" class="bg-gray-900" {{ $tipo === 'egreso' ? 'selected' : '' }}>Egresos</option>
                </select>
                <select name="pais" onchange="this.form.submit()"
                        class="px-3 py-2 bg-gray-900/80 border border-gray-700 rounded-xl text-sm text-white focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                    <option value="" class="bg-gray-900">Todos los países</option>
                    @foreach(\App\Models\IngresoEgreso::PAISES as $codigoPais => $nombrePais)
                        <option value="{{ $codigoPais }}" class="bg-gray-900" {{ $pais === $codigoPais ? 'selected' : '' }}>{{ $nombrePais }}</option>
                    @endforeach
                </select>
                <button type="submit"
                        class="px-4 py-2 bg-gray-700/60 border border-gray-600/40 text-gray-300 text-sm rounded-xl hover:bg-gray-700 transition-colors">
                    Buscar
                </button>
                @if($search || $tipo || $pais)
                    <a href="{{ route('ingresos-egresos.index') }}"
                       class="px-4 py-2 bg-gray-700/30 border border-gray-600/30 text-gray-500 text-sm rounded-xl hover:text-gray-300 transition-colors">
                        Limpiar
                    </a>
                @endif
            </form>
            <p class="text-xs text-gray-600">
                {{ $movimientos->total() }} movimiento(s) encontrado(s)
            </p>
        </div>

        {{-- Tabla --}}
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-700/40">
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-5 py-3">Fecha</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-5 py-3">Concepto</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-5 py-3 hidden md:table-cell">Categoría</th>
                        <th class="text-center text-xs font-medium text-gray-500 uppercase tracking-wider px-5 py-3 hidden sm:table-cell">País</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-5 py-3 hidden lg:table-cell">Registrado por</th>
                        <th class="text-right text-xs font-medium text-gray-500 uppercase tracking-wider px-5 py-3">Monto</th>
                        <th class="text-right text-xs font-medium text-gray-500 uppercase tracking-wider px-5 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/30">
                    @forelse($movimientos as $movimiento)
                    <tr class="hover:bg-gray-700/20 transition-colors group">
                        <td class="px-5 py-3">
                            <span class="text-sm text-gray-400">{{ $movimiento->fecha->format('d/m/Y') }}</span>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-lg font-medium flex-shrink-0
                                    {{ $movimiento->tipo === 'ingreso'
                                        ? 'bg-green-500/10 text-green-400 border border-green-500/20'
                                        : 'bg-red-500/10 text-red-400 border border-red-500/20' }}">
                                    {{ $movimiento->tipo === 'ingreso' ? 'Ingreso' : 'Egreso' }}
                                </span>
                                <span class="text-sm font-medium text-white truncate">{{ $movimiento->concepto }}</span>
                                @if($movimiento->automatico)
                                    <span class="inline-flex items-center gap-1 text-xs px-1.5 py-0.5 rounded border border-gray-700 text-gray-500 flex-shrink-0" title="Generado automáticamente por el sistema">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                        </svg>
                                        Auto
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-3 hidden md:table-cell">
                            <span class="text-sm text-gray-400">{{ $movimiento->categoria ?: '—' }}</span>
                        </td>
                        <td class="px-5 py-3 hidden sm:table-cell text-center" title="{{ \App\Models\IngresoEgreso::PAISES[$movimiento->pais] ?? 'México' }}">
                            <span class="text-lg">{{ $movimiento->pais === 'usa' ? '🇺🇸' : '🇲🇽' }}</span>
                        </td>
                        <td class="px-5 py-3 hidden lg:table-cell">
                            <span class="text-sm text-gray-400">{{ $movimiento->user?->name }}</span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <span class="text-sm font-bold {{ $movimiento->tipo === 'ingreso' ? 'text-green-400' : 'text-red-400' }}">
                                {{ $movimiento->tipo === 'ingreso' ? '+' : '-' }}${{ number_format($movimiento->monto, 2) }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-end gap-1.5">
                                @if($movimiento->comprobante_path)
                                    <a href="{{ Storage::url($movimiento->comprobante_path) }}" target="_blank"
                                       class="p-2 rounded-lg text-gray-500 hover:text-white hover:bg-gray-700 transition-all"
                                       title="Ver comprobante">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                    </a>
                                @endif
                                @if($movimiento->automatico)
                                    <span class="p-2 text-gray-700 cursor-not-allowed" title="Generado automáticamente. Se administra desde su origen (contrato, viaje o diésel).">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                    </span>
                                @else
                                    <a href="{{ route('ingresos-egresos.edit', $movimiento) }}"
                                       class="p-2 rounded-lg text-gray-500 hover:text-white hover:bg-gray-700 transition-all"
                                       title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('ingresos-egresos.destroy', $movimiento) }}"
                                          onsubmit="return confirm('¿Eliminar el movimiento \'{{ addslashes($movimiento->concepto) }}\'? Esta acción no se puede deshacer.')">
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
                                @endif
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
                                              d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-500">No se encontraron movimientos</p>
                                @if($search || $tipo || $pais)
                                    <a href="{{ route('ingresos-egresos.index') }}" class="text-xs text-red-500 hover:text-red-400">Limpiar filtros</a>
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
                        <span class="px-3 py-1.5 text-xs text-gray-700 bg-gray-800/40 border border-gray-700/40 rounded-lg cursor-not-allowed">
                            &lsaquo;
                        </span>
                    @else
                        <a href="{{ $movimientos->previousPageUrl() }}"
                           class="px-3 py-1.5 text-xs text-gray-400 bg-gray-800/40 border border-gray-700/40 rounded-lg hover:bg-gray-700 transition-colors">
                            &lsaquo;
                        </a>
                    @endif

                    @foreach($movimientos->getUrlRange(max(1, $movimientos->currentPage()-2), min($movimientos->lastPage(), $movimientos->currentPage()+2)) as $page => $url)
                        @if($page == $movimientos->currentPage())
                            <span class="brand-gradient px-3 py-1.5 text-xs text-white rounded-lg font-medium">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}"
                               class="px-3 py-1.5 text-xs text-gray-400 bg-gray-800/40 border border-gray-700/40 rounded-lg hover:bg-gray-700 transition-colors">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach

                    @if($movimientos->hasMorePages())
                        <a href="{{ $movimientos->nextPageUrl() }}"
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
