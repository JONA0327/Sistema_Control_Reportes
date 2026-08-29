<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-gray-500">
            <span>Panel</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-white">Contratos históricos</span>
        </div>
    </x-slot>

    <div x-data="{ mostrarForm: {{ $errors->any() ? 'true' : 'false' }} }">

        {{-- Encabezado --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">Contratos históricos</h1>
                <p class="text-sm text-gray-500 mt-0.5">Control de contratos anteriores al sistema, capturados manualmente con su documento anexo.</p>
            </div>
            <button type="button" @click="mostrarForm = ! mostrarForm"
                    class="brand-gradient inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-white text-sm font-semibold shadow-lg shadow-red-950/40 hover:opacity-90 transition-opacity">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo contrato histórico
            </button>
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

        {{-- Formulario de captura --}}
        <div x-show="mostrarForm" x-cloak x-transition class="bg-gray-800/40 border border-gray-700/40 rounded-2xl p-5 mb-6">
            <h2 class="text-sm font-semibold text-white mb-4">Datos del contrato histórico</h2>
            <form method="POST" action="{{ route('contratos-historicos.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label for="folio" class="block text-xs font-medium text-gray-400 mb-1.5">Folio <span class="text-red-500">*</span></label>
                        <input type="text" id="folio" name="folio" value="{{ old('folio') }}"
                               class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('folio') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                               placeholder="Ej: CT-00012"/>
                        @error('folio')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="cliente_nombre" class="block text-xs font-medium text-gray-400 mb-1.5">Cliente <span class="text-red-500">*</span></label>
                        <input type="text" id="cliente_nombre" name="cliente_nombre" value="{{ old('cliente_nombre') }}"
                               class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('cliente_nombre') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                               placeholder="Nombre del cliente"/>
                        @error('cliente_nombre')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="destino" class="block text-xs font-medium text-gray-400 mb-1.5">Destino</label>
                        <input type="text" id="destino" name="destino" value="{{ old('destino') }}"
                               class="w-full px-3.5 py-2.5 bg-gray-900/80 border border-gray-700 rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                               placeholder="Ciudad destino"/>
                    </div>
                    <div>
                        <label for="fecha_salida" class="block text-xs font-medium text-gray-400 mb-1.5">Fecha de salida</label>
                        <input type="date" id="fecha_salida" name="fecha_salida" value="{{ old('fecha_salida') }}"
                               class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('fecha_salida') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"/>
                        @error('fecha_salida')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="fecha_regreso" class="block text-xs font-medium text-gray-400 mb-1.5">Fecha de regreso</label>
                        <input type="date" id="fecha_regreso" name="fecha_regreso" value="{{ old('fecha_regreso') }}"
                               class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('fecha_regreso') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"/>
                        @error('fecha_regreso')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="precio_viaje" class="block text-xs font-medium text-gray-400 mb-1.5">Precio del viaje</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-500 text-sm">$</span>
                            <input type="number" step="0.01" min="0" id="precio_viaje" name="precio_viaje" value="{{ old('precio_viaje') }}"
                                   class="w-full pl-7 pr-3.5 py-2.5 bg-gray-900/80 border border-gray-700 rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                                   placeholder="0.00"/>
                        </div>
                    </div>
                    <div>
                        <label for="anticipo" class="block text-xs font-medium text-gray-400 mb-1.5">Anticipo</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-500 text-sm">$</span>
                            <input type="number" step="0.01" min="0" id="anticipo" name="anticipo" value="{{ old('anticipo') }}"
                                   class="w-full pl-7 pr-3.5 py-2.5 bg-gray-900/80 border border-gray-700 rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                                   placeholder="0.00"/>
                        </div>
                    </div>
                    <div class="sm:col-span-2">
                        <label for="descripcion" class="block text-xs font-medium text-gray-400 mb-1.5">Descripción <span class="text-gray-600 font-normal">(opcional)</span></label>
                        <input type="text" id="descripcion" name="descripcion" value="{{ old('descripcion') }}"
                               class="w-full px-3.5 py-2.5 bg-gray-900/80 border border-gray-700 rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                               placeholder="Notas adicionales"/>
                    </div>
                </div>

                <div class="flex flex-wrap items-end gap-3">
                    <div class="flex-1 min-w-48">
                        <label for="archivo" class="block text-xs font-medium text-gray-400 mb-1.5">Foto o PDF del contrato <span class="text-red-500">*</span> (máx. 8MB)</label>
                        <input type="file" id="archivo" name="archivo" accept=".jpg,.jpeg,.png,.pdf"
                               class="w-full text-sm text-gray-400 file:mr-3 file:px-3.5 file:py-2 file:rounded-xl file:border-0 file:bg-gray-700/60 file:text-gray-200 file:text-sm hover:file:bg-gray-700
                                      bg-gray-900/80 border {{ $errors->has('archivo') ? 'border-red-500' : 'border-gray-700' }} rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"/>
                        @error('archivo')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit"
                            class="px-4 py-2.5 brand-gradient text-white text-sm font-semibold rounded-xl shadow-lg shadow-red-950/40 hover:opacity-90 transition-opacity">
                        Guardar
                    </button>
                </div>
            </form>
        </div>

        {{-- Listado --}}
        <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-700/40">
                <form method="GET" action="{{ route('contratos-historicos.index') }}" class="relative max-w-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por folio, cliente o destino..."
                           class="w-full pl-9 pr-3.5 py-2.5 bg-gray-900/80 border border-gray-700 rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"/>
                </form>
            </div>

            @if($contratos->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-700/40 text-left text-xs text-gray-500 uppercase tracking-wider">
                                <th class="px-5 py-3 font-medium">Folio</th>
                                <th class="px-5 py-3 font-medium">Cliente</th>
                                <th class="px-5 py-3 font-medium">Destino</th>
                                <th class="px-5 py-3 font-medium">Salida / Regreso</th>
                                <th class="px-5 py-3 font-medium">Precio</th>
                                <th class="px-5 py-3 font-medium">Anticipo</th>
                                <th class="px-5 py-3 font-medium">Documento</th>
                                <th class="px-5 py-3 font-medium text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700/30">
                            @foreach($contratos as $c)
                                <tr class="hover:bg-gray-900/30 transition-colors">
                                    <td class="px-5 py-3.5 text-white font-medium">{{ $c->folio }}</td>
                                    <td class="px-5 py-3.5 text-gray-300">{{ $c->cliente_nombre }}</td>
                                    <td class="px-5 py-3.5 text-gray-400">{{ $c->destino ?: '—' }}</td>
                                    <td class="px-5 py-3.5 text-gray-500 text-xs">
                                        {{ $c->fecha_salida?->format('d/m/Y') ?: '—' }} → {{ $c->fecha_regreso?->format('d/m/Y') ?: '—' }}
                                    </td>
                                    <td class="px-5 py-3.5 text-gray-300">{{ $c->precio_viaje !== null ? '$'.number_format($c->precio_viaje, 2) : '—' }}</td>
                                    <td class="px-5 py-3.5 text-gray-300">{{ $c->anticipo !== null ? '$'.number_format($c->anticipo, 2) : '—' }}</td>
                                    <td class="px-5 py-3.5">
                                        <a href="{{ Storage::url($c->archivo_path) }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 text-red-400 hover:text-red-300 text-xs">
                                            @if($c->esImagen())
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M4 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            @else
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            @endif
                                            Ver
                                        </a>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('contratos-historicos.edit', $c) }}"
                                               class="p-1.5 rounded-lg text-gray-500 hover:text-white hover:bg-gray-700/60 transition-all" title="Editar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            <form method="POST" action="{{ route('contratos-historicos.destroy', $c) }}"
                                                  onsubmit="return confirm('¿Eliminar este contrato histórico?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg text-gray-500 hover:text-red-400 hover:bg-red-500/10 transition-all" title="Eliminar">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-5">
                    {{ $contratos->links() }}
                </div>
            @else
                <p class="text-sm text-gray-600 text-center py-8">No hay contratos históricos registrados todavía.</p>
            @endif
        </div>
    </div>
</x-app-layout>
