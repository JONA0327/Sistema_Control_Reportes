<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-gray-500">
            <span>Panel</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-white">Gastos</span>
        </div>
    </x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white">Gastos</h1>
        <p class="text-sm text-gray-500 mt-0.5">Registra el km, el diésel y los gastos de tus viajes, y cierra la liquidación cuando termines</p>
    </div>

    @if (session('success'))
        <div class="mb-4 flex items-center gap-3 px-4 py-3 bg-green-900/30 border border-green-700/50 rounded-xl text-green-300 text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="max-w-3xl space-y-6">
        @forelse($viajes as $viaje)
            <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl overflow-hidden">
                {{-- Encabezado del viaje --}}
                <div class="px-6 py-4 border-b border-gray-700/40 flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-white">{{ $viaje->no_contrato }} · Bus #{{ $viaje->bus->num_bus }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $viaje->origen }} &rarr; {{ $viaje->destino }} · {{ $viaje->fecha_salida->format('d/m/Y') }}</p>
                    </div>
                    @if($viaje->liquidacion && $viaje->liquidacion->estado === 'abierta')
                        <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-lg border font-medium bg-amber-500/10 text-amber-400 border-amber-500/20 flex-shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                            En curso
                        </span>
                    @endif
                </div>

                <div class="p-6 space-y-4">
                    @if($viaje->liquidacion)
                        @include('liquidacion._resumen', ['liquidacion' => $viaje->liquidacion, 'editable' => $viaje->liquidacion->estado === 'abierta'])
                    @endif

                    @foreach($viaje->dieselCargas as $carga)
                        <div class="border border-gray-700/40 rounded-xl p-4" x-data="{}">
                            <div class="flex items-start justify-between gap-4 mb-1">
                                <div>
                                    <p class="text-sm font-semibold text-white">
                                        {{ $carga->tipo === 'inicial' ? 'Diésel inicial' : 'Diésel extra' }}
                                    </p>
                                    <p class="text-base font-bold text-white mt-0.5">${{ number_format($carga->monto, 2) }}</p>
                                </div>
                                @php
                                    $solicitudBadge = match($carga->estado_solicitud) {
                                        'aprobada'  => 'bg-green-500/10 text-green-400 border-green-500/20',
                                        'rechazada' => 'bg-red-500/10 text-red-400 border-red-500/20',
                                        default     => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                    };
                                @endphp
                                <span class="text-xs px-2.5 py-1 rounded-lg border font-medium whitespace-nowrap flex-shrink-0 {{ $solicitudBadge }}">
                                    {{ ucfirst($carga->estado_solicitud) }}
                                </span>
                            </div>

                            @if($carga->estado_solicitud === 'pendiente')
                                <p class="text-xs text-gray-500 mt-2">Esperando la aprobación de administración.</p>
                            @elseif($carga->estado_solicitud === 'rechazada')
                                <p class="text-xs text-red-400 mt-2">Solicitud rechazada{{ $carga->motivo_rechazo ? ': '.$carga->motivo_rechazo : '' }}.</p>
                            @elseif($carga->estado_solicitud === 'aprobada')
                                @if($carga->estado_evidencia === 'validada')
                                    <div class="mt-3 pt-3 border-t border-gray-700/30 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <p class="text-xs text-gray-500 mb-1">Ticket</p>
                                            <img src="{{ Storage::url($carga->ticket_path) }}" class="w-full h-24 object-cover rounded-lg border border-gray-700/50"/>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 mb-1">Litros: {{ number_format($carga->litros, 2) }} L @if($carga->costo_litro) · ${{ number_format($carga->costo_litro, 2) }}/L @endif</p>
                                            <img src="{{ Storage::url($carga->foto_litros_path) }}" class="w-full h-24 object-cover rounded-lg border border-gray-700/50"/>
                                        </div>
                                    </div>
                                    <p class="text-xs text-green-400 mt-2">Evidencia validada por administración.</p>
                                @elseif($carga->estado_evidencia === 'enviada')
                                    <div class="mt-3 pt-3 border-t border-gray-700/30 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <p class="text-xs text-gray-500 mb-1">Ticket</p>
                                            <img src="{{ Storage::url($carga->ticket_path) }}" class="w-full h-24 object-cover rounded-lg border border-gray-700/50"/>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 mb-1">Litros: {{ number_format($carga->litros, 2) }} L @if($carga->costo_litro) · ${{ number_format($carga->costo_litro, 2) }}/L @endif</p>
                                            <img src="{{ Storage::url($carga->foto_litros_path) }}" class="w-full h-24 object-cover rounded-lg border border-gray-700/50"/>
                                        </div>
                                    </div>
                                    <p class="text-xs text-blue-400 mt-2">Evidencia enviada, esperando validación de administración.</p>
                                @else
                                    {{-- sin_evidencia o rechazada: mostrar formulario para subir/reenviar evidencia --}}
                                    @if($carga->estado_evidencia === 'rechazada')
                                        <p class="text-xs text-red-400 mt-2 mb-3">Evidencia rechazada{{ $carga->motivo_rechazo ? ': '.$carga->motivo_rechazo : '' }}. Vuelve a subirla.</p>
                                    @endif
                                    <form method="POST" action="{{ route('gastos.diesel.evidencia.store', $carga) }}" enctype="multipart/form-data" class="mt-3 pt-3 border-t border-gray-700/30 space-y-3">
                                        @csrf
                                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-400 mb-1.5">Litros cargados <span class="text-red-500">*</span></label>
                                                <input type="number" step="0.01" min="0" name="litros" required
                                                       class="w-full px-3 py-2 bg-gray-900/80 border border-gray-700 rounded-lg text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500"/>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-400 mb-1.5">Costo por litro <span class="text-red-500">*</span></label>
                                                <input type="number" step="0.01" min="0" name="costo_litro" required
                                                       class="w-full px-3 py-2 bg-gray-900/80 border border-gray-700 rounded-lg text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500"/>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-400 mb-1.5">Foto del ticket <span class="text-red-500">*</span></label>
                                                <input type="file" name="ticket" accept="image/*" required
                                                       class="w-full text-xs text-gray-400 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-gray-700 file:text-gray-200 hover:file:bg-gray-600"/>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-400 mb-1.5">Foto de litros en máquina <span class="text-red-500">*</span></label>
                                                <input type="file" name="foto_litros" accept="image/*" required
                                                       class="w-full text-xs text-gray-400 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-gray-700 file:text-gray-200 hover:file:bg-gray-600"/>
                                            </div>
                                        </div>
                                        <button type="submit" class="px-4 py-2 brand-gradient text-white text-xs font-semibold rounded-lg shadow-lg shadow-red-950/40 hover:opacity-90 transition-opacity">
                                            Enviar evidencia
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    @endforeach

                    {{-- Solicitar diésel extra --}}
                    <div class="border border-dashed border-gray-700 rounded-xl p-4" x-data="{ open: false }">
                        <button type="button" @click="open = !open" class="flex items-center gap-2 text-sm font-medium text-gray-300 hover:text-white transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Solicitar diésel extra
                        </button>
                        <div x-show="open" x-cloak>
                            <form method="POST" action="{{ route('gastos.diesel-extra.store', $viaje) }}" class="mt-4 space-y-3">
                                @csrf
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-400 mb-1.5">Monto <span class="text-red-500">*</span></label>
                                        <input type="number" step="0.01" min="0" name="monto" required
                                               class="w-full px-3 py-2 bg-gray-900/80 border border-gray-700 rounded-lg text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500"
                                               placeholder="0.00"/>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-400 mb-1.5">Fuente <span class="text-red-500">*</span></label>
                                        <div class="grid grid-cols-2 gap-2">
                                            <label class="relative cursor-pointer">
                                                <input type="radio" name="fuente" value="gastos_entregados" class="sr-only peer" checked>
                                                <div class="px-3 py-2 rounded-lg border border-gray-700 bg-gray-900/40 peer-checked:border-red-500 peer-checked:bg-red-600/10 transition-all text-center">
                                                    <span class="text-xs text-gray-300">Gastos entregados</span>
                                                </div>
                                            </label>
                                            <label class="relative cursor-pointer">
                                                <input type="radio" name="fuente" value="transferencia" class="sr-only peer">
                                                <div class="px-3 py-2 rounded-lg border border-gray-700 bg-gray-900/40 peer-checked:border-red-500 peer-checked:bg-red-600/10 transition-all text-center">
                                                    <span class="text-xs text-gray-300">Transferencia</span>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="px-4 py-2 brand-gradient text-white text-xs font-semibold rounded-lg shadow-lg shadow-red-950/40 hover:opacity-90 transition-opacity">
                                    Enviar solicitud
                                </button>
                            </form>
                        </div>
                    </div>

                    @if($viaje->liquidacion)
                        {{-- Otros gastos (casetas, hospedaje, comidas, estacionamiento, lavada, otros) --}}
                        <div>
                            <h3 class="text-sm font-semibold text-white mb-3">Otros gastos</h3>
                            <div class="space-y-3">
                                @include('liquidacion._gastos_list', ['liquidacion' => $viaje->liquidacion, 'context' => 'operador'])
                                @if($viaje->liquidacion->estado === 'abierta')
                                    @include('liquidacion._gasto_form', ['liquidacion' => $viaje->liquidacion])
                                @endif
                            </div>
                        </div>

                        @if($viaje->liquidacion->estado === 'abierta')
                            <form method="POST" action="{{ route('liquidacion.cerrar', $viaje->liquidacion) }}"
                                  onsubmit="return confirm('¿Cerrar la liquidación de este viaje? Ya no podrás agregar ni eliminar gastos.')">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full px-4 py-2.5 btn-login-gradient text-white text-sm font-semibold rounded-xl shadow-lg shadow-red-950/40">
                                    Cerrar liquidación
                                </button>
                            </form>
                        @endif
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl p-10 text-center">
                <p class="text-sm text-gray-500">Aún no tienes viajes asignados.</p>
            </div>
        @endforelse
    </div>

</x-app-layout>
