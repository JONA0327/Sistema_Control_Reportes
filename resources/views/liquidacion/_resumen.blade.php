@php
    $resumen = $liquidacion->resumen();
@endphp

<div class="border border-gray-700/40 rounded-xl p-4">
    <div class="flex items-center justify-between gap-3 mb-3">
        <h3 class="text-sm font-semibold text-white">Liquidación</h3>
        <span class="text-xs px-2.5 py-1 rounded-lg border font-medium whitespace-nowrap {{ $liquidacion->estado === 'cerrada' ? 'bg-green-500/10 text-green-400 border-green-500/20' : 'bg-amber-500/10 text-amber-400 border-amber-500/20' }}">
            {{ $liquidacion->estado === 'cerrada' ? 'Cerrada' : 'Abierta' }}
        </span>
    </div>

    {{-- Km --}}
    @if($editable)
        <form method="POST" action="{{ route('liquidacion.km.update', $liquidacion) }}" class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
            @csrf
            @method('PATCH')
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1.5">Km inicial <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0" name="km_inicial" value="{{ old('km_inicial', $liquidacion->km_inicial) }}" required
                       class="w-full px-3 py-2 bg-gray-900/80 border border-gray-700 rounded-lg text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500"/>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1.5">Km final <span class="text-gray-600 font-normal">(al terminar el viaje)</span></label>
                <input type="number" step="0.01" min="0" name="km_final" value="{{ old('km_final', $liquidacion->km_final) }}"
                       class="w-full px-3 py-2 bg-gray-900/80 border border-gray-700 rounded-lg text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500"/>
            </div>
            <div class="sm:col-span-2">
                <button type="submit" class="px-4 py-2 bg-gray-700/60 border border-gray-600/40 text-gray-200 text-xs font-medium rounded-lg hover:bg-gray-700 transition-colors">
                    Guardar km
                </button>
            </div>
        </form>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
            <div>
                <p class="text-xs text-gray-500">Km inicial</p>
                <p class="text-sm text-white font-medium">{{ $liquidacion->km_inicial !== null ? number_format($liquidacion->km_inicial, 2) : '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Km final</p>
                <p class="text-sm text-white font-medium">{{ $liquidacion->km_final !== null ? number_format($liquidacion->km_final, 2) : '—' }}</p>
            </div>
        </div>
    @endif

    {{-- Rendimiento --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-3 border-t border-gray-700/30 mb-3">
        <div>
            <p class="text-xs text-gray-500">Km totales</p>
            <p class="text-sm text-white font-medium">{{ $resumen['km_total'] !== null ? number_format($resumen['km_total'], 2) : '—' }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500">Litros consumidos</p>
            <p class="text-sm text-white font-medium">{{ number_format($resumen['litros_consumidos'], 2) }} L</p>
        </div>
        <div>
            <p class="text-xs text-gray-500">Rendimiento</p>
            <p class="text-sm text-white font-medium">{{ $resumen['rendimiento_km_l'] !== null ? number_format($resumen['rendimiento_km_l'], 2).' km/L' : '—' }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500">Consumo</p>
            <p class="text-sm text-white font-medium">{{ $resumen['consumo_l_100km'] !== null ? number_format($resumen['consumo_l_100km'], 2).' L/100km' : '—' }}</p>
        </div>
    </div>

    {{-- Gastos --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 pt-3 border-t border-gray-700/30">
        <div>
            <p class="text-xs text-gray-500">Gastos entregados</p>
            <p class="text-sm text-white font-medium">${{ number_format($liquidacion->viaje->gastos_entregados, 2) }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500">Gastos realizados</p>
            <p class="text-sm text-white font-medium">${{ number_format($resumen['total_gastos_realizados'], 2) }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500">Ganancia estimada <span class="text-gray-600">(15%)</span></p>
            <p class="text-sm text-white font-medium">${{ number_format($resumen['ganancia_estimada'], 2) }}</p>
        </div>
    </div>

    @if($resumen['exceso_gasto'] > 0)
        <div class="mt-3 px-3 py-2 bg-red-500/10 border border-red-500/20 rounded-lg">
            <p class="text-xs text-red-400 font-medium">Exceso de gasto: ${{ number_format($resumen['exceso_gasto'], 2) }} por encima de lo entregado.</p>
        </div>
    @else
        <div class="mt-3 px-3 py-2 bg-green-500/10 border border-green-500/20 rounded-lg">
            <p class="text-xs text-green-400 font-medium">Sobrante a devolver: ${{ number_format($resumen['sobrante_a_devolver'], 2) }}</p>
        </div>
    @endif

    @error('km')
        <p class="mt-3 text-xs text-red-400">{{ $message }}</p>
    @enderror
</div>
