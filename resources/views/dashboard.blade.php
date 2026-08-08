<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-gray-500">
            <span>Panel</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-white">Dashboard</span>
        </div>
    </x-slot>

    {{-- Encabezado de página --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-white">Panel Administrativo</h1>
            <p class="text-sm text-gray-500 mt-0.5">Resumen general del sistema</p>
        </div>
        <div class="flex items-center gap-2 text-xs text-gray-600 bg-gray-800/50 border border-gray-700/50 rounded-xl px-3 py-2">
            <svg class="w-3.5 h-3.5 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            {{ now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- ─── Viajes activos ───────────────────────────────────── --}}
        <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-700/40 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-600/20 border border-blue-500/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-white">Viajes activos</h3>
                        <p class="text-xs text-gray-500">En curso ahora mismo</p>
                    </div>
                </div>
            </div>

            <div class="divide-y divide-gray-700/30">
                @forelse($viajesActivos as $viaje)
                    <div class="px-5 py-3">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-sm font-medium text-white truncate">{{ $viaje->no_contrato }}</p>
                            <span class="inline-flex items-center gap-1.5 text-xs px-2 py-0.5 rounded-lg border font-medium bg-amber-500/10 text-amber-400 border-amber-500/20 flex-shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                En curso
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 truncate">
                            Bus #{{ $viaje->bus->num_bus ?? '—' }} · {{ $viaje->operador->name ?? '' }} {{ $viaje->operador->last_name ?? '' }}
                        </p>
                        <p class="text-xs text-gray-600 truncate">{{ $viaje->origen }} &rarr; {{ $viaje->destino }}</p>
                    </div>
                @empty
                    <p class="px-5 py-6 text-center text-xs text-gray-600">No hay viajes activos.</p>
                @endforelse
            </div>

            @unlessrole('mecanico')
                <div class="px-5 py-3 border-t border-gray-700/40">
                    <a href="{{ route('viajes.index') }}" class="text-xs text-red-500 hover:text-red-400 font-medium">Ver todos los viajes</a>
                </div>
            @endunlessrole
        </div>

        {{-- ─── Gastos cerrados recientemente ────────────────────── --}}
        <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-700/40 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-green-600/20 border border-green-500/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-white">Gastos cerrados</h3>
                        <p class="text-xs text-gray-500">Liquidaciones recién cerradas</p>
                    </div>
                </div>
            </div>

            <div class="divide-y divide-gray-700/30">
                @forelse($gastosCerrados as $liquidacion)
                    @php $resumen = $liquidacion->resumen(); @endphp
                    <div class="px-5 py-3">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-sm font-medium text-white truncate">{{ $liquidacion->viaje->no_contrato ?? '—' }}</p>
                            <span class="text-xs text-gray-600 flex-shrink-0">{{ $liquidacion->cerrada_at?->diffForHumans() }}</span>
                        </div>
                        <p class="text-xs text-gray-500 truncate">Bus #{{ $liquidacion->viaje->bus->num_bus ?? '—' }}</p>
                        <div class="flex items-center gap-3 mt-1">
                            <span class="text-xs text-gray-600">Gastado: <span class="text-gray-300">${{ number_format($resumen['total_gastos_realizados'], 2) }}</span></span>
                            @if($resumen['exceso_gasto'] > 0)
                                <span class="text-xs text-red-400">Exceso: ${{ number_format($resumen['exceso_gasto'], 2) }}</span>
                            @else
                                <span class="text-xs text-green-400">Sobrante: ${{ number_format($resumen['sobrante_a_devolver'], 2) }}</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="px-5 py-6 text-center text-xs text-gray-600">Aún no se ha cerrado ninguna liquidación.</p>
                @endforelse
            </div>

            @hasanyrole('administrador|administracion')
                <div class="px-5 py-3 border-t border-gray-700/40">
                    <a href="{{ route('viajes.index') }}" class="text-xs text-red-500 hover:text-red-400 font-medium">Ver todos los viajes</a>
                </div>
            @endhasanyrole
        </div>

        {{-- ─── Reportes de camiones ──────────────────────────────── --}}
        <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-700/40 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-red-600/20 border border-red-500/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-white">Reportes</h3>
                        <p class="text-xs text-gray-500">Fallas reportadas en camiones</p>
                    </div>
                </div>
            </div>

            <div class="divide-y divide-gray-700/30">
                @forelse($reportesRecientes as $report)
                    @php
                        $badge = match($report->status) {
                            'nuevo'      => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                            'en_proceso' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                            'resuelto'   => 'bg-green-500/10 text-green-400 border-green-500/20',
                            default      => 'bg-gray-700/50 text-gray-400 border-gray-600/40',
                        };
                    @endphp
                    <div class="px-5 py-3">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-sm font-medium text-white truncate">{{ $report->folio }}</p>
                            <span class="inline-flex items-center gap-1.5 text-xs px-2 py-0.5 rounded-lg border font-medium flex-shrink-0 {{ $badge }}">
                                {{ ucfirst(str_replace('_', ' ', $report->status)) }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 truncate">
                            Bus #{{ $report->bus->num_bus ?? '—' }} · {{ $report->operador->name ?? '' }} {{ $report->operador->last_name ?? '' }}
                        </p>
                        <p class="text-xs text-gray-600">{{ $report->created_at->diffForHumans() }}</p>
                    </div>
                @empty
                    <p class="px-5 py-6 text-center text-xs text-gray-600">No hay reportes registrados.</p>
                @endforelse
            </div>

            <div class="px-5 py-3 border-t border-gray-700/40">
                <a href="{{ route('reports.index') }}" class="text-xs text-red-500 hover:text-red-400 font-medium">Ver todos los reportes</a>
            </div>
        </div>
    </div>

</x-app-layout>
