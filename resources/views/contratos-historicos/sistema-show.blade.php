<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-gray-500">
            <span>Panel</span>
            <a href="{{ route('contratos-historicos.sistema') }}" class="hover:text-gray-300 transition-colors">/ Históricos del sistema</a>
            <span class="text-white">/ {{ $contrato->folio }}</span>
        </div>
    </x-slot>

    @php $money = fn ($v) => '$'.number_format((float) $v, 2); @endphp

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white">{{ $contrato->folio }} — {{ $contrato->cliente_nombre }}</h1>
        <p class="text-sm text-gray-500 mt-0.5">{{ $contrato->salida_destino }} · Salida {{ $contrato->fecha_salida?->format('d/m/Y') }} · Costo {{ $money($contrato->costo_viaje) }} · {{ $contrato->tipo_label }}</p>
    </div>

    @if(session('success'))<div class="mb-4 rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-400">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="mb-4 rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-sm text-red-400">{{ $errors->first() }}</div>@endif

    @can('egresos_ingresos.crear')
        <form method="POST" action="{{ route('contratos-historicos.sistema.movimientos.store', $contrato) }}" class="bg-gray-800/40 border border-gray-700/40 rounded-2xl p-5 mb-6">
            @csrf
            <h2 class="text-sm font-semibold text-white mb-4">Registrar liquidación o gasto</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <select name="tipo" class="px-3 py-2.5 bg-gray-900/80 border border-gray-700 rounded-xl text-white text-sm">
                    <option value="egreso" @selected(old('tipo') === 'egreso')>Egreso</option>
                    <option value="ingreso" @selected(old('tipo') === 'ingreso')>Ingreso</option>
                </select>
                <select name="categoria" class="px-3 py-2.5 bg-gray-900/80 border border-gray-700 rounded-xl text-white text-sm">
                    @foreach($categorias as $cat)<option @selected(old('categoria') === $cat)>{{ $cat }}</option>@endforeach
                </select>
                <input type="text" name="concepto" value="{{ old('concepto') }}" placeholder="Concepto" class="px-3 py-2.5 bg-gray-900/80 border border-gray-700 rounded-xl text-white text-sm">
                <input type="number" step="0.01" min="0.01" name="monto" value="{{ old('monto') }}" placeholder="Monto" class="px-3 py-2.5 bg-gray-900/80 border border-gray-700 rounded-xl text-white text-sm">
                <input type="date" name="fecha" value="{{ old('fecha', $contrato->fecha_regreso?->format('Y-m-d') ?? $contrato->fecha_salida?->format('Y-m-d')) }}" class="px-3 py-2.5 bg-gray-900/80 border border-gray-700 rounded-xl text-white text-sm">
            </div>
            <button type="submit" class="mt-4 px-4 py-2.5 bg-red-600 hover:bg-red-500 text-white text-sm font-medium rounded-xl">Guardar</button>
        </form>
    @endcan

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl p-5">
            <h2 class="text-sm font-semibold text-white mb-1">Capturados aquí (sí cuentan en Ingresos y Egresos)</h2>
            @forelse($manuales as $m)
                <div class="flex items-center justify-between py-2 border-b border-gray-700/40 text-sm">
                    <span class="text-gray-300">{{ $m->fecha->format('d/m/Y') }} · {{ $m->categoria }} · {{ $m->concepto }}</span>
                    <span class="flex items-center gap-3">
                        <span class="{{ $m->tipo === 'egreso' ? 'text-red-400' : 'text-emerald-400' }}">{{ $m->tipo === 'egreso' ? '-' : '+' }}{{ $money($m->monto) }}</span>
                        @can('egresos_ingresos.eliminar')
                            <form method="POST" action="{{ route('contratos-historicos.sistema.movimientos.destroy', $m) }}" onsubmit="return confirm('¿Eliminar este movimiento?')">
                                @csrf @method('DELETE')
                                <button class="text-xs text-gray-500 hover:text-red-400">Quitar</button>
                            </form>
                        @endcan
                    </span>
                </div>
            @empty
                <p class="text-sm text-gray-500 mt-2">Nada capturado todavía.</p>
            @endforelse
        </div>

        <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl p-5">
            <h2 class="text-sm font-semibold text-white mb-1">Generados por el sistema (fuera de Ingresos y Egresos)</h2>
            @forelse($excluidos as $m)
                <div class="flex justify-between py-2 border-b border-gray-700/40 text-sm">
                    <span class="text-gray-400">{{ $m->fecha->format('d/m/Y') }} · {{ $m->concepto }}</span>
                    <span class="text-gray-500">{{ $m->tipo === 'egreso' ? '-' : '+' }}{{ $money($m->monto) }}</span>
                </div>
            @empty
                <p class="text-sm text-gray-500 mt-2">El sistema no generó movimientos para este contrato.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
