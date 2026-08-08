@php
    $tiposMeta = \App\Models\LiquidacionGasto::TIPOS;
@endphp

<div class="border border-dashed border-gray-700 rounded-xl p-4" x-data="{ open: false, tipo: 'caseta', tiposMeta: @js($tiposMeta) }">
    <button type="button" @click="open = !open" class="flex items-center gap-2 text-sm font-medium text-gray-300 hover:text-white transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Agregar gasto
    </button>
    <div x-show="open" x-cloak>
        <form method="POST" action="{{ route('liquidacion.gastos.store', $liquidacion) }}" enctype="multipart/form-data" class="mt-4 space-y-3">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1.5">Categoría <span class="text-red-500">*</span></label>
                    <select name="tipo" x-model="tipo" required
                            class="w-full px-3 py-2 bg-gray-900/80 border border-gray-700 rounded-lg text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                        @foreach($tiposMeta as $key => $meta)
                            <option value="{{ $key }}" class="bg-gray-900">{{ $meta['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div x-show="tiposMeta[tipo] && tiposMeta[tipo].concepto_label" x-cloak>
                    <label class="block text-xs font-medium text-gray-400 mb-1.5" x-text="tiposMeta[tipo] ? tiposMeta[tipo].concepto_label : ''"></label>
                    <input type="text" name="concepto" :required="tiposMeta[tipo] && !!tiposMeta[tipo].concepto_label"
                           class="w-full px-3 py-2 bg-gray-900/80 border border-gray-700 rounded-lg text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500"/>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1.5">Monto <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" min="0" name="monto" required
                           class="w-full px-3 py-2 bg-gray-900/80 border border-gray-700 rounded-lg text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500"
                           placeholder="0.00"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1.5">Evidencia <span class="text-gray-600 font-normal">(foto, opcional)</span></label>
                    <input type="file" name="evidencia" accept="image/*"
                           class="w-full text-xs text-gray-400 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-gray-700 file:text-gray-200 hover:file:bg-gray-600"/>
                </div>
            </div>
            <button type="submit" class="px-4 py-2 brand-gradient text-white text-xs font-semibold rounded-lg shadow-lg shadow-red-950/40 hover:opacity-90 transition-opacity">
                Agregar gasto
            </button>
        </form>
    </div>
</div>
