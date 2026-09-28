<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Comprobante de anticipo {{ $anticipo->folio }}</title>
    <style>
        @page { margin: 18px 20px; }
        * { box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9.5px;
            color: #1f2933;
            line-height: 1.4;
        }

        .header-table { width: 100%; margin-bottom: 8px; }
        .header-table td { vertical-align: middle; }
        .brand-logo { width: 95px; }
        .autobus-img { width: 62px; }
        .header-right { text-align: right; }

        .doc-title {
            text-align: center;
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #b91c1c;
            padding-bottom: 5px;
            margin: 0 0 6px 0;
        }

        .folio-box {
            text-align: center;
            margin-bottom: 8px;
        }
        .folio-label { font-size: 7.5px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; }
        .folio-value { font-size: 15px; font-weight: bold; color: #b91c1c; font-family: 'DejaVu Sans Mono', monospace; }

        .cliente-box {
            background-color: #fef9e7;
            border-left: 3px solid #f5b301;
            padding: 6px 10px;
            margin-bottom: 8px;
            border-radius: 3px;
        }
        .cliente-box .label { font-size: 7.5px; color: #6b7280; text-transform: uppercase; display: block; margin-bottom: 1px; }
        .cliente-box .value { font-size: 11px; font-weight: bold; color: #1f2933; }
        .cliente-box .sub { font-size: 8.5px; color: #4b5563; }

        .monto-destacado {
            margin: 8px 0;
            padding: 12px;
            background: linear-gradient(135deg, #fef9e7 0%, #fef08a 100%);
            border: 2px solid #b91c1c;
            border-radius: 6px;
            text-align: center;
        }
        .monto-destacado .label { font-size: 8px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 3px; }
        .monto-destacado .monto { font-size: 24px; font-weight: bold; color: #b91c1c; }
        .monto-destacado .en-letras { font-size: 8px; color: #4b5563; margin-top: 2px; font-style: italic; }

        table.kv { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        table.kv td { padding: 2.5px 0; font-size: 9.5px; border-bottom: 0.5px solid #e5e7eb; }
        table.kv td.k { color: #6b7280; width: 42%; }
        table.kv td.v { font-weight: bold; color: #1f2933; text-align: right; }

        .nota-box {
            background-color: #f9fafb;
            border: 0.5px solid #d1d5db;
            border-radius: 3px;
            padding: 5px 8px;
            font-size: 8.5px;
            color: #4b5563;
            margin-bottom: 8px;
        }

        .signatures-table { width: 100%; margin-top: 22px; border-collapse: collapse; }
        .signatures-table td { width: 50%; text-align: center; vertical-align: top; padding: 0 6px; }
        .sign-line { border-top: 1px solid #1f2933; margin-top: 24px; padding-top: 3px; }
        .sign-name { font-weight: bold; font-size: 9px; }
        .sign-role { color: #6b7280; font-size: 7.5px; margin-top: 1px; }

        .footer-note {
            margin-top: 12px;
            font-size: 6.5px;
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
            <td style="width: 95px;">
                <img src="data:image/png;base64,{{ $logoBase64 }}" class="brand-logo">
            </td>
            <td></td>
            <td class="header-right" style="width: 62px;">
                <img src="data:image/png;base64,{{ $autobusBase64 }}" class="autobus-img">
            </td>
        </tr>
    </table>

    <div class="doc-title">Comprobante de anticipo</div>
    <div class="folio-box">
        <div class="folio-label">Folio</div>
        <div class="folio-value">{{ $anticipo->folio }}</div>
    </div>

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
        <div class="monto">${{ number_format($anticipo->monto, 2) }}</div>
        <div class="en-letras">
            ({{ \App\Support\NumberToWords::convert((float) $anticipo->monto) }})
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
