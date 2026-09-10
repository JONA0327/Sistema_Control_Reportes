<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Liquidación {{ $viaje->no_contrato }}</title>
    <style>
        @page { margin: 24px 30px; }
        * { box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #1f2933;
            line-height: 1.5;
        }

        .header-table { width: 100%; margin-bottom: 8px; }
        .header-table td { vertical-align: top; }
        .brand-logo { width: 110px; }
        .header-right { width: 200px; text-align: right; }

        .folio-box { border: 1.5px solid #b91c1c; border-radius: 4px; padding: 6px 12px; display: inline-block; }
        .folio-label { font-size: 8px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; }
        .folio-value { font-size: 14px; font-weight: bold; color: #b91c1c; font-family: 'DejaVu Sans Mono', monospace; }

        .doc-title {
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2.5px solid #b91c1c;
            padding-bottom: 6px;
            margin: 0 0 10px 0;
        }
        .doc-subtitle { text-align: center; font-size: 9px; color: #6b7280; margin-bottom: 12px; }

        h2.section-title {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #ffffff;
            background-color: #1f2933;
            padding: 4px 8px;
            margin: 10px 0 6px 0;
        }

        table.data-grid { width: 100%; border-collapse: collapse; margin-bottom: 4px; table-layout: fixed; }
        table.data-grid td { padding: 3px 8px 5px 0; vertical-align: top; }
        table.data-grid .label { color: #6b7280; font-size: 9px; }
        table.data-grid .value { color: #000000; font-weight: bold; font-size: 10.5px; }

        table.money-table { width: 100%; border-collapse: collapse; }
        table.money-table td, table.money-table th { padding: 4px 6px; border-bottom: 0.75px solid #e5e7eb; }
        table.money-table th { text-align: left; font-size: 8px; text-transform: uppercase; color: #6b7280; }
        table.money-table td.concepto { color: #4b5563; }
        table.money-table td.monto { text-align: right; font-weight: bold; font-family: 'DejaVu Sans Mono', monospace; }
        table.money-table tr.total td { border-top: 1.5px solid #1f2933; border-bottom: none; font-weight: bold; padding-top: 6px; }

        .two-col { width: 100%; border-collapse: collapse; }
        .two-col > tbody > tr > td { vertical-align: top; width: 50%; padding: 0; }
        .two-col > tbody > tr > td:first-child { padding-right: 10px; }
        .two-col > tbody > tr > td:last-child { padding-left: 10px; }

        .resultado-box {
            margin-top: 10px;
            padding: 12px 14px;
            border-radius: 6px;
            border: 2px solid;
        }
        .resultado-box.positivo { background: #f0fdf4; border-color: #15803d; }
        .resultado-box.negativo { background: #fef2f2; border-color: #b91c1c; }
        .resultado-box .label { font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; color: #4b5563; display: block; margin-bottom: 2px; }
        .resultado-box .monto { font-size: 20px; font-weight: bold; }
        .resultado-box.positivo .monto { color: #15803d; }
        .resultado-box.negativo .monto { color: #b91c1c; }

        .nota-box {
            background-color: #f9fafb;
            border: 0.5px solid #d1d5db;
            border-radius: 3px;
            padding: 6px 10px;
            font-size: 8.5px;
            color: #4b5563;
            margin-top: 8px;
        }

        .signatures-table { width: 100%; margin-top: 22px; border-collapse: collapse; }
        .signatures-table td { width: 50%; text-align: center; vertical-align: top; padding: 0 16px; }
        .sign-line { border-top: 1px solid #1f2933; margin-top: 30px; padding-top: 4px; }
        .sign-name { font-weight: bold; font-size: 10px; }
        .sign-role { color: #6b7280; font-size: 8.5px; margin-top: 1px; }

        .footer-note {
            margin-top: 14px;
            font-size: 7.5px;
            color: #9ca3af;
            text-align: center;
            border-top: 0.5px solid #e5e7eb;
            padding-top: 4px;
        }
    </style>
</head>
<body>

    {{-- Encabezado --}}
    <table class="header-table">
        <tr>
            <td style="width: 120px;">
                <img src="data:image/png;base64,{{ $logoBase64 }}" class="brand-logo">
            </td>
            <td></td>
            <td class="header-right">
                <div class="folio-box">
                    <div class="folio-label">Viaje</div>
                    <div class="folio-value">{{ $viaje->no_contrato }}</div>
                </div>
            </td>
        </tr>
    </table>

    <div class="doc-title">Hoja de liquidación</div>
    <div class="doc-subtitle">Turística Merlo S.A. de C.V.</div>

    {{-- Datos del viaje --}}
    <h2 class="section-title">Datos del viaje</h2>
    <table class="two-col">
        <tr>
            <td>
                <table class="data-grid">
                    <tr>
                        <td><span class="label">No. Contrato:</span> <span class="value">{{ $viaje->no_contrato }}</span></td>
                    </tr>
                    <tr>
                        <td><span class="label">Origen:</span> <span class="value">{{ $viaje->origen }}</span></td>
                    </tr>
                    <tr>
                        <td><span class="label">Destino:</span> <span class="value">{{ $viaje->destino }}</span></td>
                    </tr>
                    <tr>
                        <td><span class="label">Recorridos:</span> <span class="value" style="font-size:9.5px;">{{ $viaje->recorridos ?: '—' }}</span></td>
                    </tr>
                    <tr>
                        <td>
                            <span class="label">Operador{{ $viaje->doble_operador ? 'es' : '' }}:</span>
                            <span class="value">
                                {{ $viaje->operador->name }} {{ $viaje->operador->last_name }}
                                @if($viaje->doble_operador && $viaje->segundoOperador)
                                    <br>{{ $viaje->segundoOperador->name }} {{ $viaje->segundoOperador->last_name }}
                                @endif
                            </span>
                        </td>
                    </tr>
                </table>
            </td>
            <td>
                <table class="data-grid">
                    <tr>
                        <td><span class="label">No. Unidad:</span> <span class="value">{{ $viaje->bus->num_bus }} ({{ $viaje->bus->placa }})</span></td>
                    </tr>
                    <tr>
                        <td><span class="label">Fecha salida:</span> <span class="value">{{ $viaje->fecha_salida->format('d/m/Y') }}</span></td>
                    </tr>
                    <tr>
                        <td><span class="label">Fecha regreso:</span> <span class="value">{{ $viaje->fecha_regreso->format('d/m/Y') }}</span></td>
                    </tr>
                    <tr>
                        <td><span class="label">Km inicial / final:</span> <span class="value">{{ $liquidacion->km_inicial !== null ? number_format($liquidacion->km_inicial, 2) : '—' }} / {{ $liquidacion->km_final !== null ? number_format($liquidacion->km_final, 2) : '—' }}</span></td>
                    </tr>
                    <tr>
                        <td><span class="label">Total km:</span> <span class="value">{{ $resumen['km_total'] !== null ? number_format($resumen['km_total'], 2) : '—' }}</span></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Diésel --}}
    <h2 class="section-title">Diésel</h2>
    <table class="two-col">
        <tr>
            <td>
                <table class="money-table">
                    <tr><th>Concepto</th><th style="text-align:right;">Litros</th><th style="text-align:right;">Monto</th></tr>
                    <tr>
                        <td class="concepto">Diésel de salida</td>
                        <td class="monto">{{ $litrosSalida > 0 ? number_format($litrosSalida, 2).' L' : '—' }}</td>
                        <td class="monto">${{ number_format($dieselSalidaMonto, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="concepto">Diésel de regreso (extra)</td>
                        <td class="monto">{{ $litrosLlegada > 0 ? number_format($litrosLlegada, 2).' L' : '—' }}</td>
                        <td class="monto">${{ number_format($dieselLlegadaMonto, 2) }}</td>
                    </tr>
                    <tr class="total">
                        <td class="concepto">Total diésel</td>
                        <td class="monto">{{ number_format($resumen['litros_consumidos'], 2) }} L</td>
                        <td class="monto">${{ number_format($totalDiesel, 2) }}</td>
                    </tr>
                </table>
            </td>
            <td>
                <table class="data-grid">
                    <tr>
                        <td><span class="label">Costo por litro (promedio):</span> <span class="value">{{ $costoLitro !== null ? '$'.number_format($costoLitro, 2) : '—' }}</span></td>
                    </tr>
                    <tr>
                        <td><span class="label">Rendimiento:</span> <span class="value">{{ $resumen['rendimiento_km_l'] !== null ? number_format($resumen['rendimiento_km_l'], 2).' km/L' : '—' }}</span></td>
                    </tr>
                    <tr>
                        <td><span class="label">Consumo:</span> <span class="value">{{ $resumen['consumo_l_100km'] !== null ? number_format($resumen['consumo_l_100km'], 2).' L/100km' : '—' }}</span></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Otros gastos --}}
    <h2 class="section-title">Otros gastos</h2>
    <table class="money-table">
        <tr><th>Concepto</th><th style="text-align:right;">Monto</th></tr>
        @foreach(\App\Models\LiquidacionGasto::TIPOS as $key => $meta)
            <tr>
                <td class="concepto">{{ $meta['label'] }}</td>
                <td class="monto">${{ number_format($gastosPorTipo[$key] ?? 0, 2) }}</td>
            </tr>
        @endforeach
        <tr class="total">
            <td class="concepto">Total otros gastos</td>
            <td class="monto">${{ number_format($totalOtrosGastos, 2) }}</td>
        </tr>
    </table>

    {{-- Resumen financiero --}}
    <h2 class="section-title">Resumen financiero</h2>
    <table class="money-table">
        <tr><th>Concepto</th><th style="text-align:right;">Monto</th></tr>
        <tr>
            <td class="concepto">Costo del viaje</td>
            <td class="monto">${{ number_format($viaje->costo_viaje, 2) }}</td>
        </tr>
        <tr>
            <td class="concepto">
                Comisión operador
                <span style="color:#9ca3af; font-weight: normal;">
                    ({{ $viaje->doble_operador ? '10% × 2 operadores' : '15%' }})
                </span>
            </td>
            <td class="monto">${{ number_format($comisionOperador, 2) }}</td>
        </tr>
        <tr>
            <td class="concepto">Gastos entregados al operador</td>
            <td class="monto">${{ number_format($viaje->gastos_entregados, 2) }}</td>
        </tr>
        <tr>
            <td class="concepto">Gastos totales (diésel + otros)</td>
            <td class="monto">${{ number_format($gastosTotales, 2) }}</td>
        </tr>
        <tr class="total">
            <td class="concepto">Utilidad total</td>
            <td class="monto">${{ number_format($utilidadTotal, 2) }}</td>
        </tr>
    </table>

    {{-- Liquida o debemos --}}
    @php
        $saldo = $resumen['saldo'];
    @endphp
    <div class="resultado-box {{ $saldo >= 0 ? 'positivo' : 'negativo' }}">
        <span class="label">{{ $saldo >= 0 ? 'El operador liquida (devuelve)' : 'Le debemos al operador' }}</span>
        <div class="monto">${{ number_format(abs($saldo), 2) }}</div>
    </div>

    <div class="nota-box">
        "Gastos entregados al operador" menos lo efectivamente gastado (diésel financiado con gastos entregados + otros gastos aceptados) determina si el operador debe devolver saldo o si la empresa le debe. La "Utilidad total" es el costo del viaje menos la comisión del operador y el total de gastos del viaje (diésel + otros), sin importar la fuente de pago.
    </div>

    {{-- Firmas --}}
    <table class="signatures-table">
        <tr>
            <td>
                <div class="sign-line">
                    <div class="sign-name">{{ $viaje->operador->name }} {{ $viaje->operador->last_name }}</div>
                    <div class="sign-role">Operador{{ $viaje->doble_operador ? ' titular' : '' }}</div>
                </div>
            </td>
            <td>
                <div class="sign-line">
                    <div class="sign-name">{{ $liquidacion->cerradaPor->name ?? '—' }} {{ $liquidacion->cerradaPor->last_name ?? '' }}</div>
                    <div class="sign-role">Administración</div>
                </div>
            </td>
        </tr>
    </table>

    <div class="footer-note">
        Hoja de liquidación generada el {{ now()->format('d/m/Y H:i') }} por el sistema de Control de Reportes de Merlo Transportes ·
        Liquidación cerrada el {{ $liquidacion->cerrada_at?->format('d/m/Y H:i') ?? '—' }}
    </div>

</body>
</html>
