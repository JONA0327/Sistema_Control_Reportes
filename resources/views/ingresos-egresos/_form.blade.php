@php
    $movimiento = $movimiento ?? null;
@endphp

<div class="bg-gray-800/40 border border-gray-700/40 rounded-t-2xl divide-y divide-gray-700/40">

    {{-- Sección: Datos del movimiento --}}
    <div class="px-6 py-5">
        <h2 class="text-sm font-semibold text-white mb-4 flex items-center gap-2">
            <span class="w-5 h-5 brand-gradient rounded-md flex items-center justify-center text-white flex-shrink-0">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </span>
            Datos del movimiento
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

            {{-- Tipo --}}
            <div>
                <label for="tipo" class="block text-xs font-medium text-gray-400 mb-1.5">
                    Tipo <span class="text-red-500">*</span>
                </label>
                <select id="tipo" name="tipo"
                        class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('tipo') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                    <option value="" class="bg-gray-900">— Seleccionar —</option>
                    <option value="ingreso" class="bg-gray-900" {{ old('tipo', $movimiento?->tipo) === 'ingreso' ? 'selected' : '' }}>Ingreso</option>
                    <option value="egreso" class="bg-gray-900" {{ old('tipo', $movimiento?->tipo) === 'egreso' ? 'selected' : '' }}>Egreso</option>
                </select>
                @error('tipo')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Fecha --}}
            <div>
                <label for="fecha" class="block text-xs font-medium text-gray-400 mb-1.5">
                    Fecha <span class="text-red-500">*</span>
                </label>
                <input type="date" id="fecha" name="fecha"
                       value="{{ old('fecha', $movimiento?->fecha?->format('Y-m-d')) }}"
                       class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('fecha') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"/>
                @error('fecha')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Concepto --}}
            <div class="sm:col-span-2">
                <label for="concepto" class="block text-xs font-medium text-gray-400 mb-1.5">
                    Concepto <span class="text-red-500">*</span>
                </label>
                <input type="text" id="concepto" name="concepto" value="{{ old('concepto', $movimiento?->concepto) }}"
                       class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('concepto') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                       placeholder="Ej: Pago de nómina, Venta de servicio de flete"/>
                @error('concepto')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Monto --}}
            <div>
                <label for="monto" class="block text-xs font-medium text-gray-400 mb-1.5">
                    Monto <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-500 text-sm">$</span>
                    <input type="number" step="0.01" min="0.01" id="monto" name="monto" value="{{ old('monto', $movimiento?->monto) }}"
                           class="w-full pl-7 pr-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('monto') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                           placeholder="0.00"/>
                </div>
                @error('monto')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Categoría --}}
            <div>
                <label for="categoria" class="block text-xs font-medium text-gray-400 mb-1.5">
                    Categoría
                    <span class="text-xs text-gray-600 font-normal">(opcional)</span>
                </label>
                <input type="text" id="categoria" name="categoria" value="{{ old('categoria', $movimiento?->categoria) }}"
                       class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('categoria') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                       placeholder="Ej: Nómina, Combustible, Ventas"/>
                @error('categoria')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- País --}}
            <div>
                <label for="pais" class="block text-xs font-medium text-gray-400 mb-1.5">
                    País
                    <span class="text-xs text-gray-600 font-normal">(los automáticos lo determinan por IA según el destino)</span>
                </label>
                <select id="pais" name="pais"
                        class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('pais') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                    @foreach(\App\Models\IngresoEgreso::PAISES as $codigoPais => $nombrePais)
                        <option value="{{ $codigoPais }}" class="bg-gray-900" {{ old('pais', $movimiento?->pais ?? ($pais ?? 'mexico')) === $codigoPais ? 'selected' : '' }}>{{ $nombrePais }}</option>
                    @endforeach
                </select>
                @error('pais')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Descripción --}}
            <div class="sm:col-span-2">
                <label for="descripcion" class="block text-xs font-medium text-gray-400 mb-1.5">
                    Descripción
                    <span class="text-xs text-gray-600 font-normal">(opcional)</span>
                </label>
                <textarea id="descripcion" name="descripcion" rows="3"
                          class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('descripcion') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                          placeholder="Detalles adicionales del movimiento">{{ old('descripcion', $movimiento?->descripcion) }}</textarea>
                @error('descripcion')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- Sección: Comprobante --}}
    <div class="px-6 py-5">
        <h2 class="text-sm font-semibold text-white mb-4 flex items-center gap-2">
            <span class="w-5 h-5 brand-gradient rounded-md flex items-center justify-center text-white flex-shrink-0">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </span>
            Comprobante
            <span class="text-xs text-gray-600 font-normal">(opcional)</span>
        </h2>

        @if($movimiento?->comprobante_path)
            <div class="mb-3 flex items-center gap-2 text-xs text-gray-400">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Ya existe un comprobante cargado.
                <a href="{{ Storage::url($movimiento->comprobante_path) }}" target="_blank" class="text-red-400 hover:text-red-300">Ver actual</a>
            </div>
        @endif

        <label for="comprobante"
               class="flex flex-col items-center justify-center w-full h-24 border border-dashed border-gray-700 rounded-xl cursor-pointer bg-gray-900/40 hover:bg-gray-900/60 hover:border-gray-600 transition-all">
            <svg class="w-6 h-6 text-gray-600 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
            </svg>
            <span class="text-xs text-gray-500">Clic para subir comprobante</span>
            <span class="text-xs text-gray-700 mt-0.5">JPG, PNG o PDF hasta 4MB</span>
        </label>
        <input type="file" id="comprobante" name="comprobante" accept=".jpg,.jpeg,.png,.pdf" class="hidden"
               onchange="this.previousElementSibling.querySelector('span').textContent = this.files[0] ? this.files[0].name : 'Clic para subir comprobante';"/>
        @error('comprobante')
            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
        @enderror
    </div>
</div>
