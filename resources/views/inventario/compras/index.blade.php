<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-gray-500">
            <span>Panel</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <a href="{{ route('inventario.index') }}" class="hover:text-gray-300 transition-colors">Inventario</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-white">Compras pendientes</span>
        </div>
    </x-slot>

    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('inventario.index') }}"
           class="flex items-center justify-center w-9 h-9 rounded-xl bg-gray-800/60 border border-gray-700/50 text-gray-400 hover:text-white hover:border-gray-600 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white">Compras de inventario</h1>
            <p class="text-sm text-gray-500 mt-0.5">Valida el ticket, elige el país y se refleja en el stock y en gastos</p>
        </div>
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

        {{-- Pendientes de validar --}}
        <div>
            <h2 class="text-sm font-semibold text-white mb-3 flex items-center gap-2">
                Pendientes de validar
                @if($pendientes->count() > 0)
                    <span class="text-xs px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20">{{ $pendientes->count() }}</span>
                @endif
            </h2>

            @forelse($pendientes as $compra)
            <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl p-5 mb-3" x-data="{ rechazando: false }">
                <div class="flex items-start justify-between gap-4 mb-3">
                    <div>
                        <p class="text-sm font-semibold text-white">{{ $compra->item->name }}</p>
                        <p class="text-xs text-gray-500 font-mono">{{ $compra->item->code }}</p>
                        <p class="text-sm text-gray-300 mt-1">{{ $compra->quantity }} unidades · <span class="font-semibold text-white">${{ number_format($compra->precio, 2) }}</span></p>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Solicitado por {{ $compra->solicitadoPor->name }} {{ $compra->solicitadoPor->last_name }} · {{ $compra->created_at->format('d/m/Y H:i') }}
                        </p>
                        @if($compra->notas)
                            <p class="text-xs text-gray-400 mt-1">{{ $compra->notas }}</p>
                        @endif
                    </div>
                    <a href="{{ Storage::url($compra->comprobante_path) }}" target="_blank" class="flex-shrink-0">
                        @if(str_ends_with($compra->comprobante_path, '.pdf'))
                            <div class="w-20 h-20 rounded-lg border border-gray-700/50 bg-gray-900/60 hover:border-gray-500 transition-colors flex items-center justify-center text-xs text-gray-400">Ver PDF</div>
                        @else
                            <img src="{{ Storage::url($compra->comprobante_path) }}" class="w-20 h-20 object-cover rounded-lg border border-gray-700/50 hover:border-gray-500 transition-colors"/>
                        @endif
                    </a>
                </div>

                <form method="POST" action="{{ route('inventario.compras.aprobar', $compra) }}" class="flex flex-wrap items-end gap-2 pt-3 border-t border-gray-700/40">
                    @csrf
                    @method('PATCH')
                    <div class="flex-1 min-w-40">
                        <label class="block text-xs font-medium text-gray-400 mb-1.5">País del gasto <span class="text-red-500">*</span></label>
                        <select name="pais" required
                                class="w-full px-3.5 py-2.5 bg-gray-900/80 border border-gray-700 rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                            <option value="" class="bg-gray-900">— Seleccionar —</option>
                            @foreach(\App\Models\IngresoEgreso::PAISES as $key => $label)
                                <option value="{{ $key }}" class="bg-gray-900">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-2.5 bg-green-600/20 border border-green-600/40 text-green-400 text-sm font-medium rounded-xl hover:bg-green-600/30 transition-colors">
                        Validar compra
                    </button>
                    <button type="button" @click="rechazando = !rechazando"
                            class="px-4 py-2.5 bg-red-600/10 border border-red-600/30 text-red-400 text-sm font-medium rounded-xl hover:bg-red-600/20 transition-colors">
                        Rechazar
                    </button>
                </form>
                <div x-show="rechazando" x-cloak class="mt-3">
                    <form method="POST" action="{{ route('inventario.compras.rechazar', $compra) }}" class="flex flex-wrap gap-2">
                        @csrf
                        @method('PATCH')
                        <input type="text" name="motivo_rechazo" placeholder="Motivo (opcional)"
                               class="flex-1 min-w-0 px-3 py-2 bg-gray-900/80 border border-gray-700 rounded-lg text-white text-xs placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500"/>
                        <button type="submit" class="px-3 py-2 bg-red-600 text-white text-xs font-medium rounded-lg hover:bg-red-700 transition-colors">
                            Confirmar rechazo
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl p-10 text-center">
                <p class="text-sm text-gray-500">No hay compras pendientes de validar.</p>
            </div>
            @endforelse
        </div>

        {{-- Resueltas recientemente --}}
        @if($resueltas->isNotEmpty())
        <div>
            <h2 class="text-sm font-semibold text-white mb-3">Resueltas recientemente</h2>
            <div class="space-y-2">
                @foreach($resueltas as $compra)
                @php
                    $badge = $compra->estado === 'aprobada'
                        ? 'bg-green-500/10 text-green-400 border-green-500/20'
                        : 'bg-red-500/10 text-red-400 border-red-500/20';
                @endphp
                <div class="flex items-center justify-between gap-3 px-4 py-3 bg-gray-800/40 border border-gray-700/40 rounded-xl">
                    <div class="min-w-0">
                        <p class="text-sm text-white truncate">
                            {{ $compra->item->name }}
                            <span class="text-xs text-gray-500 font-mono">({{ $compra->item->code }})</span>
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ $compra->quantity }} unidades · ${{ number_format($compra->precio, 2) }}
                            @if($compra->pais) · {{ \App\Models\IngresoEgreso::PAISES[$compra->pais] ?? $compra->pais }} @endif
                            · revisado por {{ $compra->revisadoPor?->name }} {{ $compra->revisadoPor?->last_name }} el {{ $compra->revisado_at?->format('d/m/Y H:i') }}
                        </p>
                        @if($compra->estado === 'rechazada' && $compra->motivo_rechazo)
                            <p class="text-xs text-red-400 mt-0.5">Motivo: {{ $compra->motivo_rechazo }}</p>
                        @endif
                    </div>
                    <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-lg border font-medium whitespace-nowrap flex-shrink-0 {{ $badge }}">
                        {{ \App\Models\InventoryPurchase::ESTADOS[$compra->estado] ?? $compra->estado }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

</x-app-layout>
