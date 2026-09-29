<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-gray-500">
            <span>Panel</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-white">Anticipos</span>
        </div>
    </x-slot>

    {{-- Encabezado --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Anticipos</h1>
            <p class="text-sm text-gray-500 mt-0.5">Anticipos formalizados, agrupados por contrato</p>
        </div>
        @can('anticipos.crear')
            <div x-data="{ open: false, contratoId: '' }" @keydown.escape.window="open = false">
                <button type="button" @click="open = true"
                        class="brand-gradient inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-white text-sm font-semibold shadow-lg shadow-red-950/40 hover:opacity-90 transition-opacity">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nuevo anticipo
                </button>

                <template x-teleport="body">
                    <div x-show="open" x-cloak
                         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60"
                         @click.self="open = false">
                        <div x-show="open" x-transition class="w-full max-w-2xl bg-gray-800 border border-gray-700/60 rounded-2xl shadow-xl">
                            <div class="px-6 py-4 border-b border-gray-700/40 flex items-center justify-between">
                                <h2 class="text-base font-semibold text-white">Registrar anticipo</h2>
                                <button type="button" @click="open = false" class="text-gray-500 hover:text-white transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            <form method="POST" enctype="multipart/form-data" class="px-6 py-5 space-y-3"
                                  :action="contratoId ? '{{ url('contratos') }}/' + contratoId + '/anticipos' : '#'">
                                @csrf

                                <div>
                                    <label class="block text-xs font-medium text-gray-400 mb-1.5">
                                        Contrato <span class="text-red-500">*</span>
                                    </label>
                                    <select x-model="contratoId" required
                                            class="w-full px-3.5 py-2.5 bg-gray-900/80 border border-gray-700 rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                                        <option value="" class="bg-gray-900">— Selecciona un contrato —</option>
                                        @foreach($contratosParaSelector as $c)
                                            <option value="{{ $c->id }}" class="bg-gray-900">{{ $c->folio }} · {{ $c->cliente_nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-400 mb-1.5">
                                            Monto del anticipo <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-500 text-sm">$</span>
                                            <input type="number" step="1" min="1" name="monto" required
                                                   class="w-full pl-7 pr-3 py-2.5 bg-gray-900/80 border border-gray-700 rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                                                   placeholder="0">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-400 mb-1.5">
                                            Fecha <span class="text-red-500">*</span>
                                        </label>
                                        <input type="date" name="fecha_anticipo" value="{{ now()->format('Y-m-d') }}" required
                                               class="w-full px-3 py-2.5 bg-gray-900/80 border border-gray-700 rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-400 mb-1.5">Método de pago</label>
                                        <select name="metodo_pago"
                                                class="w-full px-3 py-2.5 bg-gray-900/80 border border-gray-700 rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                                            <option value="" class="bg-gray-900">— Sin especificar —</option>
                                            @foreach(['Efectivo', 'Transferencia', 'Tarjeta', 'Depósito', 'Cheque'] as $metodo)
                                                <option value="{{ $metodo }}" class="bg-gray-900">{{ $metodo }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-400 mb-1.5">
                                            Evidencia <span class="text-xs text-gray-600 font-normal">(jpg, png, pdf · máx 5MB)</span>
                                        </label>
                                        <input type="file" name="evidencia"
                                               accept="image/jpeg,image/png,image/gif,image/webp,application/pdf"
                                               class="w-full text-xs text-gray-300 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-gray-700 file:text-white file:text-xs file:font-medium hover:file:bg-gray-600">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-400 mb-1.5">
                                        Notas <span class="text-xs text-gray-600 font-normal">(opcional)</span>
                                    </label>
                                    <input type="text" name="notas"
                                           class="w-full px-3 py-2.5 bg-gray-900/80 border border-gray-700 rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                                           placeholder="Referencia, folio de transferencia, observaciones...">
                                </div>

                                <div class="flex justify-end gap-3 pt-2">
                                    <button type="button" @click="open = false"
                                            class="px-4 py-2.5 bg-gray-700/50 border border-gray-600/40 text-gray-300 text-sm font-medium rounded-xl hover:bg-gray-700 transition-colors">
                                        Cancelar
                                    </button>
                                    <button type="submit" :disabled="!contratoId"
                                            class="brand-gradient inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-white text-sm font-semibold shadow-lg shadow-red-950/40 hover:opacity-90 transition-opacity disabled:opacity-40 disabled:cursor-not-allowed">
                                        Registrar anticipo
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </template>
            </div>
        @endcan
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

    @if ($errors->any())
        <div class="mb-4 flex items-start gap-3 px-4 py-3 bg-red-900/30 border border-red-700/50 rounded-xl text-red-300 text-sm">
            <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Tarjeta principal --}}
    <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl overflow-hidden">

        {{-- Barra de búsqueda --}}
        <div class="px-5 py-4 border-b border-gray-700/40 flex flex-col sm:flex-row sm:items-center gap-3">
            <form method="GET" action="{{ route('anticipos.index') }}" class="flex-1 flex gap-2">
                <div class="relative flex-1 max-w-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ $search }}"
                           placeholder="Buscar por folio de anticipo o cliente..."
                           class="w-full pl-9 pr-4 py-2 bg-gray-900/80 border border-gray-700 rounded-xl text-sm text-white placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"/>
                </div>
                <button type="submit"
                        class="px-4 py-2 bg-gray-700/60 border border-gray-600/40 text-gray-300 text-sm rounded-xl hover:bg-gray-700 transition-colors">
                    Buscar
                </button>
                @if($search)
                    <a href="{{ route('anticipos.index') }}"
                       class="px-4 py-2 bg-gray-700/30 border border-gray-600/30 text-gray-500 text-sm rounded-xl hover:text-gray-300 transition-colors">
                        Limpiar
                    </a>
                @endif
            </form>
            <p class="text-xs text-gray-600">
                {{ $contratos->total() }} contrato(s) con anticipos
            </p>
        </div>

        {{-- Tabla --}}
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-700/40">
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-5 py-3">Contrato</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-5 py-3">Cliente</th>
                        <th class="text-right text-xs font-medium text-gray-500 uppercase tracking-wider px-5 py-3">Total anticipado</th>
                        <th class="text-center text-xs font-medium text-gray-500 uppercase tracking-wider px-5 py-3 hidden sm:table-cell">Anticipos</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-5 py-3 hidden md:table-cell">Última fecha</th>
                        <th class="text-right text-xs font-medium text-gray-500 uppercase tracking-wider px-5 py-3">Historial</th>
                    </tr>
                </thead>
                @forelse($contratos as $contrato)
                <tbody class="divide-y divide-gray-700/30" x-data="{ open: false }">
                    <tr class="hover:bg-gray-700/20 transition-colors">
                        <td class="px-5 py-3">
                            <span class="text-sm font-mono font-medium text-white">{{ $contrato->folio }}</span>
                        </td>
                        <td class="px-5 py-3">
                            <span class="text-sm text-white truncate">{{ $contrato->cliente_nombre }}</span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <span class="text-sm font-bold text-green-400">${{ number_format(round($contrato->anticipos_activos_sum ?? 0), 0) }}</span>
                        </td>
                        <td class="px-5 py-3 text-center hidden sm:table-cell">
                            <span class="text-xs px-2 py-0.5 rounded-lg bg-gray-700/50 text-gray-300">{{ $contrato->anticipos_activos_count }}</span>
                        </td>
                        <td class="px-5 py-3 hidden md:table-cell">
                            <span class="text-sm text-gray-400">
                                {{ $contrato->anticipos_max_fecha_anticipo ? \Illuminate\Support\Carbon::parse($contrato->anticipos_max_fecha_anticipo)->format('d/m/Y') : '—' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <button type="button" @click="open = !open"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-gray-300 bg-gray-700/40 border border-gray-600/40 hover:bg-gray-700 transition-colors">
                                <span x-text="open ? 'Ocultar' : 'Ver historial'"></span>
                                <svg class="w-3.5 h-3.5 transition-transform" :class="open ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                    <tr x-show="open" x-cloak>
                        <td colspan="6" class="px-5 py-4 bg-gray-900/40">
                            @include('contratos.anticipos._list', ['contrato' => $contrato])
                        </td>
                    </tr>
                </tbody>
                @empty
                <tbody>
                    <tr>
                        <td colspan="6" class="px-5 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-12 h-12 rounded-2xl bg-gray-800 border border-gray-700 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-500">No se encontraron contratos con anticipos</p>
                                @if($search)
                                    <a href="{{ route('anticipos.index') }}" class="text-xs text-red-500 hover:text-red-400">Limpiar búsqueda</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                </tbody>
                @endforelse
            </table>
        </div>

        {{-- Paginación --}}
        @if($contratos->hasPages())
            <div class="px-5 py-4 border-t border-gray-700/40 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <p class="text-xs text-gray-600">
                    Mostrando {{ $contratos->firstItem() }}–{{ $contratos->lastItem() }} de {{ $contratos->total() }}
                </p>
                <div class="flex items-center flex-wrap gap-1 justify-center sm:justify-end">
                    @if($contratos->onFirstPage())
                        <span class="px-3 py-1.5 text-xs text-gray-700 bg-gray-800/40 border border-gray-700/40 rounded-lg cursor-not-allowed">
                            &lsaquo;
                        </span>
                    @else
                        <a href="{{ $contratos->previousPageUrl() }}"
                           class="px-3 py-1.5 text-xs text-gray-400 bg-gray-800/40 border border-gray-700/40 rounded-lg hover:bg-gray-700 transition-colors">
                            &lsaquo;
                        </a>
                    @endif

                    @foreach($contratos->getUrlRange(max(1, $contratos->currentPage()-2), min($contratos->lastPage(), $contratos->currentPage()+2)) as $page => $url)
                        @if($page == $contratos->currentPage())
                            <span class="brand-gradient px-3 py-1.5 text-xs text-white rounded-lg font-medium">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}"
                               class="px-3 py-1.5 text-xs text-gray-400 bg-gray-800/40 border border-gray-700/40 rounded-lg hover:bg-gray-700 transition-colors">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach

                    @if($contratos->hasMorePages())
                        <a href="{{ $contratos->nextPageUrl() }}"
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
