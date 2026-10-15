<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-gray-500">
            <span>Panel</span>
            <a href="{{ route('contratos-historicos.index') }}" class="hover:text-gray-300 transition-colors">/ Contratos históricos</a>
            <span class="text-white">/ Del sistema</span>
        </div>
    </x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white">Contratos del sistema antes del corte</h1>
        <p class="text-sm text-gray-500 mt-0.5">No cuentan en Ingresos y Egresos. Aquí se capturan sus liquidaciones y gastos con la fecha que corresponda.</p>
    </div>

    @if(session('success'))<div class="mb-4 rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-400">{{ session('success') }}</div>@endif

    @role('superadmin')
        <form method="POST" action="{{ route('contratos-historicos.sistema.corte') }}" class="bg-gray-800/40 border border-gray-700/40 rounded-2xl p-5 mb-6 flex flex-wrap items-end gap-3"
              onsubmit="return confirm('Esto ajusta qué movimientos cuentan en Ingresos y Egresos. ¿Continuar?')">
            @csrf
            <div>
                <label for="fecha" class="block text-xs font-medium text-gray-400 mb-1.5">Ignorar contratos con salida hasta (inclusive)</label>
                <input type="date" id="fecha" name="fecha" value="{{ $corte?->format('Y-m-d') }}"
                       class="px-3.5 py-2.5 bg-gray-900/80 border border-gray-700 rounded-xl text-white text-sm focus:outline-none focus:border-red-500">
            </div>
            <button type="submit" class="px-4 py-2.5 bg-red-600 hover:bg-red-500 text-white text-sm font-medium rounded-xl">{{ $corte ? 'Actualizar corte' : 'Activar corte' }}</button>
            @if($corte)
                <button type="submit" onclick="document.getElementById('fecha').value=''" class="px-4 py-2.5 bg-gray-700/60 hover:bg-gray-700 text-gray-200 text-sm font-medium rounded-xl">Desactivar</button>
            @endif
            <p class="w-full text-xs text-gray-500">{{ $corte ? 'Corte activo hasta el '.$corte->format('d/m/Y').'.' : 'Corte apagado: todos los contratos cuentan normal.' }}</p>
        </form>
    @endrole

    <form method="GET" class="mb-4">
        <input type="text" name="search" value="{{ $search }}" placeholder="Buscar cliente, destino o folio…"
               class="w-full sm:w-80 px-3.5 py-2.5 bg-gray-900/80 border border-gray-700 rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500">
    </form>

    <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead class="text-xs text-gray-500 uppercase border-b border-gray-700/40">
                <tr><th class="px-5 py-3 text-left">Folio</th><th class="px-5 py-3 text-left">Cliente</th><th class="px-5 py-3 text-left">Salida</th><th class="px-5 py-3 text-right">Costo</th><th class="px-5 py-3 text-right">Capturados</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($contratos as $c)
                    <tr class="border-b border-gray-700/30">
                        <td class="px-5 py-3 text-gray-200">{{ $c->folio }}</td>
                        <td class="px-5 py-3 text-gray-300">{{ $c->cliente_nombre }}</td>
                        <td class="px-5 py-3 text-gray-400">{{ $c->fecha_salida?->format('d/m/Y') }}</td>
                        <td class="px-5 py-3 text-right text-gray-300">${{ number_format($c->costo_viaje, 2) }}</td>
                        <td class="px-5 py-3 text-right text-gray-400">{{ $c->manuales_count }}</td>
                        <td class="px-5 py-3 text-right"><a href="{{ route('contratos-historicos.sistema.show', $c) }}" class="text-red-400 hover:text-red-300 text-xs font-medium">Abrir</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-gray-500">{{ $corte ? 'Ningún contrato cae antes del corte.' : 'El corte está apagado.' }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $contratos->links() }}</div>
</x-app-layout>
