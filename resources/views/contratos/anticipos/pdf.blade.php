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
    <title>Comprobante de anticipo {{ $anticipo->folio }}</title>
    <style>
        @page { margin: 24px 30px; }
        * { box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #1f2933;
            line-height: 1.5;
        }

        .header-table { width: 100%; margin-bottom: 12px; }
        .header-table td { vertical-align: top; }
        .brand-logo { width: 110px; }
        .header-right { width: 200px; }
        .header-right table { width: 100%; border-collapse: collapse; }
        .header-right td { text-align: right; padding: 0; }

        .folio-box {
            border: 1.5px solid #b91c1c;
            border-radius: 4px;
            padding: 6px 12px;
            display: inline-block;
        }
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

        .doc-subtitle {
            text-align: center;
            font-size: 9px;
            color: #6b7280;
            margin-bottom: 12px;
        }

        .cliente-box {
            background-color: #fef9e7;
            border-left: 3px solid #f5b301;
            padding: 8px 12px;
            margin-bottom: 10px;
            border-radius: 3px;
        }
        .cliente-box .label { font-size: 8px; color: #6b7280; text-transform: uppercase; display: block; margin-bottom: 2px; }
        .cliente-box .value { font-size: 12px; font-weight: bold; color: #1f2933; }
        .cliente-box .sub { font-size: 9px; color: #4b5563; }

        table.data-grid { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        table.data-grid td { padding: 4px 8px 4px 0; vertical-align: top; }
        table.data-grid td.label { color: #6b7280; font-size: 8.5px; text-transform: uppercase; display: block; margin-bottom: 1px; }
        table.data-grid td.value { color: #1f2933; font-weight: bold; font-size: 11px; }

        .monto-destacado {
            margin: 10px 0;
            padding: 14px;
            background: linear-gradient(135deg, #fef9e7 0%, #fef08a 100%);
            border: 2px solid #b91c1c;
            border-radius: 6px;
            text-align: center;
        }
        .monto-destacado .label { font-size: 9px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 4px; }
        .monto-destacado .monto { font-size: 28px; font-weight: bold; color: #b91c1c; }
        .monto-destacado .en-letras { font-size: 9px; color: #4b5563; margin-top: 2px; font-style: italic; }

        h2.section-title {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #ffffff;
            background-color: #1f2933;
            padding: 4px 8px;
            margin: 10px 0 6px 0;
        }

        .nota-box {
            background-color: #f9fafb;
            border: 0.5px solid #d1d5db;
            border-radius: 3px;
            padding: 6px 10px;
            font-size: 9px;
            color: #4b5563;
            margin-bottom: 8px;
        }

        .evidencia-box {
            background-color: #eff6ff;
            border: 0.5px solid #93c5fd;
            border-radius: 3px;
            padding: 6px 10px;
            font-size: 9px;
            color: #1e40af;
            margin-bottom: 8px;
        }

        .signatures-table { width: 100%; margin-top: 18px; border-collapse: collapse; }
        .signatures-table td { width: 50%; text-align: center; vertical-align: top; padding: 0 16px; }
        .sign-line { border-top: 1px solid #1f2933; margin-top: 26px; padding-top: 4px; }
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
                    <div class="folio-label">Comprobante</div>
                    <div class="folio-value">{{ $anticipo->folio }}</div>
                </div>
            </td>
        </tr>
    </table>

    <div class="doc-title">Comprobante de anticipo</div>
    <div class="doc-subtitle">
        Turística Merlo S.A. de C.V. · Av. 20 de Noviembre 1560, Tlaxcala, San Luis Potosí, S.L.P.
    </div>

    {{-- Datos del cliente --}}
    <div class="cliente-box">
        <span class="label">Recibimos de</span>
        <div class="value">{{ $contrato->cliente_nombre }}</div>
        <div class="sub">
            @if($contrato->cliente_telefono) Tel: {{ $contrato->cliente_telefono }} · @endif
            @if($contrato->cliente_ciudad) {{ $contrato->cliente_ciudad }} @endif
        </div>
    </div>

    {{-- Datos del contrato --}}
    <h2 class="section-title">Datos del contrato</h2>
    <table class="data-grid">
        <tr>
            <td style="width: 25%;">
                <span class="label">Folio contrato</span>
                <span class="value">{{ $contrato->folio }}</span>
            </td>
            <td style="width: 25%;">
                <span class="label">Fecha del contrato</span>
                <span class="value">{{ $contrato->fecha_firma->format('d/m/Y') }}</span>
            </td>
            <td style="width: 25%;">
                <span class="label">Ruta</span>
                <span class="value">{{ $contrato->salida_destino }}</span>
            </td>
            <td style="width: 25%;">
                <span class="label">Fecha de salida</span>
                <span class="value">{{ $contrato->fecha_salida->format('d/m/Y') }}</span>
            </td>
        </tr>
    </table>

    {{-- Monto del anticipo (destacado) --}}
    <div class="monto-destacado">
        <span class="label">Monto del anticipo</span>
        <div class="monto">${{ number_format($anticipo->monto, 2) }}</div>
        <div class="en-letras">
            ({{ \App\Support\NumberToWords::convert((float) $anticipo->monto) }})
        </div>
    </div>

    {{-- Detalle del anticipo --}}
    <h2 class="section-title">Detalle del anticipo</h2>
    <table class="data-grid">
        <tr>
            <td style="width: 33%;">
                <span class="label">Fecha del anticipo</span>
                <span class="value">{{ $anticipo->fecha_anticipo->format('d/m/Y') }}</span>
            </td>
            <td style="width: 33%;">
                <span class="label">Método de pago</span>
                <span class="value">{{ $anticipo->metodo_pago ?: '—' }}</span>
            </td>
            <td style="width: 33%;">
                <span class="label">Registrado por</span>
                <span class="value">{{ $anticipo->user->name ?? '—' }}</span>
            </td>
        </tr>
    </table>

    @if($anticipo->notas)
        <div class="nota-box">
            <strong>Notas:</strong> {{ $anticipo->notas }}
        </div>
    @endif

    @if($anticipo->tieneEvidencia())
        <div class="evidencia-box">
            <strong>Evidencia adjunta:</strong> {{ $anticipo->evidencia_nombre_original }}
            (almacenada en el sistema para respaldo interno)
        </div>
    @endif

    {{-- Resumen de pagos --}}
    <h2 class="section-title">Resumen de pagos del contrato</h2>
    <table class="data-grid">
        <tr>
            <td style="width: 25%;">
                <span class="label">Costo total del viaje</span>
                <span class="value">${{ number_format($contrato->costo_viaje, 2) }}</span>
            </td>
            <td style="width: 25%;">
                <span class="label">Anticipo total</span>
                <span class="value" style="color: #b91c1c;">${{ number_format($contrato->anticipo_efectivo, 2) }}</span>
            </td>
            <td style="width: 25%;">
                <span class="label">Total pagado</span>
                <span class="value" style="color: #15803d;">${{ number_format($contrato->total_pagado, 2) }}</span>
            </td>
            <td style="width: 25%;">
                <span class="label">Saldo pendiente</span>
                <span class="value" style="color: {{ $contrato->esta_liquidado ? '#1f2933' : '#b91c1c' }};">${{ number_format($contrato->saldo_pendiente, 2) }}</span>
            </td>
        </tr>
    </table>

    {{-- Firmas --}}
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
        Comprobante generado el {{ now()->format('d/m/Y H:i') }} por el sistema de Control de Reportes de Merlo Transportes ·
        Folio {{ $anticipo->folio }} · Contrato {{ $contrato->folio }}
    </div>

</body>
</html>
