<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-gray-500">
            <span>Panel</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <a href="{{ route('contratos-historicos.index') }}" class="hover:text-gray-300 transition-colors">Contratos históricos</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-white">{{ $contrato->folio }}</span>
        </div>
    </x-slot>

    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('contratos-historicos.index') }}"
           class="flex items-center justify-center w-9 h-9 rounded-xl bg-gray-800/60 border border-gray-700/50 text-gray-400 hover:text-white hover:border-gray-600 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white">Editar contrato histórico {{ $contrato->folio }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $contrato->cliente_nombre }}</p>
        </div>
    </div>

    <div class="max-w-3xl">
        <form method="POST" action="{{ route('contratos-historicos.update', $contrato) }}" enctype="multipart/form-data"
              class="bg-gray-800/40 border border-gray-700/40 rounded-2xl p-5 space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label for="folio" class="block text-xs font-medium text-gray-400 mb-1.5">Folio <span class="text-red-500">*</span></label>
                    <input type="text" id="folio" name="folio" value="{{ old('folio', $contrato->folio) }}"
                           class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('folio') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"/>
                    @error('folio')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="cliente_nombre" class="block text-xs font-medium text-gray-400 mb-1.5">Cliente <span class="text-red-500">*</span></label>
                    <input type="text" id="cliente_nombre" name="cliente_nombre" value="{{ old('cliente_nombre', $contrato->cliente_nombre) }}"
                           class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('cliente_nombre') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"/>
                    @error('cliente_nombre')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="destino" class="block text-xs font-medium text-gray-400 mb-1.5">Destino</label>
                    <input type="text" id="destino" name="destino" value="{{ old('destino', $contrato->destino) }}"
                           class="w-full px-3.5 py-2.5 bg-gray-900/80 border border-gray-700 rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"/>
                </div>
                <div>
                    <label for="fecha_salida" class="block text-xs font-medium text-gray-400 mb-1.5">Fecha de salida</label>
                    <input type="date" id="fecha_salida" name="fecha_salida" value="{{ old('fecha_salida', $contrato->fecha_salida?->format('Y-m-d')) }}"
                           class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('fecha_salida') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"/>
                    @error('fecha_salida')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="fecha_regreso" class="block text-xs font-medium text-gray-400 mb-1.5">Fecha de regreso</label>
                    <input type="date" id="fecha_regreso" name="fecha_regreso" value="{{ old('fecha_regreso', $contrato->fecha_regreso?->format('Y-m-d')) }}"
                           class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('fecha_regreso') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"/>
                    @error('fecha_regreso')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="precio_viaje" class="block text-xs font-medium text-gray-400 mb-1.5">Precio del viaje</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-500 text-sm">$</span>
                        <input type="number" step="0.01" min="0" id="precio_viaje" name="precio_viaje" value="{{ old('precio_viaje', $contrato->precio_viaje) }}"
                               class="w-full pl-7 pr-3.5 py-2.5 bg-gray-900/80 border border-gray-700 rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"/>
                    </div>
                </div>
                <div>
                    <label for="anticipo" class="block text-xs font-medium text-gray-400 mb-1.5">Anticipo</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-500 text-sm">$</span>
                        <input type="number" step="0.01" min="0" id="anticipo" name="anticipo" value="{{ old('anticipo', $contrato->anticipo) }}"
                               class="w-full pl-7 pr-3.5 py-2.5 bg-gray-900/80 border border-gray-700 rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"/>
                    </div>
                </div>
                <div class="sm:col-span-2 lg:col-span-3">
                    <label for="descripcion" class="block text-xs font-medium text-gray-400 mb-1.5">Descripción <span class="text-gray-600 font-normal">(opcional)</span></label>
                    <input type="text" id="descripcion" name="descripcion" value="{{ old('descripcion', $contrato->descripcion) }}"
                           class="w-full px-3.5 py-2.5 bg-gray-900/80 border border-gray-700 rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"/>
                </div>
            </div>

            <div>
                <p class="text-xs text-gray-500 mb-2">
                    Documento actual:
                    <a href="{{ Storage::url($contrato->archivo_path) }}" target="_blank" class="text-red-400 hover:text-red-300">{{ $contrato->nombre_original }}</a>
                </p>
                <label for="archivo" class="block text-xs font-medium text-gray-400 mb-1.5">Reemplazar documento <span class="text-gray-600 font-normal">(opcional, JPG/PNG/PDF, máx. 8MB)</span></label>
                <input type="file" id="archivo" name="archivo" accept=".jpg,.jpeg,.png,.pdf"
                       class="w-full text-sm text-gray-400 file:mr-3 file:px-3.5 file:py-2 file:rounded-xl file:border-0 file:bg-gray-700/60 file:text-gray-200 file:text-sm hover:file:bg-gray-700
                              bg-gray-900/80 border {{ $errors->has('archivo') ? 'border-red-500' : 'border-gray-700' }} rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"/>
                @error('archivo')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>

            <div class="flex flex-col-reverse sm:flex-row sm:items-center justify-end gap-3 pt-2 border-t border-gray-700/40">
                <a href="{{ route('contratos-historicos.index') }}"
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
</x-app-layout>
