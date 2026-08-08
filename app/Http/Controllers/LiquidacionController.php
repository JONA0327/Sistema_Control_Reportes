<?php

namespace App\Http\Controllers;

use App\Models\Liquidacion;
use App\Models\LiquidacionGasto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class LiquidacionController extends Controller
{
    public function updateKm(Request $request, Liquidacion $liquidacion)
    {
        abort_if($liquidacion->viaje->operador_id !== $request->user()->id, 403);
        abort_if($liquidacion->estado !== 'abierta', 403);

        $data = $request->validate([
            'km_inicial' => ['required', 'numeric', 'min:0'],
            'km_final'   => ['nullable', 'numeric', 'gte:km_inicial'],
        ]);

        $liquidacion->update($data);

        return back()->with('success', 'Kilometraje actualizado.');
    }

    public function storeGasto(Request $request, Liquidacion $liquidacion)
    {
        abort_if($liquidacion->viaje->operador_id !== $request->user()->id, 403);
        abort_if($liquidacion->estado !== 'abierta', 403);

        $data = $request->validate([
            'tipo'       => ['required', Rule::in(array_keys(LiquidacionGasto::TIPOS))],
            'concepto'   => ['nullable', 'string', 'max:255'],
            'monto'      => ['required', 'numeric', 'min:0'],
            'evidencia'  => ['nullable', 'image', 'max:4096'],
        ]);

        $requiereConcepto = ! is_null(LiquidacionGasto::TIPOS[$data['tipo']]['concepto_label']);
        if ($requiereConcepto && empty($data['concepto'])) {
            return back()->withErrors(['concepto' => 'Este campo es obligatorio.'])->withInput();
        }

        LiquidacionGasto::create([
            'liquidacion_id' => $liquidacion->id,
            'tipo'           => $data['tipo'],
            'concepto'       => $requiereConcepto ? $data['concepto'] : null,
            'monto'          => $data['monto'],
            'evidencia_path' => $request->hasFile('evidencia')
                ? $request->file('evidencia')->store('liquidacion_gastos', 'public')
                : null,
            'estado' => 'pendiente',
        ]);

        return back()->with('success', 'Gasto registrado.');
    }

    public function destroyGasto(Request $request, LiquidacionGasto $liquidacionGasto)
    {
        $liquidacion = $liquidacionGasto->liquidacion;

        abort_if($liquidacion->viaje->operador_id !== $request->user()->id, 403);
        abort_if($liquidacion->estado !== 'abierta', 403);

        if ($liquidacionGasto->evidencia_path) {
            Storage::disk('public')->delete($liquidacionGasto->evidencia_path);
        }

        $liquidacionGasto->delete();

        return back()->with('success', 'Gasto eliminado.');
    }

    public function cerrar(Request $request, Liquidacion $liquidacion)
    {
        abort_if($liquidacion->viaje->operador_id !== $request->user()->id, 403);
        abort_if($liquidacion->estado !== 'abierta', 403);

        if (is_null($liquidacion->km_inicial) || is_null($liquidacion->km_final)) {
            return back()->withErrors(['km' => 'Debes registrar el km inicial y final antes de cerrar la liquidación.']);
        }

        $liquidacion->update([
            'estado'     => 'cerrada',
            'cerrada_at' => now(),
            'cerrada_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Liquidación cerrada correctamente.');
    }

    public function aceptarGasto(Request $request, LiquidacionGasto $liquidacionGasto)
    {
        abort_if($liquidacionGasto->liquidacion->estado !== 'cerrada', 403);

        $liquidacionGasto->update([
            'estado'         => 'aceptado',
            'motivo_rechazo' => null,
        ]);

        return back()->with('success', 'Gasto aceptado.');
    }

    public function rechazarGasto(Request $request, LiquidacionGasto $liquidacionGasto)
    {
        abort_if($liquidacionGasto->liquidacion->estado !== 'cerrada', 403);

        $data = $request->validate([
            'motivo_rechazo' => ['nullable', 'string', 'max:255'],
        ]);

        $liquidacionGasto->update([
            'estado'         => 'rechazado',
            'motivo_rechazo' => $data['motivo_rechazo'] ?? null,
        ]);

        return back()->with('success', 'Gasto rechazado.');
    }
}
