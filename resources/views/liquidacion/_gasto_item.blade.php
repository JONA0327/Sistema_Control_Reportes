@php
    $estadoBadge = match($gasto->estado) {
        'aceptado'  => 'bg-green-500/10 text-green-400 border-green-500/20',
        'rechazado' => 'bg-red-500/10 text-red-400 border-red-500/20',
        default     => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
    };
@endphp

<div class="border border-gray-700/40 rounded-xl p-4" x-data="{ rechazando: false }">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm font-semibold text-white">
                {{ $gasto->label() }}
                @if($gasto->concepto)
                    <span class="text-xs text-gray-500 font-normal">· {{ $gasto->concepto }}</span>
                @endif
            </p>
            <p class="text-base font-bold text-white mt-0.5">${{ number_format($gasto->monto, 2) }}</p>
        </div>
        <span class="text-xs px-2.5 py-1 rounded-lg border font-medium whitespace-nowrap flex-shrink-0 {{ $estadoBadge }}">
            {{ ucfirst($gasto->estado) }}
        </span>
    </div>

    @if($gasto->evidencia_path)
        <div class="mt-3">
            <a href="{{ Storage::url($gasto->evidencia_path) }}" target="_blank">
                <img src="{{ Storage::url($gasto->evidencia_path) }}" class="w-32 h-24 object-cover rounded-lg border border-gray-700/50"/>
            </a>
        </div>
    @endif

    @if($gasto->estado === 'rechazado' && $gasto->motivo_rechazo)
        <p class="text-xs text-red-400 mt-2">Motivo: {{ $gasto->motivo_rechazo }}</p>
    @endif

    @if(($context ?? 'operador') === 'operador' && $gasto->liquidacion->estado === 'abierta' && ! ($soloLectura ?? false))
        <form method="POST" action="{{ route('liquidacion.gastos.destroy', $gasto) }}"
              onsubmit="return confirm('¿Eliminar este gasto?')" class="mt-3">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-3 py-1.5 bg-red-600/10 border border-red-600/30 text-red-400 text-xs font-medium rounded-lg hover:bg-red-600/20 transition-colors">
                Eliminar
            </button>
        </form>
    @endif

    @if(($context ?? 'operador') === 'admin' && $gasto->liquidacion->estado === 'cerrada' && $gasto->estado === 'pendiente')
        <div class="flex flex-wrap items-center gap-2 pt-3 mt-3 border-t border-gray-700/40">
            <form method="POST" action="{{ route('liquidacion.gastos.aceptar', $gasto) }}">
                @csrf @method('PATCH')
                <button type="submit" class="px-3 py-1.5 bg-green-600/20 border border-green-600/40 text-green-400 text-xs font-medium rounded-lg hover:bg-green-600/30 transition-colors">
                    Aceptar
                </button>
            </form>
            <button type="button" @click="rechazando = !rechazando"
                    class="px-3 py-1.5 bg-red-600/10 border border-red-600/30 text-red-400 text-xs font-medium rounded-lg hover:bg-red-600/20 transition-colors">
                Rechazar
            </button>
        </div>
        <div x-show="rechazando" x-cloak class="mt-3">
            <form method="POST" action="{{ route('liquidacion.gastos.rechazar', $gasto) }}" class="flex flex-wrap gap-2">
                @csrf @method('PATCH')
                <input type="text" name="motivo_rechazo" placeholder="Motivo (opcional)"
                       class="flex-1 min-w-0 px-3 py-2 bg-gray-900/80 border border-gray-700 rounded-lg text-white text-xs placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500"/>
                <button type="submit" class="px-3 py-2 bg-red-600 text-white text-xs font-medium rounded-lg hover:bg-red-700 transition-colors">
                    Confirmar rechazo
                </button>
            </form>
        </div>
    @endif
</div>
