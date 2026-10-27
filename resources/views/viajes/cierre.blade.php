<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-gray-500">
            <span>Panel</span>
            <a href="{{ route('viajes.index') }}" class="hover:text-gray-300 transition-colors">/ Viajes</a>
            <span class="text-white">/ Cierre</span>
        </div>
    </x-slot>

    @php
        $money = fn ($v) => '$'.number_format((float) $v, 2);
        $totalEgresos = $egresos->sum('monto');
        $esCompras = $viaje->es_compras;
        $costoContrato = (float) $viaje->contrato->costo_viaje;
        $lineasCorte = $esCompras ? $cortes->flatMap->lines : collect();
        $otrosIngresos = (float) $lineasCorte->where('kind', 'ingreso')->where('cuenta', true)->sum('monto');
        $totalIngresos = $esCompras ? $cortes->sum('monto') + $otrosIngresos : $costoContrato;
        $liq = $viaje->liquidacion;
    @endphp

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white">{{ $esCompras ? 'Cierre de viaje' : 'Resultado del viaje' }} — {{ $viaje->no_contrato }}</h1>
        <p class="text-sm text-gray-500 mt-0.5">
            Contrato {{ $viaje->contrato->folio }} · {{ $viaje->origen }} &rarr; {{ $viaje->destino }}
            @if($viaje->cerrado_at) · <span class="text-emerald-400">Cerrado {{ $viaje->cerrado_at->format('d/m/Y H:i') }}</span> @endif
        </p>
    </div>

    @if(session('success'))<div class="mb-4 rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-400">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="mb-4 rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-sm text-red-400">{{ session('error') }}</div>@endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl p-5">
            <p class="text-xs text-gray-500 uppercase">Egresos</p>
            <p class="mt-1 text-2xl font-bold text-red-400">{{ $money($totalEgresos) }}</p>
        </div>
        <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl p-5">
            <p class="text-xs text-gray-500 uppercase">{{ $esCompras ? 'Ingresos (boletos Merlo)' : 'Ingresos (costo del contrato)' }}</p>
            <p class="mt-1 text-2xl font-bold text-emerald-400">{{ $money($totalIngresos) }}</p>
        </div>
        <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl p-5">
            <p class="text-xs text-gray-500 uppercase">Resultado del viaje</p>
            <p class="mt-1 text-2xl font-bold {{ $totalIngresos - $totalEgresos >= 0 ? 'text-emerald-400' : 'text-red-400' }}">{{ $money($totalIngresos - $totalEgresos) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl p-5">
            <h2 class="text-sm font-semibold text-white mb-3">{{ $esCompras ? 'Egresos que se enviarán' : 'Egresos del viaje' }}</h2>
            @forelse($egresos as $e)
                <div class="flex justify-between py-2 border-b border-gray-700/40 text-sm">
                    <span class="text-gray-300">{{ $e['concepto'] }}</span>
                    <span class="text-gray-200">{{ $money($e['monto']) }}</span>
                </div>
            @empty
                <p class="text-sm text-gray-500">Sin egresos.</p>
            @endforelse
            <div class="flex justify-between pt-3 text-sm font-semibold"><span class="text-gray-400">Total egresos</span><span class="text-red-400">{{ $money($totalEgresos) }}</span></div>
        </div>

        @unless($esCompras)
        <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl p-5">
            <h2 class="text-sm font-semibold text-white mb-3">Ingresos del contrato ({{ $viaje->contrato->tipo_label }})</h2>
            <div class="flex justify-between py-2 border-b border-gray-700/40 text-sm"><span class="text-gray-300">Costo del contrato {{ $viaje->contrato->folio }} · {{ $viaje->contrato->cliente_nombre }}</span><span class="text-gray-200">{{ $money($costoContrato) }}</span></div>
            <p class="text-xs text-gray-500 mt-1">Cobrado hasta hoy {{ $money($viaje->contrato->total_pagado) }} · por cobrar {{ $money($viaje->contrato->saldo_pendiente) }}</p>
            <div class="flex justify-between pt-3 text-sm font-semibold"><span class="text-gray-400">Total ingresos</span><span class="text-emerald-400">{{ $money($costoContrato) }}</span></div>
            <p class="text-xs text-gray-500 mt-3">Los anticipos y abonos ya entran a Ingresos y Egresos al registrarse; aquí el costo total del contrato se usa para calcular el resultado.</p>
        </div>
        @else
        <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl p-5">
            <h2 class="text-sm font-semibold text-white mb-3">Ingresos que se enviarán (Merlo Web)</h2>
            @forelse($cortes as $c)
                <div class="py-2 border-b border-gray-700/40 text-sm">
                    <div class="flex justify-between"><span class="text-gray-300">{{ $c->ruta }} · {{ $c->fecha->format('d/m/Y') }}</span><span class="text-gray-200">{{ $money($c->monto) }}</span></div>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $c->sold_seats }} vendidos ({{ $c->late_sold_seats }} después) · {{ $c->free_seats }} sobraron de {{ $c->total_seats }} · vendido {{ $money($c->sold_amount) }} (abonos ya recibidos {{ $money($c->abonos_amount) }}) + vendido sin apartar en guía {{ $money($c->external_amount) }} ({{ $c->external_seats }} asientos)</p>
                </div>
            @empty
                <p class="text-sm text-gray-500">Merlo aún no envía el corte de este viaje.</p>
            @endforelse
            @foreach($lineasCorte->where('kind', 'ingreso')->where('cuenta', true) as $l)
                <div class="flex justify-between py-2 border-b border-gray-700/40 text-sm"><span class="text-gray-300">Corte Merlo: {{ $l->concepto }}</span><span class="text-gray-200">{{ $money($l->monto) }}</span></div>
            @endforeach
            <div class="flex justify-between pt-3 text-sm font-semibold"><span class="text-gray-400">Total ingresos</span><span class="text-emerald-400">{{ $money($totalIngresos) }}</span></div>
        </div>
        @endunless
    </div>

    @if($esCompras && $cortes->isNotEmpty())
        <h2 class="text-sm font-semibold text-white mb-3">Corte del viaje en Merlo (desglose del día)</h2>
        @foreach($cortes as $c)
            @php
                $dz = $c->desglose ?? [];
                $gastos = $c->lines->where('kind', 'gasto');
                $otros = $c->lines->where('kind', 'ingreso');
            @endphp
            <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl p-5 mb-6">
                <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
                    <p class="text-sm text-gray-200">{{ $c->ruta }} · {{ $c->fecha->format('d/m/Y') }}</p>
                    <p class="text-xs text-gray-500">{{ $c->sold_seats + $c->external_seats }} asientos vendidos de {{ $c->total_seats }}</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-xs uppercase text-gray-500 mb-2">Ventas por categoría</h3>
                        @forelse($dz['by_type'] ?? [] as $r)
                            <div class="flex justify-between py-1.5 border-b border-gray-700/30 text-sm"><span class="text-gray-400">{{ $r['seats'] }} {{ $r['label'] }}</span><span class="text-gray-200">{{ $money($r['amount']) }}</span></div>
                        @empty
                            <p class="text-sm text-gray-500">—</p>
                        @endforelse
                        @if($c->external_seats > 0)
                            <div class="flex justify-between py-1.5 border-b border-gray-700/30 text-sm"><span class="text-gray-400">{{ $c->external_seats }} sin apartar en guía</span><span class="text-gray-200">{{ $money($c->external_amount) }}</span></div>
                        @endif
                        <div class="flex justify-between pt-2 text-sm font-semibold"><span class="text-gray-300">Recaudado</span><span class="text-gray-100">{{ $money($c->sold_amount + $c->external_amount) }}</span></div>

                        <h3 class="text-xs uppercase text-gray-500 mt-5 mb-2">Por forma de pago</h3>
                        @foreach($dz['by_method'] ?? [] as $r)
                            <div class="flex justify-between py-1.5 border-b border-gray-700/30 text-sm"><span class="text-gray-400">{{ $r['seats'] }} · {{ $r['label'] }}</span><span class="text-gray-200">{{ $money($r['amount']) }}</span></div>
                        @endforeach
                        @if(($dz['abonos_amount'] ?? 0) > 0)
                            <div class="flex justify-between py-1.5 border-b border-gray-700/30 text-sm"><span class="text-gray-400">Abonos recibidos (apartados)</span><span class="text-gray-200">{{ $money($dz['abonos_amount']) }}</span></div>
                        @endif
                        @if(($dz['por_cobrar_seats'] ?? 0) > 0)
                            <div class="flex justify-between py-1.5 text-sm"><span class="text-amber-400">{{ $dz['por_cobrar_seats'] }} por cobrar</span><span class="text-amber-400">{{ $money($dz['por_cobrar_amount']) }}</span></div>
                        @endif
                    </div>

                    <div>
                        <h3 class="text-xs uppercase text-gray-500 mb-2">Gastos del día</h3>
                        @forelse($gastos as $l)
                            <div class="flex justify-between gap-3 py-1.5 border-b border-gray-700/30 text-sm">
                                <span class="text-gray-300">{{ $l->concepto }}
                                    @if((float) $l->cantidad !== 1.0)<span class="text-xs text-gray-500">({{ rtrim(rtrim(number_format($l->cantidad, 2), '0'), '.') }} × {{ $money($l->monto_unitario) }})</span>@endif
                                    @if($l->nota)<span class="text-xs text-gray-500">· {{ $l->nota }}</span>@endif
                                    @unless($l->cuenta)<span class="text-[10px] text-gray-500 border border-gray-600/50 rounded px-1">informativo</span>@endunless
                                </span>
                                <span class="{{ $l->cuenta ? 'text-red-400' : 'text-gray-500' }}">{{ $money($l->monto) }}</span>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">Sin gastos.</p>
                        @endforelse
                        @if($gastos->isNotEmpty())
                            <div class="flex justify-between pt-2 text-sm font-semibold"><span class="text-gray-300">Total gastos (cuentan como egreso)</span><span class="text-red-400">{{ $money($gastos->where('cuenta', true)->sum('monto')) }}</span></div>
                        @endif

                        @if($otros->isNotEmpty())
                            <h3 class="text-xs uppercase text-gray-500 mt-5 mb-2">Otros ingresos del día</h3>
                            @foreach($otros as $l)
                                <div class="flex justify-between gap-3 py-1.5 border-b border-gray-700/30 text-sm">
                                    <span class="text-gray-300">{{ $l->concepto }}@if($l->nota) <span class="text-xs text-gray-500">· {{ $l->nota }}</span>@endif @unless($l->cuenta)<span class="text-[10px] text-gray-500 border border-gray-600/50 rounded px-1">informativo</span>@endunless</span>
                                    <span class="{{ $l->cuenta ? 'text-emerald-400' : 'text-gray-500' }}">{{ $money($l->monto) }}</span>
                                </div>
                            @endforeach
                        @endif

                        <div class="mt-5 rounded-xl bg-gray-900/50 border border-gray-700/40 px-4 py-3 flex justify-between text-sm">
                            <span class="text-gray-400">Efectivo (capturado en Merlo)</span>
                            <span class="font-semibold text-gray-100">{{ $c->efectivo !== null ? $money($c->efectivo) : '—' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    @if($desglose)
        @php
            $d = $desglose;
            $res = $d['resumen'];
            $row = 'flex justify-between py-1.5 border-b border-gray-700/30 text-sm';
        @endphp
        <h2 class="text-sm font-semibold text-white mb-3">Desglose de la liquidación</h2>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl p-5">
                <h3 class="text-xs uppercase text-gray-500 mb-3">Datos del viaje</h3>
                <div class="{{ $row }}"><span class="text-gray-400">Recorrido</span><span class="text-gray-200">{{ $viaje->recorridos ?: '—' }}</span></div>
                <div class="{{ $row }}"><span class="text-gray-400">Operador{{ $viaje->doble_operador ? 'es' : '' }}</span><span class="text-gray-200">{{ $viaje->operador ? $viaje->operador->name.' '.$viaje->operador->last_name : 'Sin asignar' }}@if($viaje->doble_operador && $viaje->segundoOperador), {{ $viaje->segundoOperador->name }} {{ $viaje->segundoOperador->last_name }}@endif</span></div>
                <div class="{{ $row }}"><span class="text-gray-400">Unidad</span><span class="text-gray-200">{{ $viaje->bus ? $viaje->bus->num_bus.' ('.$viaje->bus->placa.')' : 'Sin asignar' }}</span></div>
                <div class="{{ $row }}"><span class="text-gray-400">Salida / regreso</span><span class="text-gray-200">{{ $viaje->fecha_salida->format('d/m/Y') }} – {{ $viaje->fecha_regreso->format('d/m/Y') }}</span></div>
                <div class="{{ $row }}"><span class="text-gray-400">Km inicial / final</span><span class="text-gray-200">{{ $liq->km_inicial !== null ? number_format($liq->km_inicial, 2) : '—' }} / {{ $liq->km_final !== null ? number_format($liq->km_final, 2) : '—' }}</span></div>
                <div class="{{ $row }}"><span class="text-gray-400">Total km</span><span class="text-gray-200">{{ $res['km_total'] !== null ? number_format($res['km_total'], 2) : '—' }}</span></div>
                <div class="{{ $row }}"><span class="text-gray-400">Liquidación</span><span class="text-gray-200">{{ ucfirst($liq->estado) }}{{ $liq->cerrada_at ? ' · '.$liq->cerrada_at->format('d/m/Y H:i') : '' }}</span></div>
                @if($liq->estado === 'cerrada')
                    <a href="{{ route('liquidacion.pdf', $liq) }}" target="_blank" class="mt-3 inline-block text-xs font-medium text-red-400 hover:text-red-300">Ver hoja de liquidación (PDF) &rarr;</a>
                @endif
            </div>

            <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl p-5">
                <h3 class="text-xs uppercase text-gray-500 mb-3">Diésel</h3>
                <div class="{{ $row }}"><span class="text-gray-400">Diésel de salida</span><span class="text-gray-200">{{ $d['litrosSalida'] > 0 ? number_format($d['litrosSalida'], 2).' L · ' : '' }}{{ $money($d['dieselSalidaMonto']) }}</span></div>
                <div class="{{ $row }}"><span class="text-gray-400">Diésel de regreso (extra)</span><span class="text-gray-200">{{ $d['litrosLlegada'] > 0 ? number_format($d['litrosLlegada'], 2).' L · ' : '' }}{{ $money($d['dieselLlegadaMonto']) }}</span></div>
                <div class="{{ $row }} font-semibold"><span class="text-gray-300">Total diésel</span><span class="text-gray-100">{{ number_format($res['litros_consumidos'], 2) }} L · {{ $money($d['totalDiesel']) }}</span></div>
                <div class="{{ $row }}"><span class="text-gray-400">Costo por litro (promedio)</span><span class="text-gray-200">{{ $d['costoLitro'] !== null ? $money($d['costoLitro']) : '—' }}</span></div>
                <div class="{{ $row }}"><span class="text-gray-400">Rendimiento</span><span class="text-gray-200">{{ $res['rendimiento_km_l'] !== null ? number_format($res['rendimiento_km_l'], 2).' km/L' : '—' }}</span></div>
                <div class="{{ $row }}"><span class="text-gray-400">Consumo</span><span class="text-gray-200">{{ $res['consumo_l_100km'] !== null ? number_format($res['consumo_l_100km'], 2).' L/100km' : '—' }}</span></div>
            </div>

            <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl p-5">
                <h3 class="text-xs uppercase text-gray-500 mb-3">Otros gastos</h3>
                @foreach(\App\Models\LiquidacionGasto::TIPOS as $key => $meta)
                    <div class="{{ $row }}"><span class="text-gray-400">{{ $meta['label'] }}</span><span class="text-gray-200">{{ $money($d['gastosPorTipo'][$key] ?? 0) }}</span></div>
                @endforeach
                <div class="{{ $row }} font-semibold"><span class="text-gray-300">Total otros gastos</span><span class="text-gray-100">{{ $money($d['totalOtrosGastos']) }}</span></div>
            </div>

            <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl p-5">
                <h3 class="text-xs uppercase text-gray-500 mb-3">Resumen financiero</h3>
                <div class="{{ $row }}"><span class="text-gray-400">Costo del viaje</span><span class="text-gray-200">{{ $money($viaje->costo_viaje) }}</span></div>
                <div class="{{ $row }}"><span class="text-gray-400">Comisión operador ({{ $viaje->doble_operador ? '10% × 2 operadores' : '15%' }})</span><span class="text-gray-200">{{ $money($d['comisionOperador']) }}</span></div>
                <div class="{{ $row }}"><span class="text-gray-400">Gastos entregados al operador</span><span class="text-gray-200">{{ $money($viaje->gastos_entregados) }}</span></div>
                <div class="{{ $row }}"><span class="text-gray-400">Gastos totales (diésel + otros)</span><span class="text-gray-200">{{ $money($d['gastosTotales']) }}</span></div>
                <div class="{{ $row }} font-semibold"><span class="text-gray-300">Utilidad total</span><span class="{{ $d['utilidadTotal'] >= 0 ? 'text-emerald-400' : 'text-red-400' }}">{{ $money($d['utilidadTotal']) }}</span></div>
                <div class="{{ $row }}"><span class="text-gray-400">{{ $res['saldo'] >= 0 ? 'El operador liquida (devuelve)' : 'Le debemos al operador' }}</span><span class="text-gray-200">{{ $money(abs($res['saldo'])) }}</span></div>
            </div>
        </div>

        @php $gastosDetalle = $liq->gastos->where('estado', '!=', 'rechazado'); @endphp
        @if($gastosDetalle->isNotEmpty())
            <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl p-5 mb-6">
                <h3 class="text-xs uppercase text-gray-500 mb-3">Detalle de gastos capturados por el operador</h3>
                @foreach($gastosDetalle as $g)
                    <div class="{{ $row }}">
                        <span class="text-gray-300">{{ $g->label() }}{{ $g->concepto ? ' · '.$g->concepto : '' }} <span class="text-xs text-gray-500">({{ $g->estado }})</span></span>
                        <span class="text-gray-200">{{ $money($g->monto) }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    @endif

    @if($esCompras && ! $viaje->cerrado_at)
        @if($bloqueo)
            <p class="rounded-xl border border-amber-500/20 bg-amber-500/10 px-4 py-3 text-sm text-amber-400">{{ $bloqueo }}</p>
        @else
            <form method="POST" action="{{ route('viajes.cierre.store', $viaje) }}"
                  onsubmit="return confirm('¿Cerrar el viaje y enviar {{ $egresos->count() }} egreso(s) y {{ $cortes->count() }} ingreso(s) a Ingresos y Egresos?')">
                @csrf
                <button type="submit" class="px-5 py-2.5 bg-red-600 hover:bg-red-500 text-white text-sm font-medium rounded-xl transition-colors">Cerrar viaje y enviar a Ingresos y Egresos</button>
            </form>
        @endif
    @endif
</x-app-layout>
