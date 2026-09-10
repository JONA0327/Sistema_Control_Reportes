@php
    $contrato = $contrato ?? null;
@endphp

<div class="bg-gray-800/40 border border-gray-700/40 rounded-t-2xl divide-y divide-gray-700/40">

    {{-- Sección: Cliente --}}
    <div class="px-6 py-5">
        <h2 class="text-sm font-semibold text-white mb-4 flex items-center gap-2">
            <span class="w-5 h-5 brand-gradient rounded-md flex items-center justify-center text-white flex-shrink-0">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </span>
            Datos del arrendatario (cliente)
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label for="cliente_nombre" class="block text-xs font-medium text-gray-400 mb-1.5">
                    Nombre del cliente <span class="text-red-500">*</span>
                </label>
                <input type="text" id="cliente_nombre" name="cliente_nombre" value="{{ old('cliente_nombre', $contrato?->cliente_nombre) }}"
                       class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('cliente_nombre') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                       placeholder="Ej: David Franco Rodríguez"/>
                @error('cliente_nombre')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="cliente_telefono" class="block text-xs font-medium text-gray-400 mb-1.5">Teléfono</label>
                <input type="text" id="cliente_telefono" name="cliente_telefono" value="{{ old('cliente_telefono', $contrato?->cliente_telefono) }}"
                       class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('cliente_telefono') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                       placeholder="Ej: 4441897586"/>
                @error('cliente_telefono')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="cliente_ciudad" class="block text-xs font-medium text-gray-400 mb-1.5">Ciudad</label>
                <input type="text" id="cliente_ciudad" name="cliente_ciudad" value="{{ old('cliente_ciudad', $contrato?->cliente_ciudad) }}"
                       class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('cliente_ciudad') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                       placeholder="Ej: San Luis Potosí"/>
                @error('cliente_ciudad')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="sm:col-span-2">
                <label for="cliente_domicilio" class="block text-xs font-medium text-gray-400 mb-1.5">Domicilio</label>
                <input type="text" id="cliente_domicilio" name="cliente_domicilio" value="{{ old('cliente_domicilio', $contrato?->cliente_domicilio) }}"
                       class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('cliente_domicilio') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                       placeholder="Calle, número, colonia"/>
                @error('cliente_domicilio')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- Sección: Unidad --}}
    <div class="px-6 py-5">
        <h2 class="text-sm font-semibold text-white mb-4 flex items-center gap-2">
            <span class="w-5 h-5 brand-gradient rounded-md flex items-center justify-center text-white flex-shrink-0">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
            </span>
            Unidad
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="bus_id" class="block text-xs font-medium text-gray-400 mb-1.5">
                    Unidad asignada
                    <span class="text-xs text-gray-600 font-normal">(opcional)</span>
                </label>
                <select id="bus_id" name="bus_id"
                        onchange="const o=this.options[this.selectedIndex]; const a=o.getAttribute('data-asientos'); if(a){ document.getElementById('num_plazas').value = a; }"
                        class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('bus_id') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                    <option value="" class="bg-gray-900">— Sin asignar —</option>
                    @foreach($buses as $bus)
                        <option value="{{ $bus->id }}" class="bg-gray-900"
                                @if($bus->num_asientos) data-asientos="{{ $bus->num_asientos }}" @endif
                                {{ (int) old('bus_id', $contrato?->bus_id) === $bus->id ? 'selected' : '' }}>
                            Bus #{{ $bus->num_bus }} ({{ $bus->placa }})@if($bus->num_asientos) &middot; {{ $bus->num_asientos }} asientos @endif
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-600">Al elegir una unidad con asientos registrados, se llenan automáticamente.</p>
                @error('bus_id')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="num_plazas" class="block text-xs font-medium text-gray-400 mb-1.5">Número de plazas</label>
                <input type="number" min="1" id="num_plazas" name="num_plazas" value="{{ old('num_plazas', $contrato?->num_plazas ?? $contrato?->bus?->num_asientos) }}"
                       class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('num_plazas') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                       placeholder="Ej: 64"/>
                @error('num_plazas')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- Sección: Datos del viaje --}}
    <div class="px-6 py-5">
        <h2 class="text-sm font-semibold text-white mb-4 flex items-center gap-2">
            <span class="w-5 h-5 brand-gradient rounded-md flex items-center justify-center text-white flex-shrink-0">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
            </span>
            Datos del viaje
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="fecha_salida" class="block text-xs font-medium text-gray-400 mb-1.5">
                    Fecha de salida <span class="text-red-500">*</span>
                </label>
                <input type="date" id="fecha_salida" name="fecha_salida" value="{{ old('fecha_salida', $contrato?->fecha_salida?->format('Y-m-d')) }}"
                       class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('fecha_salida') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"/>
                @error('fecha_salida')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="hora_salida" class="block text-xs font-medium text-gray-400 mb-1.5">Hora de salida</label>
                <input type="time" id="hora_salida" name="hora_salida" value="{{ old('hora_salida', $contrato?->hora_salida) }}"
                       class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('hora_salida') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"/>
                @error('hora_salida')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="fecha_regreso" class="block text-xs font-medium text-gray-400 mb-1.5">
                    Fecha de regreso <span class="text-red-500">*</span>
                </label>
                <input type="date" id="fecha_regreso" name="fecha_regreso" value="{{ old('fecha_regreso', $contrato?->fecha_regreso?->format('Y-m-d')) }}"
                       class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('fecha_regreso') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"/>
                @error('fecha_regreso')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="hora_regreso" class="block text-xs font-medium text-gray-400 mb-1.5">Hora de regreso</label>
                <input type="time" id="hora_regreso" name="hora_regreso" value="{{ old('hora_regreso', $contrato?->hora_regreso) }}"
                       class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('hora_regreso') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"/>
                @error('hora_regreso')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="salida" class="block text-xs font-medium text-gray-400 mb-1.5">
                    Salida <span class="text-red-500">*</span>
                </label>
                <input type="text" id="salida" name="salida" value="{{ old('salida', $contrato?->salida) }}"
                       class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('salida') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                       placeholder="Ej: San Luis Potosí"/>
                @error('salida')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="destino" class="block text-xs font-medium text-gray-400 mb-1.5">
                    Destino <span class="text-red-500">*</span>
                </label>
                <input type="text" id="destino" name="destino" value="{{ old('destino', $contrato?->destino) }}"
                       class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('destino') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                       placeholder="Ej: Tlaquepaque / Akron"/>
                @error('destino')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="sm:col-span-2">
                <label for="punto_partida_llegada" class="block text-xs font-medium text-gray-400 mb-1.5">Punto de partida y llegada</label>
                <input type="text" id="punto_partida_llegada" name="punto_partida_llegada" value="{{ old('punto_partida_llegada', $contrato?->punto_partida_llegada) }}"
                       class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('punto_partida_llegada') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                       placeholder="Ej: Telas Parisina Plaza el Paseo"/>
                @error('punto_partida_llegada')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="sm:col-span-2">
                <label for="itinerario" class="block text-xs font-medium text-gray-400 mb-1.5">Itinerario</label>
                <textarea id="itinerario" name="itinerario" rows="3"
                          class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('itinerario') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                          placeholder="Ej: Llegan a Tlaquepaque después a Akron">{{ old('itinerario', $contrato?->itinerario) }}</textarea>
                @error('itinerario')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="sm:col-span-2 flex items-center justify-between px-4 py-3 bg-gray-900/40 border border-gray-700/50 rounded-xl">
                <div>
                    <p class="text-sm font-medium text-white">El costo del viaje incluye estacionamientos</p>
                    <p class="text-xs text-gray-500">Si se desactiva, el contrato mostrará la leyenda "No incluye estacionamientos"</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                    <input type="checkbox" name="incluye_estacionamiento" value="1"
                           class="sr-only peer" {{ old('incluye_estacionamiento', $contrato?->incluye_estacionamiento ?? true) ? 'checked' : '' }}>
                    <div class="relative w-11 h-6 bg-gray-700 rounded-full peer
                                peer-checked:bg-red-600
                                after:content-[''] after:absolute after:top-[3px] after:left-[3px]
                                after:bg-white after:rounded-full after:h-[18px] after:w-[18px]
                                after:transition-all peer-checked:after:translate-x-5"></div>
                </label>
            </div>
        </div>
    </div>

    {{-- Sección: Costos --}}
    <div class="px-6 py-5">
        <h2 class="text-sm font-semibold text-white mb-4 flex items-center gap-2">
            <span class="w-5 h-5 brand-gradient rounded-md flex items-center justify-center text-white flex-shrink-0">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </span>
            Costos
        </h2>

        <div class="max-w-xs">
            <label for="costo_viaje" class="block text-xs font-medium text-gray-400 mb-1.5">
                Costo del viaje <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-500 text-sm">$</span>
                <input type="number" step="0.01" min="0" id="costo_viaje" name="costo_viaje" value="{{ old('costo_viaje', $contrato?->costo_viaje) }}"
                       class="w-full pl-7 pr-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('costo_viaje') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                       placeholder="0.00"/>
            </div>
            @error('costo_viaje')
                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Anticipo inicial: solo al crear el contrato; una vez creado se maneja en "Anticipos formalizados" --}}
    @unless($contrato)
    <div class="px-6 py-5">
        <h2 class="text-sm font-semibold text-white mb-1 flex items-center gap-2">
            <span class="w-5 h-5 brand-gradient rounded-md flex items-center justify-center text-white flex-shrink-0">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </span>
            Anticipo inicial
        </h2>
        <p class="text-xs text-gray-600 mb-4">Se descuenta del costo del viaje. Si pones un monto mayor a $0, se genera folio, evidencia y comprobante al guardar el contrato.</p>

        <div class="max-w-xs">
            <label for="anticipo" class="block text-xs font-medium text-gray-400 mb-1.5">
                Anticipo <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-500 text-sm">$</span>
                <input type="number" step="0.01" min="0" id="anticipo" name="anticipo" value="{{ old('anticipo', $contrato?->anticipo) }}"
                       class="w-full pl-7 pr-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('anticipo') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                       placeholder="0.00"/>
            </div>
            @error('anticipo')
                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- Detalles del anticipo (se ven cuando hay monto > 0) --}}
        <div id="anticipo-detalles" class="mt-4 pt-4 border-t border-gray-700/40 {{ (float) old('anticipo', 0) > 0 ? '' : 'hidden' }}">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-xs font-semibold text-white flex items-center gap-2">
                    <span class="w-4 h-4 brand-gradient rounded-md flex items-center justify-center flex-shrink-0">
                        <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </span>
                    Detalles del anticipo
                </h3>
                <span class="text-xs text-gray-500 italic">Se genera folio, evidencia y PDF al guardar</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label for="anticipo_fecha" class="block text-xs font-medium text-gray-400 mb-1.5">
                        Fecha del anticipo <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="anticipo_fecha" name="anticipo_fecha"
                           value="{{ old('anticipo_fecha', now()->format('Y-m-d')) }}"
                           class="w-full px-3 py-2.5 bg-gray-900/80 border {{ $errors->has('anticipo_fecha') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"/>
                    @error('anticipo_fecha')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="anticipo_metodo_pago" class="block text-xs font-medium text-gray-400 mb-1.5">Método de pago</label>
                    <select id="anticipo_metodo_pago" name="anticipo_metodo_pago"
                            class="w-full px-3 py-2.5 bg-gray-900/80 border border-gray-700 rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                        <option value="" class="bg-gray-900">— Sin especificar —</option>
                        @foreach(['Efectivo', 'Transferencia', 'Tarjeta', 'Depósito', 'Cheque'] as $metodo)
                            <option value="{{ $metodo }}" class="bg-gray-900" {{ old('anticipo_metodo_pago') === $metodo ? 'selected' : '' }}>{{ $metodo }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="anticipo_evidencia" class="block text-xs font-medium text-gray-400 mb-1.5">
                        Evidencia
                        <span class="text-xs text-gray-600 font-normal">(jpg, png, pdf · 5MB)</span>
                    </label>
                    <input type="file" id="anticipo_evidencia" name="anticipo_evidencia"
                           accept="image/jpeg,image/png,image/gif,image/webp,application/pdf"
                           class="w-full text-xs text-gray-300 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-gray-700 file:text-white file:text-xs file:font-medium hover:file:bg-gray-600"/>
                    @error('anticipo_evidencia')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-3">
                <label for="anticipo_notas" class="block text-xs font-medium text-gray-400 mb-1.5">
                    Notas
                    <span class="text-xs text-gray-600 font-normal">(opcional)</span>
                </label>
                <input type="text" id="anticipo_notas" name="anticipo_notas"
                       value="{{ old('anticipo_notas') }}"
                       class="w-full px-3 py-2.5 bg-gray-900/80 border border-gray-700 rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                       placeholder="Referencia, folio de transferencia, observaciones..."/>
            </div>
        </div>
    </div>
    @endunless

    {{-- Sección: Firma y notas --}}
    <div class="px-6 py-5">
        <h2 class="text-sm font-semibold text-white mb-4 flex items-center gap-2">
            <span class="w-5 h-5 brand-gradient rounded-md flex items-center justify-center text-white flex-shrink-0">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </span>
            Firma y notas
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="lugar_firma" class="block text-xs font-medium text-gray-400 mb-1.5">Lugar de firma</label>
                <input type="text" id="lugar_firma" name="lugar_firma" value="{{ old('lugar_firma', $contrato?->lugar_firma ?? 'San Luis Potosí, S.L.P.') }}"
                       class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('lugar_firma') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"/>
                @error('lugar_firma')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="fecha_firma" class="block text-xs font-medium text-gray-400 mb-1.5">
                    Fecha de firma <span class="text-red-500">*</span>
                </label>
                <input type="date" id="fecha_firma" name="fecha_firma" value="{{ old('fecha_firma', $contrato?->fecha_firma?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
                       class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('fecha_firma') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"/>
                @error('fecha_firma')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="sm:col-span-2">
                <label for="notas" class="block text-xs font-medium text-gray-400 mb-1.5">
                    Notas adicionales
                    <span class="text-xs text-gray-600 font-normal">(opcional)</span>
                </label>
                <textarea id="notas" name="notas" rows="2"
                          class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('notas') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                          placeholder="Condiciones especiales del contrato">{{ old('notas', $contrato?->notas) }}</textarea>
                @error('notas')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
    (function () {
        const anticipoInput = document.getElementById('anticipo');
        const detalles = document.getElementById('anticipo-detalles');
        if (!anticipoInput || !detalles) return;

        const toggle = () => {
            const value = parseFloat(anticipoInput.value) || 0;
            detalles.classList.toggle('hidden', value <= 0);
        };

        anticipoInput.addEventListener('input', toggle);
        anticipoInput.addEventListener('change', toggle);
        // Estado inicial (por si el form viene con old() lleno)
        toggle();
    })();
</script>
@endpush
@endonce
