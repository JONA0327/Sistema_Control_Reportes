<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-gray-500">
            <span>Panel</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <a href="{{ route('viajes.index') }}" class="hover:text-gray-300 transition-colors">Viajes</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-white">Editar</span>
        </div>
    </x-slot>

    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('viajes.index') }}"
           class="flex items-center justify-center w-9 h-9 rounded-xl bg-gray-800/60 border border-gray-700/50 text-gray-400 hover:text-white hover:border-gray-600 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-white">Viaje {{ $viaje->no_contrato }}</h1>
            <p class="text-xs text-gray-500 mt-0.5">
                @if($viaje->esta_pendiente)
                    Pendiente — completa origen, destino, recorridos y gastos para activarlo
                @elseif(!$viaje->liquidacion || $viaje->liquidacion->estado === 'abierta')
                    En curso — el operador registra el km y cierra la liquidación desde Gastos
                @else
                    Finalizado
                @endif
            </p>
        </div>
    </div>

    @if($viaje->esta_pendiente)
        <div class="mb-6 max-w-3xl flex items-center gap-3 px-4 py-3 bg-red-500/10 border border-red-500/20 rounded-xl text-red-300 text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            Este viaje viene de un contrato y todavía le falta información operativa. Complétala y guarda para activarlo.
        </div>
    @endif

    @if($viaje->contrato)
        <div class="mb-6 max-w-3xl flex items-center justify-between gap-3 px-4 py-3 bg-gray-800/40 border border-gray-700/40 rounded-xl text-sm">
            <span class="text-gray-400">Generado desde el contrato <span class="text-white font-medium">{{ $viaje->contrato->folio }}</span> — {{ $viaje->contrato->cliente_nombre }}</span>
            <a href="{{ route('contratos.edit', $viaje->contrato) }}" class="text-red-400 hover:text-red-300 flex-shrink-0">Ver contrato</a>
        </div>
    @endif

    @if(!$viaje->liquidacion || $viaje->liquidacion->estado === 'abierta')
        <div class="mb-6 max-w-3xl bg-gray-800/40 border border-gray-700/40 rounded-2xl p-5">
            <h4 class="text-sm font-semibold text-white mb-1">Subir hoja de liquidación en Excel</h4>
            <p class="text-xs text-gray-500 mb-3">
                Si ya tienes la liquidación en el Excel de siempre, súbela aquí directamente: toma gastos entregados y diésel de salida para activar el viaje, y km, diésel de llegada y los demás gastos (casetas, hospedaje, comida, estacionamiento, lavada, otros) para cerrar la liquidación de una vez.
            </p>
            <form method="POST" action="{{ route('viajes.importar-excel', $viaje) }}" enctype="multipart/form-data" class="flex flex-wrap items-end gap-3">
                @csrf
                <div class="flex-1 min-w-[200px]">
                    <input type="file" name="excel" accept=".xlsx,.xls" required
                           class="w-full text-sm text-gray-300 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-gray-700 file:text-gray-200 hover:file:bg-gray-600 bg-gray-900/80 border border-gray-700 rounded-xl"/>
                </div>
                <button type="submit" class="btn-login-gradient px-5 py-2.5 text-white text-sm font-semibold rounded-xl shadow-lg shadow-red-950/40 whitespace-nowrap">
                    Importar desde Excel
                </button>
            </form>
            @error('excel')
                <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <p class="mb-3 max-w-3xl text-xs text-gray-600 text-center">— o completa los datos a mano —</p>
    @endif

    <div class="max-w-3xl">
        <form method="POST" action="{{ route('viajes.update', $viaje) }}">
            @csrf
            @method('PUT')
            @include('viajes._form', ['submitLabel' => $viaje->esta_pendiente ? 'Activar viaje' : 'Guardar cambios'])
        </form>
    </div>

</x-app-layout>
