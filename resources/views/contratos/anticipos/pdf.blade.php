<?php
$montoRedondeado = round((float) $anticipo->monto);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Comprobante de anticipo {{ $anticipo->folio }}</title>
    <style>
        @page { margin: 14px 18px; }
        * { box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8.5px;
            color: #1f2933;
            line-height: 1.3;
        }

        .header-table { width: 100%; margin-bottom: 4px; }
        .header-table td { vertical-align: middle; }
        .brand-logo { width: 62px; }
        .autobus-img { width: 42px; }
        .header-right { text-align: right; }

        .doc-title {
            text-align: center;
            font-size: 11.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 2px 0 0 0;
        }
        .folio-value {
            text-align: center;
            font-size: 10.5px;
            font-weight: bold;
            color: #b91c1c;
            font-family: 'DejaVu Sans Mono', monospace;
        }

        hr.rule { border: none; border-top: 2px solid #b91c1c; margin: 4px 0 6px 0; }

        .cliente-box {
            background-color: #fef9e7;
            border-left: 3px solid #f5b301;
            padding: 5px 8px;
            margin-bottom: 6px;
            border-radius: 3px;
        }
        .cliente-box .label { font-size: 7px; color: #6b7280; text-transform: uppercase; display: block; }
        .cliente-box .value { font-size: 9.5px; font-weight: bold; color: #1f2933; }
        .cliente-box .sub { font-size: 7.5px; color: #4b5563; }

        .monto-destacado {
            padding: 8px;
            margin-bottom: 6px;
            background: linear-gradient(135deg, #fef9e7 0%, #fef08a 100%);
            border: 1.5px solid #b91c1c;
            border-radius: 5px;
            text-align: center;
        }
        .monto-destacado .label { font-size: 7px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 2px; }
        .monto-destacado .monto { font-size: 22px; font-weight: bold; color: #b91c1c; }
        .monto-destacado .en-letras { font-size: 7px; color: #4b5563; margin-top: 1px; font-style: italic; }

        table.kv { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        table.kv td { padding: 2px 0; font-size: 8.5px; border-bottom: 0.5px solid #e5e7eb; }
        table.kv td.k { color: #6b7280; width: 45%; }
        table.kv td.v { font-weight: bold; color: #1f2933; text-align: right; }

        table.restante-box {
            width: 100%;
            margin-bottom: 6px;
            border-collapse: collapse;
            background-color: #fef2f2;
            border: 1px solid #fca5a5;
            border-radius: 4px;
        }
        .restante-box td { padding: 5px 8px; vertical-align: middle; }
        .restante-box .label { font-size: 7.5px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.3px; }
        .restante-box .valor { font-size: 12px; font-weight: bold; color: #b91c1c; text-align: right; }
        .restante-box .valor.ok { color: #15803d; }

        .nota-box {
            background-color: #f9fafb;
            border: 0.5px solid #d1d5db;
            border-radius: 3px;
            padding: 4px 7px;
            font-size: 7.5px;
            color: #4b5563;
            margin-bottom: 6px;
        }

        .signatures-table { width: 100%; margin-top: 16px; border-collapse: collapse; }
        .signatures-table td { width: 50%; text-align: center; vertical-align: top; padding: 0 6px; }
        .sign-line { border-top: 1px solid #1f2933; margin-top: 18px; padding-top: 3px; }
        .sign-name { font-weight: bold; font-size: 8px; }
        .sign-role { color: #6b7280; font-size: 6.5px; margin-top: 1px; }

        .footer-note {
            margin-top: 8px;
            font-size: 5.5px;
            color: #9ca3af;
            text-align: center;
            border-top: 0.5px solid #e5e7eb;
            padding-top: 3px;
        }
    </style>
</head>
<body>

    {{-- Encabezado --}}
    <table class="header-table">
        <tr>
            <td style="width: 62px;">
                <img src="data:image/png;base64,{{ $logoBase64 }}" class="brand-logo">
            </td>
            <td></td>
            <td class="header-right" style="width: 42px;">
                <img src="data:image/png;base64,{{ $autobusBase64 }}" class="autobus-img">
            </td>
        </tr>
    </table>
    <div class="doc-title">Comprobante de anticipo</div>
    <div class="folio-value">{{ $anticipo->folio }}</div>
    <hr class="rule">

    {{-- Cliente --}}
    <div class="cliente-box">
        <span class="label">Recibimos de</span>
        <div class="value">{{ $contrato->cliente_nombre }}</div>
        @if($contrato->cliente_telefono || $contrato->cliente_ciudad)
            <div class="sub">
                @if($contrato->cliente_telefono) Tel: {{ $contrato->cliente_telefono }} @endif
                @if($contrato->cliente_telefono && $contrato->cliente_ciudad) · @endif
                @if($contrato->cliente_ciudad) {{ $contrato->cliente_ciudad }} @endif
            </div>
        @endif
    </div>

    {{-- Monto destacado --}}
    <div class="monto-destacado">
        <span class="label">Monto del anticipo</span>
        <div class="monto">${{ number_format($montoRedondeado, 0) }}</div>
        <div class="en-letras">
            ({{ \App\Support\NumberToWords::convert($montoRedondeado) }})
        </div>
    </div>

    {{-- Datos mínimos --}}
    <table class="kv">
        <tr>
            <td class="k">Contrato</td>
            <td class="v">{{ $contrato->folio }}</td>
        </tr>
        <tr>
            <td class="k">Fecha del anticipo</td>
            <td class="v">{{ $anticipo->fecha_anticipo->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="k">Método de pago</td>
            <td class="v">{{ $anticipo->metodo_pago ?: '—' }}</td>
        </tr>
        <tr>
            <td class="k">Registrado por</td>
            <td class="v">{{ $anticipo->user->name ?? '—' }}</td>
        </tr>
    </table>

    {{-- Saldo restante --}}
    <table class="restante-box">
        <tr>
            <td class="label">{{ $contrato->esta_liquidado ? 'Contrato liquidado' : 'Saldo restante' }}</td>
            <td class="valor {{ $contrato->esta_liquidado ? 'ok' : '' }}">
                ${{ number_format($contrato->saldo_pendiente, 0) }}
            </td>
        </tr>
    </table>

    @if($anticipo->notas)
        <div class="nota-box">
            <strong>Notas:</strong> {{ $anticipo->notas }}
        </div>
    @endif

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
        Generado el {{ now()->format('d/m/Y H:i') }} · Merlo Transportes · Folio {{ $anticipo->folio }} · Contrato {{ $contrato->folio }}
    </div>

</body>
</html>
