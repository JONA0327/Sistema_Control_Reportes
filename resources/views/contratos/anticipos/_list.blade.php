@php
    /** @var \App\Models\Contrato $contrato */
    $anticipos = $contrato->anticipos;
@endphp

@if($anticipos->isEmpty())
    <p class="text-xs text-gray-600 italic">Aún no hay anticipos formalizados. Registra el primero con el formulario de arriba para generar su folio, evidencia y comprobante PDF.</p>
@else
    <div class="space-y-2">
        @foreach($anticipos as $anticipo)
            <div class="px-3.5 py-3 bg-gray-900/40 border rounded-xl {{ $anticipo->cancelado ? 'border-gray-800 opacity-60' : 'border-gray-700/40' }}" x-data="{ cancelando: false }">
                <div class="flex items-start justify-between gap-3 flex-wrap">
                    <div class="flex items-start gap-3 flex-1 min-w-0">
                        <span class="inline-flex items-center gap-1.5 text-xs px-2 py-1 rounded-lg border font-mono font-bold shrink-0
                                     {{ $anticipo->cancelado ? 'bg-gray-800 text-gray-500 border-gray-700' : 'bg-amber-500/10 text-amber-300 border-amber-500/30' }}">
                            {{ $anticipo->folio }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="inline-flex items-center gap-1.5 text-xs px-2 py-0.5 rounded-lg font-bold
                                             {{ $anticipo->cancelado ? 'bg-gray-800 text-gray-500 border border-gray-700 line-through' : 'bg-green-500/10 text-green-400 border border-green-500/20' }}">
                                    +${{ number_format($anticipo->monto, 2) }}
                                </span>
                                @if($anticipo->cancelado)
                                    <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-lg font-medium bg-red-500/10 text-red-400 border border-red-500/20">
                                        Cancelado
                                    </span>
                                @endif
                                <span class="text-xs text-gray-400">
                                    {{ $anticipo->metodo_pago ?: 'Sin método especificado' }}
                                </span>
                                <span class="text-xs text-gray-600">·</span>
                                <span class="text-xs text-gray-500">
                                    {{ $anticipo->user->name ?? '—' }}
                                </span>
                            </div>
                            @if($anticipo->notas)
                                <p class="text-xs text-gray-500 mt-1 truncate" title="{{ $anticipo->notas }}">{{ $anticipo->notas }}</p>
                            @endif
                            @if($anticipo->cancelado)
                                <p class="text-xs text-red-400 mt-1">
                                    Cancelado por {{ $anticipo->canceladoPor->name ?? '—' }} el {{ $anticipo->cancelado_at->format('d/m/Y H:i') }}
                                    @if($anticipo->motivo_cancelacion) — {{ $anticipo->motivo_cancelacion }} @endif
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs text-gray-600">{{ $anticipo->fecha_anticipo->format('d/m/Y') }}</span>

                        @if($anticipo->tieneEvidencia())
                            <a href="{{ route('contratos.anticipos.evidencia', [$contrato, $anticipo]) }}"
                               target="_blank"
                               class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-lg bg-blue-500/10 text-blue-300 border border-blue-500/20 hover:bg-blue-500/20 transition-colors"
                               title="Ver evidencia ({{ $anticipo->evidencia_nombre_original }})">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                </svg>
                                Evidencia
                            </a>
                        @else
                            <span class="text-xs text-gray-600 italic">Sin evidencia</span>
                        @endif

                        <a href="{{ route('contratos.anticipos.comprobante', [$contrato, $anticipo]) }}"
                           target="_blank"
                           class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-lg bg-red-500/10 text-red-300 border border-red-500/20 hover:bg-red-500/20 transition-colors"
                           title="Descargar comprobante PDF para el cliente">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            PDF
                        </a>

                        @unless($anticipo->cancelado)
                            <button type="button" @click="cancelando = !cancelando"
                                    class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-lg bg-gray-700/40 text-gray-400 border border-gray-600/40 hover:bg-red-500/10 hover:text-red-400 hover:border-red-500/20 transition-colors"
                                    title="Cancelar anticipo">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Cancelar
                            </button>
                        @endunless
                    </div>
                </div>

                @unless($anticipo->cancelado)
                <div x-show="cancelando" x-cloak class="mt-3 pt-3 border-t border-gray-700/40">
                    <form method="POST" action="{{ route('contratos.anticipos.cancelar', [$contrato, $anticipo]) }}" class="flex flex-wrap gap-2">
                        @csrf
                        @method('PATCH')
                        <input type="text" name="motivo_cancelacion" required placeholder="Motivo de la cancelación (obligatorio)"
                               class="flex-1 min-w-0 px-3 py-2 bg-gray-900/80 border border-gray-700 rounded-lg text-white text-xs placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500"/>
                        <button type="submit" class="px-3 py-2 bg-red-600 text-white text-xs font-medium rounded-lg hover:bg-red-700 transition-colors">
                            Confirmar cancelación
                        </button>
                    </form>
                    <p class="mt-1.5 text-xs text-gray-600">El anticipo no se borra: queda marcado como cancelado, con folio y motivo, y deja de contar en el saldo.</p>
                </div>
                @endunless
            </div>
        @endforeach
    </div>
@endif
