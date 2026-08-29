<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-gray-500">
            <span>Panel</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <a href="{{ route('contratos.index') }}" class="hover:text-gray-300 transition-colors">Contratos</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-white">{{ $contrato->folio }}</span>
        </div>
    </x-slot>

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('contratos.index') }}"
               class="flex items-center justify-center w-9 h-9 rounded-xl bg-gray-800/60 border border-gray-700/50 text-gray-400 hover:text-white hover:border-gray-600 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-white">Editar contrato {{ $contrato->folio }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ $contrato->cliente_nombre }}</p>
            </div>
        </div>
        <a href="{{ route('contratos.pdf', $contrato) }}" target="_blank"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-800/60 border border-gray-700/50 text-gray-300 text-sm font-medium rounded-xl hover:bg-gray-700 hover:text-white transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Ver PDF
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 max-w-3xl flex items-center gap-3 px-4 py-3 bg-green-900/30 border border-green-700/50 rounded-xl text-green-300 text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="max-w-3xl space-y-4">
        <form method="POST" action="{{ route('contratos.update', $contrato) }}">
            @csrf
            @method('PUT')
            @include('contratos._form')

            <div class="bg-gray-800/40 border border-gray-700/40 border-t-0 rounded-b-2xl px-6 py-4 flex flex-col-reverse sm:flex-row sm:items-center justify-end gap-3">
                <a href="{{ route('contratos.index') }}"
                   class="w-full sm:w-auto text-center px-5 py-2.5 bg-gray-700/50 border border-gray-600/40 text-gray-300 text-sm font-medium rounded-xl hover:bg-gray-700 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="w-full sm:w-auto btn-login-gradient px-5 py-2.5 text-white text-sm font-semibold rounded-xl shadow-lg shadow-red-950/40">
                    Guardar cambios
                </button>
            </div>
        </form>

        {{-- Control de pagos --}}
        <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl divide-y divide-gray-700/40">
            <div class="px-6 py-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-semibold text-white flex items-center gap-2">
                        <span class="w-5 h-5 brand-gradient rounded-md flex items-center justify-center flex-shrink-0">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </span>
                        Control de pagos
                    </h2>
                    @if($contrato->esta_liquidado)
                        <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-lg font-medium bg-green-500/10 text-green-400 border border-green-500/20">
                            <span class="w-1.5 h-1.5 bg-green-400 rounded-full"></span>
                            Liquidado
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-lg font-medium bg-amber-500/10 text-amber-400 border border-amber-500/20">
                            <span class="w-1.5 h-1.5 bg-amber-400 rounded-full"></span>
                            Pendiente
                        </span>
                    @endif
                </div>

                {{-- Resumen --}}
                <div class="grid grid-cols-3 gap-3 mb-5">
                    <div class="bg-gray-900/40 border border-gray-700/50 rounded-xl px-3.5 py-3">
                        <p class="text-xs text-gray-500">Costo total</p>
                        <p class="text-base font-bold text-white mt-0.5">${{ number_format($contrato->costo_viaje, 2) }}</p>
                    </div>
                    <div class="bg-gray-900/40 border border-gray-700/50 rounded-xl px-3.5 py-3">
                        <p class="text-xs text-gray-500">Total pagado</p>
                        <p class="text-base font-bold text-green-400 mt-0.5">${{ number_format($contrato->total_pagado, 2) }}</p>
                    </div>
                    <div class="bg-gray-900/40 border border-gray-700/50 rounded-xl px-3.5 py-3">
                        <p class="text-xs text-gray-500">Saldo pendiente</p>
                        <p class="text-base font-bold {{ $contrato->esta_liquidado ? 'text-white' : 'text-red-400' }} mt-0.5">${{ number_format($contrato->saldo_pendiente, 2) }}</p>
                    </div>
                </div>

                {{-- Formulario de nuevo abono --}}
                @unless($contrato->esta_liquidado)
                    <form method="POST" action="{{ route('contratos.pagos.store', $contrato) }}" class="flex flex-wrap items-end gap-3">
                        @csrf
                        <div>
                            <label for="monto" class="block text-xs font-medium text-gray-400 mb-1.5">Monto del abono</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-500 text-sm">$</span>
                                <input type="number" step="0.01" min="0.01" max="{{ $contrato->saldo_pendiente }}" id="monto" name="monto" value="{{ old('monto') }}"
                                       class="w-36 pl-7 pr-3 py-2.5 bg-gray-900/80 border {{ $errors->has('monto') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                                       placeholder="0.00"/>
                            </div>
                        </div>
                        <div>
                            <label for="fecha_pago" class="block text-xs font-medium text-gray-400 mb-1.5">Fecha</label>
                            <input type="date" id="fecha_pago" name="fecha_pago" value="{{ old('fecha_pago', now()->format('Y-m-d')) }}"
                                   class="px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('fecha_pago') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"/>
                        </div>
                        <div>
                            <label for="metodo_pago" class="block text-xs font-medium text-gray-400 mb-1.5">Método</label>
                            <select id="metodo_pago" name="metodo_pago"
                                    class="px-3.5 py-2.5 bg-gray-900/80 border border-gray-700 rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                                <option value="" class="bg-gray-900">— Sin especificar —</option>
                                <option value="Efectivo" class="bg-gray-900" {{ old('metodo_pago') === 'Efectivo' ? 'selected' : '' }}>Efectivo</option>
                                <option value="Transferencia" class="bg-gray-900" {{ old('metodo_pago') === 'Transferencia' ? 'selected' : '' }}>Transferencia</option>
                                <option value="Tarjeta" class="bg-gray-900" {{ old('metodo_pago') === 'Tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                                <option value="Depósito" class="bg-gray-900" {{ old('metodo_pago') === 'Depósito' ? 'selected' : '' }}>Depósito</option>
                            </select>
                        </div>
                        <div class="flex-1 min-w-40">
                            <label for="notas" class="block text-xs font-medium text-gray-400 mb-1.5">Notas <span class="text-gray-600 font-normal">(opcional)</span></label>
                            <input type="text" id="notas" name="notas" value="{{ old('notas') }}"
                                   class="w-full px-3.5 py-2.5 bg-gray-900/80 border border-gray-700 rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                                   placeholder="Referencia, folio de depósito..."/>
                        </div>
                        <button type="submit"
                                class="px-4 py-2.5 brand-gradient text-white text-sm font-semibold rounded-xl shadow-lg shadow-red-950/40 hover:opacity-90 transition-opacity">
                            Registrar abono
                        </button>
                    </form>
                    @error('monto')
                        <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                @else
                    <p class="text-xs text-green-400">Este contrato ya está completamente liquidado.</p>
                @endunless
            </div>

            {{-- Historial de abonos --}}
            <div class="px-6 py-5">
                <h2 class="text-sm font-semibold text-white mb-4">Historial de abonos</h2>
                @if($contrato->anticipo > 0 || $contrato->pagos->isNotEmpty())
                    <div class="space-y-2">
                        @if($contrato->anticipo > 0)
                            <div class="px-3.5 py-2.5 bg-gray-900/40 border border-gray-700/40 rounded-xl">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <span class="inline-flex items-center gap-1.5 text-xs px-2 py-0.5 rounded-lg border font-medium bg-green-500/10 text-green-400 border-green-500/20">
                                            +${{ number_format($contrato->anticipo, 2) }}
                                        </span>
                                        <p class="text-xs text-gray-300">Anticipo inicial (al firmar el contrato)</p>
                                    </div>
                                    <span class="text-xs text-gray-600">{{ $contrato->fecha_firma->format('d/m/Y') }}</span>
                                </div>
                            </div>
                        @endif
                        @foreach($contrato->pagos as $pago)
                        <div class="px-3.5 py-2.5 bg-gray-900/40 border border-gray-700/40 rounded-xl">
                            <div class="flex items-center justify-between gap-2 flex-wrap">
                                <div class="flex items-center gap-2.5">
                                    <span class="inline-flex items-center gap-1.5 text-xs px-2 py-0.5 rounded-lg border font-medium bg-green-500/10 text-green-400 border-green-500/20">
                                        +${{ number_format($pago->monto, 2) }}
                                    </span>
                                    <div>
                                        <p class="text-xs text-gray-300">
                                            {{ $pago->metodo_pago ?: 'Abono' }} · {{ $pago->user->name }}
                                        </p>
                                        @if($pago->notas)
                                            <p class="text-xs text-gray-600">{{ $pago->notas }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-gray-600">{{ $pago->fecha_pago->format('d/m/Y') }}</span>
                                    <form method="POST" action="{{ route('contratos.pagos.destroy', $pago) }}"
                                          onsubmit="return confirm('¿Eliminar este abono de ${{ number_format($pago->monto, 2) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-gray-500 hover:text-red-400 hover:bg-red-500/10 transition-all" title="Eliminar abono">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-gray-600">Sin abonos registrados todavía.</p>
                @endif
            </div>
        </div>
    </div>

</x-app-layout>
