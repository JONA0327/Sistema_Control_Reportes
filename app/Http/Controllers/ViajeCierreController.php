<?php

namespace App\Http\Controllers;

use App\Models\Viaje;
use Illuminate\Http\Request;

class ViajeCierreController extends Controller
{
    public function show(Viaje $viaje)
    {
        abort_unless($viaje->contrato, 404);

        $viaje->load('contrato', 'bus', 'operador', 'liquidacion');

        return view('viajes.cierre', [
            'viaje' => $viaje,
            'egresos' => $viaje->egresosDelCierre(),
            'cortes' => $viaje->es_compras ? $viaje->cortesMerlo() : collect(),
            'bloqueo' => $viaje->motivoSinCierre(),
            'desglose' => $viaje->liquidacion?->desglose(),
        ]);
    }

    public function store(Request $request, Viaje $viaje)
    {
        abort_unless($viaje->es_compras, 404);

        if ($motivo = $viaje->motivoSinCierre()) {
            return back()->with('error', $motivo);
        }

        $viaje->cerrar($request->user()->id);

        return redirect()->route('viajes.cierre', $viaje)
            ->with('success', 'Viaje cerrado: sus egresos e ingresos ya están en Ingresos y Egresos.');
    }
}
