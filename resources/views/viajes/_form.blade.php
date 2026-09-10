<div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl divide-y divide-gray-700/40">

    {{-- Datos del viaje --}}
    <div class="px-6 py-5">
        <h2 class="text-sm font-semibold text-white mb-4 flex items-center gap-2">
            <span class="w-5 h-5 brand-gradient rounded-md flex items-center justify-center flex-shrink-0">
                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
            </span>
            Datos del viaje
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

            {{-- No. Contrato --}}
            <div>
                <label for="no_contrato" class="block text-xs font-medium text-gray-400 mb-1.5">
                    No. Contrato <span class="text-red-500">*</span>
                </label>
                <input type="text" id="no_contrato" name="no_contrato" value="{{ old('no_contrato', $viaje->no_contrato ?? '') }}"
                       class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('no_contrato') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                       placeholder="Ej: CTR-2026-014"/>
                @error('no_contrato')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Unidad --}}
            <div>
                <label for="bus_id" class="block text-xs font-medium text-gray-400 mb-1.5">
                    Unidad <span class="text-red-500">*</span>
                </label>
                @if($buses->isEmpty())
                    <div class="flex items-center gap-3 px-4 py-3 bg-amber-500/10 border border-amber-500/20 rounded-xl">
                        <svg class="w-4 h-4 text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <p class="text-xs text-amber-300">No hay unidades activas disponibles.</p>
                    </div>
                @else
                    <select id="bus_id" name="bus_id" onchange="viajeAutoOperador(this)"
                            class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('bus_id') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                        <option value="" class="bg-gray-900">— Seleccionar unidad —</option>
                        @foreach($buses as $bus)
                            <option value="{{ $bus->id }}" data-operador="{{ $bus->operator_id }}" data-copiloto="{{ $bus->copiloto_id }}" class="bg-gray-900"
                                    {{ old('bus_id', $viaje->bus_id ?? '') == $bus->id ? 'selected' : '' }}>
                                Bus #{{ $bus->num_bus }} — {{ $bus->placa }}
                            </option>
                        @endforeach
                    </select>
                @endif
                @error('bus_id')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Operador --}}
            <div class="sm:col-span-2">
                <label for="operador_id" class="block text-xs font-medium text-gray-400 mb-1.5">
                    Operador <span class="text-red-500">*</span>
                </label>
                @if($operadores->isEmpty())
                    <div class="flex items-center gap-3 px-4 py-3 bg-amber-500/10 border border-amber-500/20 rounded-xl">
                        <svg class="w-4 h-4 text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <p class="text-xs text-amber-300">No hay operadores activos disponibles.</p>
                    </div>
                @else
                    <select id="operador_id" name="operador_id"
                            class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('operador_id') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                        <option value="" class="bg-gray-900">— Seleccionar operador —</option>
                        @foreach($operadores as $op)
                            <option value="{{ $op->id }}" class="bg-gray-900"
                                    {{ old('operador_id', $viaje->operador_id ?? '') == $op->id ? 'selected' : '' }}>
                                {{ $op->name }} {{ $op->last_name }} ({{ $op->carnet }})
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1.5 text-xs text-gray-600">Se autocompleta con el operador asignado a la unidad. Cámbialo si un operador suplente cubre este viaje.</p>
                @endif
                @error('operador_id')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Doble operador --}}
            <div class="sm:col-span-2" x-data="{ doble: {{ old('segundo_operador_id', $viaje->segundo_operador_id ?? '') ? 'true' : 'false' }} }">
                <label class="flex items-center gap-2 cursor-pointer select-none mb-1.5">
                    <input type="checkbox" id="doble_operador_toggle" x-model="doble" @change="if (!doble) document.getElementById('segundo_operador_id').value = ''"
                           class="w-4 h-4 rounded border-gray-600 bg-gray-900 text-red-600 focus:ring-red-500 focus:ring-offset-gray-800"/>
                    <span class="text-xs font-medium text-gray-400">¿Va con doble operador?</span>
                </label>
                <p class="text-xs text-gray-600 mb-2">Los gastos y la liquidación los sigue gestionando el operador titular. El segundo operador solo puede consultarlos. Con doble operador, cada uno gana 10% del costo del viaje (en vez del 15% de un solo operador).</p>

                <div x-show="doble" x-cloak>
                    @if($operadores->isEmpty())
                        <p class="text-xs text-amber-300">No hay operadores activos disponibles.</p>
                    @else
                        <select id="segundo_operador_id" name="segundo_operador_id"
                                class="w-full sm:w-96 px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('segundo_operador_id') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                            <option value="" class="bg-gray-900">— Seleccionar segundo operador —</option>
                            @foreach($operadores as $op)
                                <option value="{{ $op->id }}" class="bg-gray-900"
                                        {{ old('segundo_operador_id', $viaje->segundo_operador_id ?? '') == $op->id ? 'selected' : '' }}>
                                    {{ $op->name }} {{ $op->last_name }} ({{ $op->carnet }})
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1.5 text-xs text-gray-600">Se sugiere el copiloto asignado a la unidad, pero puedes elegir a cualquier otro operador para este viaje.</p>
                    @endif
                </div>
                @error('segundo_operador_id')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Origen --}}
            <div>
                <label for="origen" class="block text-xs font-medium text-gray-400 mb-1.5">
                    Origen <span class="text-red-500">*</span>
                </label>
                <input type="text" id="origen" name="origen" value="{{ old('origen', $viaje->origen ?? '') }}"
                       class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('origen') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                       placeholder="Ej: Terminal Central"/>
                @error('origen')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Destino --}}
            <div>
                <label for="destino" class="block text-xs font-medium text-gray-400 mb-1.5">
                    Destino <span class="text-red-500">*</span>
                </label>
                <input type="text" id="destino" name="destino" value="{{ old('destino', $viaje->destino ?? '') }}"
                       class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('destino') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                       placeholder="Ej: Puerto de carga"/>
                @error('destino')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Recorridos --}}
            <div class="sm:col-span-2">
                <label for="recorridos" class="block text-xs font-medium text-gray-400 mb-1.5">
                    Recorridos <span class="text-red-500">*</span>
                </label>
                <textarea id="recorridos" name="recorridos" rows="2"
                          class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('recorridos') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors resize-none"
                          placeholder="Describe las rutas o tramos recorridos...">{{ old('recorridos', $viaje->recorridos ?? '') }}</textarea>
                @error('recorridos')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Fecha salida --}}
            <div>
                <label for="fecha_salida" class="block text-xs font-medium text-gray-400 mb-1.5">
                    Fecha de salida <span class="text-red-500">*</span>
                </label>
                <input type="date" id="fecha_salida" name="fecha_salida"
                       value="{{ old('fecha_salida', isset($viaje) ? $viaje->fecha_salida?->format('Y-m-d') : '') }}"
                       class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('fecha_salida') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"/>
                @error('fecha_salida')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Fecha regreso --}}
            <div>
                <label for="fecha_regreso" class="block text-xs font-medium text-gray-400 mb-1.5">
                    Fecha de regreso <span class="text-red-500">*</span>
                </label>
                <input type="date" id="fecha_regreso" name="fecha_regreso"
                       value="{{ old('fecha_regreso', isset($viaje) ? $viaje->fecha_regreso?->format('Y-m-d') : '') }}"
                       class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('fecha_regreso') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"/>
                @error('fecha_regreso')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- Costos y combustible --}}
    <div class="px-6 py-5">
        <h2 class="text-sm font-semibold text-white mb-4 flex items-center gap-2">
            <span class="w-5 h-5 brand-gradient rounded-md flex items-center justify-center flex-shrink-0">
                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </span>
            Costos y combustible
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

            {{-- Costo de viaje --}}
            <div>
                <label for="costo_viaje" class="block text-xs font-medium text-gray-400 mb-1.5">
                    Costo de viaje <span class="text-red-500">*</span>
                </label>
                <input type="number" step="0.01" min="0" id="costo_viaje" name="costo_viaje"
                       value="{{ old('costo_viaje', $viaje->costo_viaje ?? '') }}"
                       class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('costo_viaje') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                       placeholder="0.00"/>
                @error('costo_viaje')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Gastos entregados --}}
            <div>
                <label for="gastos_entregados" class="block text-xs font-medium text-gray-400 mb-1.5">
                    Gastos entregados <span class="text-red-500">*</span>
                </label>
                <input type="number" step="0.01" min="0" id="gastos_entregados" name="gastos_entregados"
                       value="{{ old('gastos_entregados', $viaje->gastos_entregados ?? '') }}"
                       class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('gastos_entregados') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                       placeholder="0.00"/>
                @error('gastos_entregados')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Gasto diésel inicio --}}
            <div>
                <label for="gasto_diesel_inicio" class="block text-xs font-medium text-gray-400 mb-1.5">
                    Diésel de salida <span class="text-red-500">*</span>
                </label>
                <input type="number" step="0.01" min="0" id="gasto_diesel_inicio" name="gasto_diesel_inicio"
                       value="{{ old('gasto_diesel_inicio', $viaje->gasto_diesel_inicio ?? '') }}"
                       class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('gasto_diesel_inicio') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                       placeholder="0.00"/>
                @error('gasto_diesel_inicio')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Litros diésel inicio --}}
            <div>
                <label for="litros_diesel_inicio" class="block text-xs font-medium text-gray-400 mb-1.5">
                    Litros de salida <span class="text-gray-600 font-normal">(opcional)</span>
                </label>
                <input type="number" step="0.01" min="0" id="litros_diesel_inicio" name="litros_diesel_inicio"
                       value="{{ old('litros_diesel_inicio', $viaje->litros_diesel_inicio ?? '') }}"
                       class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('litros_diesel_inicio') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                       placeholder="0.00"/>
                @error('litros_diesel_inicio')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Costo por litro diésel inicio --}}
            <div>
                <label for="costo_litro_diesel_inicio" class="block text-xs font-medium text-gray-400 mb-1.5">
                    Costo por litro de salida <span class="text-gray-600 font-normal">(opcional)</span>
                </label>
                <input type="number" step="0.01" min="0" id="costo_litro_diesel_inicio" name="costo_litro_diesel_inicio"
                       value="{{ old('costo_litro_diesel_inicio', $viaje->costo_litro_diesel_inicio ?? '') }}"
                       class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('costo_litro_diesel_inicio') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                       placeholder="0.00"/>
                <p class="mt-1 text-xs text-gray-600">El diésel extra que registre el operador durante el viaje se suma como el diésel de regreso.</p>
                @error('costo_litro_diesel_inicio')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- Acciones --}}
    <div class="px-6 py-4 flex flex-col-reverse sm:flex-row sm:items-center justify-end gap-3">
        <a href="{{ route('viajes.index') }}"
           class="w-full sm:w-auto text-center px-5 py-2.5 bg-gray-700/50 border border-gray-600/40 text-gray-300 text-sm font-medium rounded-xl hover:bg-gray-700 transition-colors">
            Cancelar
        </a>
        <button type="submit"
                class="w-full sm:w-auto btn-login-gradient px-5 py-2.5 text-white text-sm font-semibold rounded-xl shadow-lg shadow-red-950/40">
            {{ $submitLabel }}
        </button>
    </div>
</div>

<script>
    function viajeAutoOperador(busSelect) {
        const opt = busSelect.options[busSelect.selectedIndex];
        const operadorId = opt ? opt.dataset.operador : '';
        const operadorSelect = document.getElementById('operador_id');
        if (operadorSelect && operadorId && operadorSelect.querySelector(`option[value="${operadorId}"]`)) {
            operadorSelect.value = operadorId;
        }

        // Sugiere el copiloto de la unidad como segundo operador, sin forzar
        // la casilla si el usuario ya la había desmarcado a propósito.
        const copilotoId = opt ? opt.dataset.copiloto : '';
        const segundoSelect = document.getElementById('segundo_operador_id');
        const toggle = document.getElementById('doble_operador_toggle');
        if (!toggle || !copilotoId) return;
        if (segundoSelect && segundoSelect.querySelector(`option[value="${copilotoId}"]`)) {
            if (!toggle.checked) {
                toggle.checked = true;
                toggle.dispatchEvent(new Event('change'));
            }
            segundoSelect.value = copilotoId;
        }
    }
</script>
