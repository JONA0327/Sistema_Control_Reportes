<?php

namespace App\Http\Controllers;

use App\Models\Liquidacion;
use App\Models\Report;
use App\Models\Viaje;

class DashboardController extends Controller
{
    public function index()
    {
        $viajesActivos = Viaje::with(['bus', 'operador'])
            ->whereHas('liquidacion', fn ($q) => $q->where('estado', 'abierta'))
            ->orderByDesc('fecha_salida')
            ->take(5)
            ->get();

        $gastosCerrados = Liquidacion::with(['viaje.bus', 'viaje.operador'])
            ->where('estado', 'cerrada')
            ->orderByDesc('cerrada_at')
            ->take(5)
            ->get();

        $reportesRecientes = Report::with(['bus', 'operador'])
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        return view('dashboard', compact('viajesActivos', 'gastosCerrados', 'reportesRecientes'));
    }
}
