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

    @php
        $tabInicial = $errors->hasAny(['monto', 'fecha_anticipo', 'metodo_pago', 'evidencia']) ? 'anticipo' : 'detalles';
    @endphp

    <div class="max-w-3xl space-y-4" x-data="{ tab: '{{ $tabInicial }}' }">

        {{-- Pestañas --}}
        <div class="flex gap-2">
            <button type="button" @click="tab = 'detalles'"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium transition-all"
                    :class="tab === 'detalles' ? 'brand-gradient text-white shadow-lg shadow-red-950/40' : 'bg-gray-800/60 border border-gray-700/50 text-gray-400 hover:text-white hover:border-gray-600'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Detalles del contrato
            </button>
            <button type="button" @click="tab = 'anticipo'"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium transition-all"
                    :class="tab === 'anticipo' ? 'brand-gradient text-white shadow-lg shadow-red-950/40' : 'bg-gray-800/60 border border-gray-700/50 text-gray-400 hover:text-white hover:border-gray-600'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Anticipos
            </button>
        </div>

        <div x-show="tab === 'detalles'" x-cloak>
            <form method="POST" action="{{ route('contratos.update', $contrato) }}" enctype="multipart/form-data">
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
        </div>

        {{-- Anticipos formalizados (con folio, evidencia y comprobante PDF) --}}
        <div x-show="tab === 'anticipo'" x-cloak class="bg-gray-800/40 border border-gray-700/40 rounded-2xl divide-y divide-gray-700/40">
            <div class="px-6 py-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-semibold text-white flex items-center gap-2">
                        <span class="w-5 h-5 brand-gradient rounded-md flex items-center justify-center flex-shrink-0">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </span>
                        Anticipos formalizados
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

                {{-- Resumen de liquidación --}}
                <div class="grid grid-cols-3 gap-3 mb-4">
                    <div class="bg-gray-900/40 border border-gray-700/50 rounded-xl px-3.5 py-3">
                        <p class="text-xs text-gray-500">Costo total</p>
                        <p class="text-base font-bold text-white mt-0.5">${{ number_format($contrato->costo_viaje, 2) }}</p>
                    </div>
                    <div class="bg-gray-900/40 border border-gray-700/50 rounded-xl px-3.5 py-3">
                        <p class="text-xs text-gray-500">Total anticipado</p>
                        <p class="text-base font-bold text-green-400 mt-0.5">${{ number_format($contrato->total_pagado, 2) }}</p>
                    </div>
                    <div class="bg-gray-900/40 border border-gray-700/50 rounded-xl px-3.5 py-3">
                        <p class="text-xs text-gray-500">Falta por liquidar</p>
                        <p class="text-base font-bold {{ $contrato->esta_liquidado ? 'text-white' : 'text-red-400' }} mt-0.5">${{ number_format($contrato->saldo_pendiente, 2) }}</p>
                    </div>
                </div>

                <p class="text-xs text-gray-500 mb-4">
                    Cada anticipo formalizado genera un folio único, puede llevar evidencia adjunta
                    (foto o PDF) y tiene su propio comprobante para entregar al cliente.
                </p>

                @can('contratos.anticipos')
                    @include('contratos.anticipos._form')
                @endcan
            </div>

            <div class="px-6 py-5">
                <h2 class="text-sm font-semibold text-white mb-4">Historial de anticipos</h2>
                @include('contratos.anticipos._list')
            </div>
        </div>
    </div>

</x-app-layout>
