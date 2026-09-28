<?php
$montoRedondeado = round((float) $anticipo->monto);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Comprobante de anticipo {{ $anticipo->folio }}</title>
    <style>
        @page { margin: 12px 18px; }
        * { box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8.5px;
            color: #1f2933;
            line-height: 1.25;
        }

        .header-table { width: 100%; margin-bottom: 4px; }
        .header-table td { vertical-align: middle; }
        .brand-logo { width: 68px; }
        .autobus-img { width: 46px; }
        .header-center { text-align: center; }
        .header-right { text-align: right; }

        .doc-title {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .folio-value {
            font-size: 11px;
            font-weight: bold;
            color: #b91c1c;
            font-family: 'DejaVu Sans Mono', monospace;
            margin-top: 1px;
        }

        hr.rule { border: none; border-top: 2px solid #b91c1c; margin: 4px 0 6px 0; }

        table.main-grid { width: 100%; border-collapse: collapse; }
        table.main-grid > tr > td { vertical-align: top; padding: 0; }
        .col-left { width: 55%; padding-right: 10px; }
        .col-right { width: 45%; }

        .cliente-box {
            background-color: #fef9e7;
            border-left: 3px solid #f5b301;
            padding: 4px 8px;
            margin-bottom: 6px;
            border-radius: 3px;
        }
        .cliente-box .label { font-size: 7px; color: #6b7280; text-transform: uppercase; display: block; }
        .cliente-box .value { font-size: 9.5px; font-weight: bold; color: #1f2933; }
        .cliente-box .sub { font-size: 7.5px; color: #4b5563; }

        .monto-destacado {
            padding: 8px;
            background: linear-gradient(135deg, #fef9e7 0%, #fef08a 100%);
            border: 1.5px solid #b91c1c;
            border-radius: 5px;
            text-align: center;
        }
        .monto-destacado .label { font-size: 7px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 2px; }
        .monto-destacado .monto { font-size: 20px; font-weight: bold; color: #b91c1c; }
        .monto-destacado .en-letras { font-size: 7px; color: #4b5563; margin-top: 1px; font-style: italic; }

        table.kv { width: 100%; border-collapse: collapse; }
        table.kv td { padding: 1.5px 0; font-size: 8.5px; border-bottom: 0.5px solid #e5e7eb; }
        table.kv td.k { color: #6b7280; width: 45%; }
        table.kv td.v { font-weight: bold; color: #1f2933; text-align: right; }

        .nota-box {
            background-color: #f9fafb;
            border: 0.5px solid #d1d5db;
            border-radius: 3px;
            padding: 3px 6px;
            font-size: 7.5px;
            color: #4b5563;
            margin-top: 5px;
        }

        .signatures-table { width: 100%; margin-top: 14px; border-collapse: collapse; }
        .signatures-table td { width: 50%; text-align: center; vertical-align: top; padding: 0 10px; }
        .sign-line { border-top: 1px solid #1f2933; margin-top: 14px; padding-top: 2px; }
        .sign-name { font-weight: bold; font-size: 8px; }
        .sign-role { color: #6b7280; font-size: 6.5px; margin-top: 1px; }

        .footer-note {
            margin-top: 6px;
            font-size: 5.5px;
            color: #9ca3af;
            text-align: center;
            border-top: 0.5px solid #e5e7eb;
            padding-top: 2px;
        }
    </style>
</head>
<body>

    {{-- Encabezado --}}
    <table class="header-table">
        <tr>
            <td style="width: 68px;">
                <img src="data:image/png;base64,{{ $logoBase64 }}" class="brand-logo">
            </td>
            <td class="header-center">
                <div class="doc-title">Comprobante de anticipo</div>
                <div class="folio-value">{{ $anticipo->folio }}</div>
            </td>
            <td class="header-right" style="width: 46px;">
                <img src="data:image/png;base64,{{ $autobusBase64 }}" class="autobus-img">
            </td>
        </tr>
    </table>
    <hr class="rule">

    <table class="main-grid">
        <tr>
            <td class="col-left">
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

                <div class="monto-destacado">
                    <span class="label">Monto del anticipo</span>
                    <div class="monto">${{ number_format($montoRedondeado, 0) }}</div>
                    <div class="en-letras">
                        ({{ \App\Support\NumberToWords::convert($montoRedondeado) }})
                    </div>
                </div>
            </td>
            <td class="col-right">
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

                @if($anticipo->notas)
                    <div class="nota-box">
                        <strong>Notas:</strong> {{ $anticipo->notas }}
                    </div>
                @endif
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
        Generado el {{ now()->format('d/m/Y H:i') }} · Merlo Transportes · Folio {{ $anticipo->folio }} · Contrato {{ $contrato->folio }}
    </div>

</body>
</html>
