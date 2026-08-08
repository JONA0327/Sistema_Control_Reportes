<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\DieselCarga;
use App\Models\Liquidacion;
use App\Models\User;
use App\Models\Viaje;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ViajeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $viajes = Viaje::with(['bus', 'operador', 'liquidacion'])
            ->when($search, fn($q) => $q
                ->where('no_contrato', 'like', "%{$search}%")
                ->orWhere('origen', 'like', "%{$search}%")
                ->orWhere('destino', 'like', "%{$search}%")
            )
            ->orderByDesc('fecha_salida')
            ->paginate(10)
            ->withQueryString();

        return view('viajes.index', compact('viajes', 'search'));
    }

    public function create()
    {
        $buses = Bus::where('status', 'activo')->orderBy('num_bus')->get();
        $operadores = User::role('operador')->where('is_active', true)->orderBy('name')->get();

        return view('viajes.create', compact('buses', 'operadores'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        $viaje = Viaje::create($data);

        Liquidacion::create(['viaje_id' => $viaje->id]);

        DieselCarga::create([
            'viaje_id'         => $viaje->id,
            'tipo'             => 'inicial',
            'monto'            => $viaje->gasto_diesel_inicio,
            'origen'           => 'administracion',
            'estado_solicitud' => 'aprobada',
            'requested_by'     => $request->user()->id,
            'reviewed_by'      => $request->user()->id,
            'reviewed_at'      => now(),
        ]);

        return redirect()->route('viajes.index')
            ->with('success', "Viaje {$viaje->no_contrato} registrado correctamente.");
    }

    public function edit(Viaje $viaje)
    {
        $buses = Bus::where('status', 'activo')->orderBy('num_bus')->get();
        $operadores = User::role('operador')->where('is_active', true)->orderBy('name')->get();

        return view('viajes.edit', compact('viaje', 'buses', 'operadores'));
    }

    public function update(Request $request, Viaje $viaje)
    {
        $data = $this->validateData($request, $viaje);

        $viaje->update($data);

        $viaje->dieselCargas()
            ->where('tipo', 'inicial')
            ->update(['monto' => $viaje->gasto_diesel_inicio]);

        return redirect()->route('viajes.edit', $viaje)
            ->with('success', "Viaje {$viaje->no_contrato} actualizado correctamente.");
    }

    public function destroy(Viaje $viaje)
    {
        foreach ($viaje->dieselCargas as $carga) {
            if ($carga->ticket_path) {
                Storage::disk('public')->delete($carga->ticket_path);
            }
            if ($carga->foto_litros_path) {
                Storage::disk('public')->delete($carga->foto_litros_path);
            }
        }

        if ($viaje->liquidacion) {
            foreach ($viaje->liquidacion->gastos as $gasto) {
                if ($gasto->evidencia_path) {
                    Storage::disk('public')->delete($gasto->evidencia_path);
                }
            }
        }

        $viaje->delete();

        return redirect()->route('viajes.index')
            ->with('success', 'Viaje eliminado correctamente.');
    }

    private function validateData(Request $request, ?Viaje $viaje = null): array
    {
        return $request->validate([
            'no_contrato'          => ['required', 'string', 'max:255'],
            'bus_id'                => ['required', 'exists:buses,id'],
            'operador_id'           => ['required', 'exists:users,id'],
            'origen'                => ['required', 'string', 'max:255'],
            'destino'               => ['required', 'string', 'max:255'],
            'recorridos'            => ['required', 'string'],
            'fecha_salida'          => ['required', 'date'],
            'fecha_regreso'         => ['required', 'date', 'after_or_equal:fecha_salida'],
            'costo_viaje'           => ['required', 'numeric', 'min:0'],
            'gastos_entregados'     => ['required', 'numeric', 'min:0'],
            'gasto_diesel_inicio'   => ['required', 'numeric', 'min:0'],
        ]);
    }
}
