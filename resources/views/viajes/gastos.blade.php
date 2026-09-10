<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-gray-500">
            <span>Panel</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <a href="{{ route('viajes.index') }}" class="hover:text-gray-300 transition-colors">Viajes</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-white">Gastos</span>
        </div>
    </x-slot>

    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('viajes.index') }}"
           class="flex items-center justify-center w-9 h-9 rounded-xl bg-gray-800/60 border border-gray-700/50 text-gray-400 hover:text-white hover:border-gray-600 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-white">Gastos — {{ $viaje->no_contrato }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                Bus #{{ $viaje->bus->num_bus }} · {{ $viaje->operador->name }} {{ $viaje->operador->last_name }} · {{ $viaje->origen }} &rarr; {{ $viaje->destino }}
            </p>
        </div>
        @if($viaje->liquidacion && $viaje->liquidacion->estado === 'cerrada')
            <a href="{{ route('liquidacion.pdf', $viaje->liquidacion) }}" target="_blank"
               class="flex items-center gap-2 px-4 py-2.5 bg-gray-700/60 hover:bg-gray-700 border border-gray-600/40 rounded-xl text-sm font-medium text-gray-200 transition-colors flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H8a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Hoja de liquidación
            </a>
        @endif
    </div>

    @if (session('success'))
        <div class="mb-4 flex items-center gap-3 px-4 py-3 bg-green-900/30 border border-green-700/50 rounded-xl text-green-300 text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="max-w-3xl space-y-4">
        @if($viaje->liquidacion)
            @include('liquidacion._resumen', ['liquidacion' => $viaje->liquidacion, 'editable' => false])
        @endif

        @forelse($viaje->dieselCargas as $carga)
            <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl p-5" x-data="{ rechazandoSolicitud: false, rechazandoEvidencia: false }">
                <div class="flex items-start justify-between gap-4 mb-1">
                    <div>
                        <p class="text-sm font-semibold text-white">
                            {{ $carga->tipo === 'inicial' ? 'Diésel inicial' : 'Diésel extra' }}
                            <span class="text-xs text-gray-500 font-normal">· solicitado por {{ ucfirst($carga->origen) }}</span>
                        </p>
                        <p class="text-lg font-bold text-white mt-0.5">${{ number_format($carga->monto, 2) }}</p>
                        @if($carga->fuente)
                            <p class="text-xs text-gray-500">Fuente: {{ $carga->fuente === 'gastos_entregados' ? 'Gastos entregados' : 'Transferencia' }}</p>
                        @endif
                    </div>
                    <div class="flex flex-col items-end gap-1.5 flex-shrink-0">
                        @php
                            $solicitudBadge = match($carga->estado_solicitud) {
                                'aprobada'  => 'bg-green-500/10 text-green-400 border-green-500/20',
                                'rechazada' => 'bg-red-500/10 text-red-400 border-red-500/20',
                                default     => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                            };
                        @endphp
                        <span class="text-xs px-2.5 py-1 rounded-lg border font-medium whitespace-nowrap {{ $solicitudBadge }}">
                            Solicitud: {{ ucfirst($carga->estado_solicitud) }}
                        </span>
                        @if($carga->estado_solicitud === 'aprobada')
                            @php
                                $evBadge = match($carga->estado_evidencia) {
                                    'validada'  => 'bg-green-500/10 text-green-400 border-green-500/20',
                                    'enviada'   => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                    'rechazada' => 'bg-red-500/10 text-red-400 border-red-500/20',
                                    default     => 'bg-gray-700/50 text-gray-400 border-gray-600/40',
                                };
                                $evLabel = match($carga->estado_evidencia) {
                                    'validada'  => 'Evidencia validada',
                                    'enviada'   => 'Evidencia enviada',
                                    'rechazada' => 'Evidencia rechazada',
                                    default     => 'Sin evidencia',
                                };
                            @endphp
                            <span class="text-xs px-2.5 py-1 rounded-lg border font-medium whitespace-nowrap {{ $evBadge }}">{{ $evLabel }}</span>
                        @endif
                    </div>
                </div>

                {{-- Solicitud pendiente --}}
                @if($carga->estado_solicitud === 'pendiente')
                    <div class="flex flex-wrap items-center gap-2 pt-3 mt-3 border-t border-gray-700/40">
                        <form method="POST" action="{{ route('gastos.diesel.aprobar-solicitud', $carga) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="px-3 py-1.5 bg-green-600/20 border border-green-600/40 text-green-400 text-xs font-medium rounded-lg hover:bg-green-600/30 transition-colors">
                                Aprobar solicitud
                            </button>
                        </form>
                        <button type="button" @click="rechazandoSolicitud = !rechazandoSolicitud"
                                class="px-3 py-1.5 bg-red-600/10 border border-red-600/30 text-red-400 text-xs font-medium rounded-lg hover:bg-red-600/20 transition-colors">
                            Rechazar
                        </button>
                    </div>
                    <div x-show="rechazandoSolicitud" x-cloak class="mt-3">
                        <form method="POST" action="{{ route('gastos.diesel.rechazar-solicitud', $carga) }}" class="flex flex-wrap gap-2">
                            @csrf @method('PATCH')
                            <input type="text" name="motivo_rechazo" placeholder="Motivo (opcional)"
                                   class="flex-1 min-w-0 px-3 py-2 bg-gray-900/80 border border-gray-700 rounded-lg text-white text-xs placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500"/>
                            <button type="submit" class="px-3 py-2 bg-red-600 text-white text-xs font-medium rounded-lg hover:bg-red-700 transition-colors">
                                Confirmar rechazo
                            </button>
                        </form>
                    </div>
                @elseif($carga->estado_solicitud === 'rechazada' && $carga->motivo_rechazo)
                    <p class="text-xs text-red-400 pt-3 mt-3 border-t border-gray-700/40">Motivo: {{ $carga->motivo_rechazo }}</p>
                @endif

                {{-- Evidencia --}}
                @if(in_array($carga->estado_evidencia, ['enviada', 'validada']))
                    <div class="pt-3 mt-3 border-t border-gray-700/40">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Ticket / comprobante</p>
                                <a href="{{ Storage::url($carga->ticket_path) }}" target="_blank">
                                    <img src="{{ Storage::url($carga->ticket_path) }}" class="w-full h-32 object-cover rounded-lg border border-gray-700/50"/>
                                </a>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Litros cargados: {{ number_format($carga->litros, 2) }} L</p>
                                <a href="{{ Storage::url($carga->foto_litros_path) }}" target="_blank">
                                    <img src="{{ Storage::url($carga->foto_litros_path) }}" class="w-full h-32 object-cover rounded-lg border border-gray-700/50"/>
                                </a>
                            </div>
                        </div>
                        @if($carga->estado_evidencia === 'enviada')
                            <div class="flex flex-wrap items-center gap-2">
                                <form method="POST" action="{{ route('gastos.diesel.aprobar-evidencia', $carga) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="px-3 py-1.5 bg-green-600/20 border border-green-600/40 text-green-400 text-xs font-medium rounded-lg hover:bg-green-600/30 transition-colors">
                                        Validar evidencia
                                    </button>
                                </form>
                                <button type="button" @click="rechazandoEvidencia = !rechazandoEvidencia"
                                        class="px-3 py-1.5 bg-red-600/10 border border-red-600/30 text-red-400 text-xs font-medium rounded-lg hover:bg-red-600/20 transition-colors">
                                    Rechazar evidencia
                                </button>
                            </div>
                            <div x-show="rechazandoEvidencia" x-cloak class="mt-3">
                                <form method="POST" action="{{ route('gastos.diesel.rechazar-evidencia', $carga) }}" class="flex flex-wrap gap-2">
                                    @csrf @method('PATCH')
                                    <input type="text" name="motivo_rechazo" placeholder="Motivo (opcional)"
                                           class="flex-1 min-w-0 px-3 py-2 bg-gray-900/80 border border-gray-700 rounded-lg text-white text-xs placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500"/>
                                    <button type="submit" class="px-3 py-2 bg-red-600 text-white text-xs font-medium rounded-lg hover:bg-red-700 transition-colors">
                                        Confirmar rechazo
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                @elseif($carga->estado_solicitud === 'aprobada' && $carga->estado_evidencia === 'sin_evidencia')
                    <p class="text-xs text-gray-600 pt-3 mt-3 border-t border-gray-700/40">Esperando que el operador suba la evidencia.</p>
                @elseif($carga->estado_evidencia === 'rechazada')
                    <p class="text-xs text-red-400 pt-3 mt-3 border-t border-gray-700/40">
                        Evidencia rechazada{{ $carga->motivo_rechazo ? ': '.$carga->motivo_rechazo : '' }}. Esperando que el operador la vuelva a subir.
                    </p>
                @endif
            </div>
        @empty
            <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl p-10 text-center">
                <p class="text-sm text-gray-500">Este viaje aún no tiene gastos de diésel registrados.</p>
            </div>
        @endforelse

        {{-- Registrar diésel extra ya autorizado por administración --}}
        <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl p-5" x-data="{ open: false }">
            <button type="button" @click="open = !open" class="flex items-center gap-2 text-sm font-semibold text-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Registrar diésel extra ya autorizado
            </button>
            <div x-show="open" x-cloak class="mt-4">
                <form method="POST" action="{{ route('viajes.gastos.diesel-extra.store', $viaje) }}" class="flex flex-wrap items-end gap-3">
                    @csrf
                    <div class="flex-1 min-w-[140px]">
                        <label class="block text-xs font-medium text-gray-400 mb-1.5">Monto</label>
                        <input type="number" step="0.01" min="0" name="monto"
                               class="w-full px-3.5 py-2.5 bg-gray-900/80 border border-gray-700 rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                               placeholder="0.00"/>
                    </div>
                    <button type="submit" class="btn-login-gradient px-5 py-2.5 text-white text-sm font-semibold rounded-xl shadow-lg shadow-red-950/40">
                        Registrar
                    </button>
                </form>
                <p class="mt-2 text-xs text-gray-600">Úsalo cuando administración ya puso el diésel extra directamente. El operador solo deberá anexar la evidencia, sin aprobación previa.</p>
            </div>
        </div>

        @if($viaje->liquidacion)
            <div>
                <h3 class="text-sm font-semibold text-white mb-3">Otros gastos</h3>
                @if($viaje->liquidacion->estado === 'cerrada')
                    @include('liquidacion._gastos_list', ['liquidacion' => $viaje->liquidacion, 'context' => 'admin'])
                @else
                    <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl p-6 text-center">
                        <p class="text-sm text-gray-500">Liquidación aún abierta, esperando que el operador la cierre.</p>
                    </div>
                @endif
            </div>
        @endif
    </div>

</x-app-layout>
