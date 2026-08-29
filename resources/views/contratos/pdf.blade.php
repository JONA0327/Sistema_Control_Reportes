<?php
$mesesEs = [
    1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril', 5 => 'mayo', 6 => 'junio',
    7 => 'julio', 8 => 'agosto', 9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre',
];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Contrato {{ $contrato->folio }}</title>
    <style>
        @page { margin: 26px 34px; }
        * { box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10.5px;
            color: #1f2933;
            line-height: 1.5;
        }

        .header-table { width: 100%; margin-bottom: 14px; }
        .header-table td { vertical-align: top; }
        .brand-logo { width: 130px; }
        .header-right { width: 150px; }
        .header-right table { width: 100%; border-collapse: collapse; }
        .header-right td { text-align: center; padding: 0; }
        .autobus-img { width: 130px; }
        .folio-box {
            margin-top: 8px;
            border: 1.5px solid #b91c1c;
            border-radius: 4px;
            padding: 5px 10px;
        }
        .folio-label { font-size: 8px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; }
        .folio-value { font-size: 15px; font-weight: bold; color: #b91c1c; }

        .doc-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2.5px solid #b91c1c;
            padding-bottom: 8px;
            margin: 0 0 10px 0;
        }

        .intro-text { text-align: center; font-size: 9.5px; color: #374151; margin-bottom: 10px; }
        .intro-text strong { color: #1f2933; }

        table.kv-line { width: 100%; border-collapse: collapse; margin-bottom: 3px; }
        table.kv-line td { padding: 2px 0; font-size: 10.5px; }
        table.kv-line td.k { color: #6b7280; font-size: 8.5px; text-transform: uppercase; white-space: nowrap; padding-right: 6px; }
        table.kv-line td.v { font-weight: bold; border-bottom: 0.75px solid #9ca3af; padding-left: 4px; }

        h2.section-title {
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #ffffff;
            background-color: #1f2933;
            padding: 4px 8px;
            margin: 10px 0 6px 0;
        }

        table.data-grid { width: 100%; border-collapse: collapse; margin-bottom: 4px; table-layout: fixed; }
        table.data-grid td { padding: 3px 8px 3px 0; vertical-align: top; }
        table.data-grid td.label { color: #6b7280; font-size: 8.5px; text-transform: uppercase; display: block; }
        table.data-grid td.value { color: #1f2933; font-weight: bold; font-size: 10.5px; }

        .box {
            border: 0.75px solid #d1d5db;
            border-radius: 4px;
            padding: 8px 10px;
            margin-bottom: 8px;
            background-color: #f9fafb;
        }
        .box .label { color: #6b7280; font-size: 8.5px; text-transform: uppercase; display: block; margin-bottom: 4px; }
        .highlight-note {
            display: block;
            margin-top: 6px;
            padding: 4px 8px;
            background-color: #fef08a;
            border: 0.75px solid #ca8a04;
            border-radius: 3px;
            font-weight: bold;
            font-size: 9.5px;
        }

        .costos-table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        .costos-table td {
            border: 1px solid #1f2933;
            padding: 8px 10px;
            width: 33.33%;
            text-align: center;
        }
        .costos-table .label { display: block; font-size: 8.5px; color: #6b7280; text-transform: uppercase; margin-bottom: 3px; }
        .costos-table .value { font-size: 13px; font-weight: bold; }

        .clausulas { margin: 8px 0; padding-left: 16px; font-size: 9px; color: #374151; }
        .clausulas li { margin-bottom: 4px; }
        .clausulas .clausula-highlight { background-color: #fef08a; font-weight: bold; padding: 1px 3px; }

        .firma-fecha { margin: 14px 0 4px 0; font-size: 10px; text-align: center; }

        .signatures-table { width: 100%; margin-top: 20px; border-collapse: collapse; }
        .signatures-table td { width: 50%; text-align: center; vertical-align: top; padding: 0 20px; }
        .sign-line { border-top: 1px solid #1f2933; margin-top: 30px; padding-top: 4px; }
        .sign-name { font-weight: bold; font-size: 10.5px; }
        .sign-role { color: #6b7280; font-size: 9px; margin-top: 1px; }

        .footer-note {
            margin-top: 16px;
            font-size: 8px;
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
            <td style="width: 140px;">
                <img src="data:image/png;base64,{{ $logoBase64 }}" class="brand-logo">
            </td>
            <td></td>
            <td class="header-right">
                <table>
                    <tr><td><img src="data:image/png;base64,{{ $autobusBase64 }}" class="autobus-img"></td></tr>
                    <tr>
                        <td>
                            <div class="folio-box">
                                <div class="folio-label">No. Folio</div>
                                <div class="folio-value">{{ $contrato->folio }}</div>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="doc-title">Contrato de arrendamiento</div>

    <div class="intro-text">
        Por una parte, como arrendador <strong>Turística Merlo S.A. de C.V.</strong> con domicilio en Av. 20 de Noviembre 1560, Tlaxcala, San Luis Potosí, S.L.P. CP. 78038,
        y por otra parte como arrendatario:
    </div>

    <table class="kv-line">
        <tr>
            <td class="k" style="width: 90px;">El Sr(a).</td>
            <td class="v">{{ $contrato->cliente_nombre }}</td>
            <td class="k" style="width: 60px; text-align: right; padding-right: 6px;">Con tel.</td>
            <td class="v" style="width: 130px;">{{ $contrato->cliente_telefono ?: '—' }}</td>
        </tr>
        <tr>
            <td class="k">Domicilio</td>
            <td class="v">{{ $contrato->cliente_domicilio ?: '—' }}</td>
            <td class="k" style="text-align: right; padding-right: 6px;">Ciudad</td>
            <td class="v">{{ $contrato->cliente_ciudad ?: '—' }}</td>
        </tr>
    </table>

    <table class="data-grid" style="margin-top: 8px;">
        <tr>
            <td style="width: 25%;">
                <span class="label">Renta autobús</span>
                <span class="value">Sí</span>
            </td>
            <td style="width: 25%;">
                <span class="label">N° plazas</span>
                <span class="value">{{ $contrato->num_plazas ?: '—' }}</span>
            </td>
            <td style="width: 50%;">
                <span class="label">Unidad asignada</span>
                <span class="value">{{ $contrato->bus ? 'Bus #'.$contrato->bus->num_bus.' ('.$contrato->bus->placa.')' : 'Por asignar' }}</span>
            </td>
        </tr>
    </table>

    {{-- Datos del viaje --}}
    <h2 class="section-title">Datos del viaje</h2>
    <table class="data-grid">
        <tr>
            <td style="width: 25%;">
                <span class="label">Fecha de salida</span>
                <span class="value">{{ $contrato->fecha_salida->format('d/m/Y') }}</span>
            </td>
            <td style="width: 25%;">
                <span class="label">Hora de salida</span>
                <span class="value">{{ $contrato->hora_salida ?: '—' }} hrs</span>
            </td>
            <td style="width: 25%;">
                <span class="label">Fecha de regreso</span>
                <span class="value">{{ $contrato->fecha_regreso->format('d/m/Y') }}</span>
            </td>
            <td style="width: 25%;">
                <span class="label">Hora de regreso</span>
                <span class="value">{{ $contrato->hora_regreso ?: '—' }} hrs</span>
            </td>
        </tr>
    </table>
    <table class="data-grid">
        <tr>
            <td style="width: 50%;">
                <span class="label">Salida y destino</span>
                <span class="value">{{ $contrato->salida_destino }}</span>
            </td>
            <td style="width: 50%;">
                <span class="label">Punto de partida y llegada</span>
                <span class="value">{{ $contrato->punto_partida_llegada ?: '—' }}</span>
            </td>
        </tr>
    </table>

    <div class="box">
        <span class="label">Itinerario</span>
        {{ $contrato->itinerario ?: 'Sin itinerario adicional especificado.' }}
        @unless($contrato->incluye_estacionamiento)
            <span class="highlight-note">Este costo no incluye estacionamientos.</span>
        @endunless
    </div>

    <div style="font-size: 8.5px; color: #6b7280; margin-bottom: 6px;">
        Nota: solo se visitarán los puntos aquí señalados. Cualquier punto adicional requerido puede generar un costo extra,
        el cual deberá cubrirse en ese mismo momento con el operador de la unidad.
    </div>

    {{-- Costos --}}
    <table class="costos-table">
        <tr>
            <td>
                <span class="label">Costo del viaje</span>
                <span class="value">${{ number_format($contrato->costo_viaje, 2) }}</span>
            </td>
            <td>
                <span class="label">Anticipo</span>
                <span class="value">${{ number_format($contrato->anticipo, 2) }}</span>
            </td>
            <td>
                <span class="label">Resto</span>
                <span class="value">${{ number_format($contrato->resto, 2) }}</span>
            </td>
        </tr>
    </table>

    {{-- Cláusulas --}}
    <h2 class="section-title">Cláusulas</h2>
    <ol class="clausulas">
        <li>El presente contrato es en base a la fecha, hora, destino e itinerario proporcionado por el cliente.</li>
        <li>En caso de incumplimiento del contrato por cualquiera de las partes, el anticipo quedará como indemnización a favor de Turística Merlo S.A. de C.V. o del arrendatario según corresponda, así como no liquidar la totalidad al tiempo indicado en este contrato y se dará por cancelado el viaje.</li>
        <li @if(! $contrato->incluye_estacionamiento) class="clausula-highlight" @endif>
            El costo del viaje {{ $contrato->incluye_estacionamiento ? 'incluye' : 'no incluye' }} estacionamientos.
        </li>
        <li>En caso de descompostura de la unidad, la empresa se compromete a sustituir la unidad, sin garantizar tener las mismas características y condiciones que la unidad original.</li>
        <li>La empresa no se hace responsable de objetos olvidados o extraviados en la unidad.</li>
        <li>Queda estrictamente prohibido fumar y tomar bebidas alcohólicas dentro de la unidad y en caso de no cumplir con esta restricción se podrá dar por cancelado el servicio y no se hará devolución alguna del costo del viaje.</li>
        <li>El contratante se responsabiliza por los daños de la unidad ocasionados por los pasajeros.</li>
        <li>Debido a las dimensiones de la unidad y por su seguridad, no accesan a zonas de terracería y de difícil acceso (queda a criterio del operador).</li>
        <li>Se podrá transportar mascotas solo en jaulas de viaje y el único lugar permitido serán las cajuelas de la unidad (previo aviso).</li>
        <li>Se requiere el 20% de anticipo para reservar la unidad, el 80% del monto total deberá estar cubierto un mes antes y el 100% 5 días antes de la fecha de salida.</li>
        <li>La póliza de seguro cubre gastos médicos descritos en la misma, la empresa queda exenta de algún cargo que se le impute.</li>
        @if($contrato->notas)
            <li>{{ $contrato->notas }}</li>
        @endif
    </ol>

    <div class="firma-fecha">
        {{ $contrato->lugar_firma }} a {{ $contrato->fecha_firma->format('d') }} de {{ $mesesEs[(int) $contrato->fecha_firma->format('n')] }} del {{ $contrato->fecha_firma->format('Y') }}
    </div>

    <div style="text-align: center; font-size: 10px; font-weight: bold; text-transform: uppercase; margin-top: 4px;">
        Se acepta de conformidad
    </div>

    <table class="signatures-table">
        <tr>
            <td>
                <div class="sign-line">
                    <div class="sign-name">Turística Merlo S.A. de C.V.</div>
                    <div class="sign-role">El arrendador</div>
                </div>
            </td>
            <td>
                <div class="sign-line">
                    <div class="sign-name">{{ $contrato->cliente_nombre }}</div>
                    <div class="sign-role">El arrendatario</div>
                </div>
            </td>
        </tr>
    </table>

    <div class="footer-note">
        Documento generado por el sistema de Control de Reportes de Merlo Transportes · Folio {{ $contrato->folio }} · Emitido {{ now()->format('d/m/Y H:i') }}
    </div>

</body>
</html>
