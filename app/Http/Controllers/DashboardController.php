<?php

namespace App\Http\Controllers;

use App\Models\Liquidacion;
use App\Models\Report;
use App\Models\Viaje;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->hasRole('mecanico_externo')) {
            return redirect()->route('reports.orden.externo.index');
        }

        $role = $user->roles->first()?->name;

        $showViajes   = in_array($role, ['superadmin', 'administracion', 'operador']);
        $showGastos   = in_array($role, ['superadmin', 'administracion', 'operador']);
        $showReportes = in_array($role, ['superadmin', 'operador', 'mecanico']);

        $soloPropio = $role === 'operador';

        $esDelOperador = function ($q) use ($user) {
            $q->where('operador_id', $user->id)->orWhere('segundo_operador_id', $user->id);
        };

        $viajesActivos = collect();
        if ($showViajes) {
            $viajesActivos = Viaje::with(['bus', 'operador'])
                ->whereHas('liquidacion', fn ($q) => $q->where('estado', 'abierta'))
                ->when($soloPropio, fn ($q) => $q->where($esDelOperador))
                ->orderByDesc('fecha_salida')
                ->take(5)
                ->get();
        }

        $gastosCerrados = collect();
        if ($showGastos) {
            $gastosCerrados = Liquidacion::with(['viaje.bus', 'viaje.operador'])
                ->where('estado', 'cerrada')
                ->when($soloPropio, fn ($q) => $q->whereHas('viaje', $esDelOperador))
                ->orderByDesc('cerrada_at')
                ->take(5)
                ->get();
        }

        $reportesRecientes = collect();
        if ($showReportes) {
            $reportesRecientes = Report::with(['bus', 'operador'])
                ->when($soloPropio, fn ($q) => $q->where('user_id', $user->id))
                ->orderByDesc('created_at')
                ->take(5)
                ->get();
        }

        return view('dashboard', compact(
            'viajesActivos', 'gastosCerrados', 'reportesRecientes',
            'showViajes', 'showGastos', 'showReportes',
        ));
    }
}
