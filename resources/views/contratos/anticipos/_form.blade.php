@php
    /** @var \App\Models\Contrato $contrato */
    $contrato = $contrato ?? null;
@endphp

<form method="POST"
      action="{{ route('contratos.anticipos.store', $contrato) }}"
      enctype="multipart/form-data"
      class="space-y-3">
    @csrf

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <div>
            <label for="anticipo-monto" class="block text-xs font-medium text-gray-400 mb-1.5">
                Monto del anticipo <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-500 text-sm">$</span>
                <input type="number" step="0.01" min="0.01"
                       id="anticipo-monto" name="monto"
                       value="{{ old('monto') }}"
                       class="w-full pl-7 pr-3 py-2.5 bg-gray-900/80 border {{ $errors->has('monto') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                       placeholder="0.00"
                       required>
            </div>
            @error('monto')
                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="anticipo-fecha" class="block text-xs font-medium text-gray-400 mb-1.5">
                Fecha <span class="text-red-500">*</span>
            </label>
            <input type="date"
                   id="anticipo-fecha" name="fecha_anticipo"
                   value="{{ old('fecha_anticipo', now()->format('Y-m-d')) }}"
                   class="w-full px-3 py-2.5 bg-gray-900/80 border {{ $errors->has('fecha_anticipo') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                   required>
            @error('fecha_anticipo')
                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="anticipo-metodo" class="block text-xs font-medium text-gray-400 mb-1.5">Método de pago</label>
            <select id="anticipo-metodo" name="metodo_pago"
                    class="w-full px-3 py-2.5 bg-gray-900/80 border border-gray-700 rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                <option value="" class="bg-gray-900">— Sin especificar —</option>
                @foreach(['Efectivo', 'Transferencia', 'Tarjeta', 'Depósito', 'Cheque'] as $metodo)
                    <option value="{{ $metodo }}" class="bg-gray-900" {{ old('metodo_pago') === $metodo ? 'selected' : '' }}>{{ $metodo }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="anticipo-evidencia" class="block text-xs font-medium text-gray-400 mb-1.5">
                Evidencia
                <span class="text-xs text-gray-600 font-normal">(jpg, png, pdf · máx 5MB)</span>
            </label>
            <input type="file"
                   id="anticipo-evidencia" name="evidencia"
                   accept="image/jpeg,image/png,image/gif,image/webp,application/pdf"
                   class="w-full text-xs text-gray-300 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-gray-700 file:text-white file:text-xs file:font-medium hover:file:bg-gray-600">
            @error('evidencia')
                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label for="anticipo-notas" class="block text-xs font-medium text-gray-400 mb-1.5">
            Notas
            <span class="text-xs text-gray-600 font-normal">(opcional)</span>
        </label>
        <input type="text"
               id="anticipo-notas" name="notas"
               value="{{ old('notas') }}"
               class="w-full px-3 py-2.5 bg-gray-900/80 border border-gray-700 rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
               placeholder="Referencia, folio de transferencia, observaciones...">
    </div>

    <div class="flex justify-end">
        <button type="submit"
                class="inline-flex items-center gap-2 px-4 py-2.5 brand-gradient text-white text-sm font-semibold rounded-xl shadow-lg shadow-red-950/40 hover:opacity-90 transition-opacity">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Registrar anticipo
        </button>
    </div>
</form>
