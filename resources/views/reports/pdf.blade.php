<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Orden de trabajo {{ $report->folio }}</title>
    <style>
        @page { margin: 20px 34px; }
        * { box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10.5px;
            color: #1f2933;
            line-height: 1.45;
        }

        /* Encabezado */
        .header-table { width: 100%; border-bottom: 2.5px solid #b91c1c; padding-bottom: 6px; margin-bottom: 8px; }
        .header-table td { vertical-align: middle; }
        .brand-logo { width: 60px; }
        .brand-name { font-size: 15px; font-weight: bold; color: #1f2933; padding-left: 10px; }
        .brand-sub { font-size: 9px; color: #6b7280; padding-left: 10px; }
        .doc-title { text-align: right; font-size: 16px; font-weight: bold; color: #b91c1c; }
        .doc-folio { text-align: right; font-size: 10.5px; color: #4b5563; margin-top: 2px; }

        h2.section-title {
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #ffffff;
            background-color: #1f2933;
            padding: 4px 8px;
            margin: 8px 0 5px 0;
        }

        table.data-grid { width: 100%; border-collapse: collapse; margin-bottom: 4px; table-layout: fixed; page-break-inside: avoid; }
        table.data-grid td { padding: 3px 6px 3px 0; vertical-align: top; width: 25%; }
        table.data-grid td.label { color: #6b7280; font-size: 9px; text-transform: uppercase; display: block; }
        table.data-grid td.value { color: #1f2933; font-weight: bold; }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            border: 0.75px solid #9ca3af;
            color: #1f2933;
        }
        .badge-verde { background-color: #dcfce7; border-color: #16a34a; color: #14532d; }
        .badge-amarillo { background-color: #fef9c3; border-color: #ca8a04; color: #713f12; }
        .badge-rojo { background-color: #fee2e2; border-color: #dc2626; color: #7f1d1d; }
        .badge-nuevo { background-color: #dbeafe; border-color: #2563eb; color: #1e3a8a; }
        .badge-en_proceso { background-color: #fef9c3; border-color: #ca8a04; color: #713f12; }
        .badge-resuelto { background-color: #dcfce7; border-color: #16a34a; color: #14532d; }

        .box {
            border: 0.75px solid #d1d5db;
            border-radius: 4px;
            padding: 6px 10px;
            margin-bottom: 6px;
            background-color: #f9fafb;
            page-break-inside: avoid;
        }

        .chip {
            display: inline-block;
            border: 0.75px solid #9ca3af;
            border-radius: 3px;
            padding: 2px 7px;
            margin: 0 4px 4px 0;
            font-size: 9px;
        }

        .photos-table { width: 100%; margin-top: 4px; margin-bottom: 6px; table-layout: fixed; page-break-inside: avoid; }
        .photos-table td { width: 25%; height: 75px; padding: 3px; text-align: center; vertical-align: middle; border: 0.75px solid #d1d5db; border-radius: 3px; background-color: #f9fafb; }
        .photos-table img { max-width: 100%; max-height: 68px; }

        table.kv { width: 100%; border-collapse: collapse; }
        table.kv td { padding: 1.5px 0; font-size: 10px; }
        table.kv td.k { color: #6b7280; width: 32%; }
        table.kv td.v { font-weight: bold; color: #1f2933; }

        table.parts-table { width: 100%; border-collapse: collapse; font-size: 9.5px; }
        table.parts-table th { text-align: left; color: #6b7280; font-size: 8.5px; text-transform: uppercase; padding: 3px 4px; border-bottom: 0.75px solid #d1d5db; }
        table.parts-table td { padding: 3px 4px; border-bottom: 0.5px solid #e5e7eb; }
        table.parts-table td:last-child, table.parts-table th:last-child { text-align: right; }

        .sign-block { margin-top: 6px; page-break-inside: avoid; }
        .signatures-table { width: 100%; margin-top: 16px; border-collapse: collapse; }
        .signatures-table td { width: 50%; text-align: center; vertical-align: top; padding: 0 16px; }
        .sign-line { border-top: 1px solid #1f2933; margin-top: 26px; padding-top: 4px; }
        .sign-name { font-weight: bold; font-size: 10.5px; }
        .sign-role { color: #6b7280; font-size: 9px; margin-top: 1px; }
        .sign-date { margin-top: 8px; font-size: 9px; color: #6b7280; }
        .sign-date span { display: inline-block; border-bottom: 0.75px solid #9ca3af; width: 110px; }

        .footer-note {
            margin-top: 10px;
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
            <td style="width: 60px;">
                <img src="data:image/png;base64,{{ $logoBase64 }}" class="brand-logo">
            </td>
            <td>
                <div class="brand-name">Merlo Transportes</div>
                <div class="brand-sub">Panel administrativo — Control de Reportes</div>
            </td>
            <td>
                <div class="doc-title">Orden de trabajo</div>
                <div class="doc-folio">Folio {{ $report->folio }}</div>
                <div class="doc-folio">Emitido: {{ now()->format('d/m/Y H:i') }}</div>
            </td>
        </tr>
    </table>

    {{-- Datos del reporte --}}
    <h2 class="section-title">Datos del reporte</h2>
    <table class="data-grid">
        <tr>
            <td>
                <span class="label">Unidad</span>
                <span class="value">Bus #{{ $report->bus->num_bus }} ({{ $report->bus->placa }})</span>
            </td>
            <td>
                <span class="label">Reportado por</span>
                <span class="value">{{ $report->operador->name }} {{ $report->operador->last_name }}</span>
            </td>
            <td>
                <span class="label">Fecha de reporte</span>
                <span class="value">{{ $report->created_at->format('d/m/Y H:i') }}</span>
            </td>
            <td>
                <span class="label">Kilometraje reportado</span>
                <span class="value">{{ number_format($report->km_actual) }} km</span>
            </td>
        </tr>
    </table>
    <table class="data-grid">
        <tr>
            <td>
                <span class="label">Urgencia</span>
                @php
                    $urgLabel = \App\Models\Report::URGENCIAS[$report->urgencia] ?? '—';
                @endphp
                <span class="badge badge-{{ $report->urgencia }}">{{ $urgLabel }}</span>
            </td>
            <td>
                <span class="label">Estado del reporte</span>
                <span class="badge badge-{{ $report->status }}">{{ ucfirst(str_replace('_', ' ', $report->status)) }}</span>
            </td>
            <td colspan="2">
                <span class="label">Categorías de falla</span>
                <span>
                    @foreach($report->categorias ?? [] as $cat)
                        <span class="chip">{{ \App\Models\Report::CATEGORIAS[$cat] ?? $cat }}</span>
                    @endforeach
                </span>
            </td>
        </tr>
    </table>

    <div class="box">
        <span class="label" style="display:block; margin-bottom: 3px;">Descripción de la falla</span>
        {{ $report->description }}
    </div>

    @if($report->photos->count() > 0)
        <table class="photos-table">
            <tr>
                @foreach($report->photos->take(4) as $photo)
                    @php
                        $photoData = \Illuminate\Support\Facades\Storage::disk('public')->exists($photo->evidence_path)
                            ? base64_encode(\Illuminate\Support\Facades\Storage::disk('public')->get($photo->evidence_path))
                            : null;
                    @endphp
                    @if($photoData)
                        <td><img src="data:image/jpeg;base64,{{ $photoData }}"></td>
                    @endif
                @endforeach
            </tr>
        </table>
    @endif

    {{-- Orden de trabajo --}}
    <h2 class="section-title">Orden de trabajo</h2>
    <table class="data-grid">
        <tr>
            <td>
                <span class="label">Recepción</span>
                <span class="value">{{ $orden->recibido_at->format('d/m/Y H:i') }}</span>
            </td>
            <td>
                <span class="label">Tipo de atención</span>
                <span class="value">{{ \App\Models\OrdenTrabajo::TIPOS_ATENCION[$orden->tipo_atencion] ?? '—' }}</span>
            </td>
            <td colspan="2">
                <span class="label">Atendido por</span>
                <span class="value">{{ $firmanteNombre }}</span>
            </td>
        </tr>
    </table>

    <div class="box">
        <table class="kv">
            <tr>
                <td class="k">Falla confirmada</td>
                <td class="v">{{ \App\Models\OrdenTrabajo::FALLA_CONFIRMADA[$orden->falla_confirmada] ?? 'Sin especificar' }}</td>
            </tr>
            <tr>
                <td class="k">Subsistema afectado</td>
                <td class="v">
                    @php $subs = collect($orden->subsistemas ?? [])->map(fn($s) => \App\Models\OrdenTrabajo::SUBSISTEMAS[$s] ?? $s)->implode(', '); @endphp
                    {{ $subs ?: 'Sin especificar' }}
                </td>
            </tr>
            <tr>
                <td class="k">Código de falla (DTC / J1939)</td>
                <td class="v">{{ $orden->codigo_falla ?: '—' }}</td>
            </tr>
        </table>
        @if($orden->diagnostico)
            <div style="margin-top: 6px;">
                <span class="label" style="display:block; margin-bottom: 3px;">Diagnóstico y causa raíz</span>
                {{ $orden->diagnostico }}
            </div>
        @endif
    </div>

    @if($orden->tipo_atencion === 'taller_interno' && $report->partsUsed->isNotEmpty())
        <div class="box">
            <span class="label" style="display:block; margin-bottom: 4px;">Refacciones utilizadas (descontadas de inventario)</span>
            <table class="parts-table">
                <tr>
                    <th>Código</th>
                    <th>Refacción</th>
                    <th>Cantidad</th>
                </tr>
                @foreach($report->partsUsed as $parte)
                <tr>
                    <td>{{ $parte->item->code }}</td>
                    <td>{{ $parte->item->name }}</td>
                    <td>{{ $parte->quantity_used }}</td>
                </tr>
                @endforeach
            </table>
        </div>
    @endif

    @if($orden->tipo_atencion === 'taller_externo')
        <div class="box">
            <span class="label" style="display:block; margin-bottom: 4px;">Canalización a taller externo</span>
            <table class="kv">
                <tr>
                    <td class="k">Proveedor / taller externo</td>
                    <td class="v">{{ $orden->proveedor_externo ?: '—' }}</td>
                </tr>
                <tr>
                    <td class="k">Folio del proveedor</td>
                    <td class="v">{{ $orden->folio_proveedor ?: '—' }}</td>
                </tr>
                <tr>
                    <td class="k">Motivo del envío externo</td>
                    <td class="v">
                        @php $motivos = collect($orden->motivo_externo ?? [])->map(fn($m) => \App\Models\OrdenTrabajo::MOTIVOS_EXTERNO[$m] ?? $m)->implode(', '); @endphp
                        {{ $motivos ?: '—' }}
                    </td>
                </tr>
                <tr>
                    <td class="k">Fecha promesa de entrega</td>
                    <td class="v">{{ $orden->fecha_promesa_entrega?->format('d/m/Y H:i') ?: '—' }}</td>
                </tr>
            </table>
        </div>
    @endif

    {{-- Firmas --}}
    <div class="sign-block">
        <table class="signatures-table">
            <tr>
                <td>
                    <div class="sign-line">
                        <div class="sign-name">{{ $firmanteNombre }}</div>
                        <div class="sign-role">{{ $firmanteRol }} — Firma de conformidad de atención</div>
                    </div>
                    <div class="sign-date">Fecha: <span>&nbsp;</span></div>
                </td>
                <td>
                    <div class="sign-line">
                        <div class="sign-name">{{ $report->operador->name }} {{ $report->operador->last_name }}</div>
                        <div class="sign-role">Operador — Firma de enterado</div>
                    </div>
                    <div class="sign-date">Fecha: <span>&nbsp;</span></div>
                </td>
            </tr>
        </table>

        <div class="footer-note">
            Documento generado por el sistema de Control de Reportes de Merlo Transportes · {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

</body>
</html>
