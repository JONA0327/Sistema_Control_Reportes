<?php

namespace App\Http\Controllers;

use App\Models\DieselCarga;
use App\Models\Viaje;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DieselCargaController extends Controller
{
    // ===== Lado operador =====

    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $viajes = Viaje::with(['bus', 'operador', 'segundoOperador', 'dieselCargas' => fn($q) => $q->orderByDesc('created_at'), 'liquidacion.gastos'])
            ->where(fn($q) => $q->where('operador_id', $userId)->orWhere('segundo_operador_id', $userId))
            ->orderByDesc('fecha_salida')
            ->get();

        return view('gastos.index', compact('viajes', 'userId'));
    }

    public function storeExtraByOperador(Request $request, Viaje $viaje)
    {
        abort_if($viaje->operador_id !== $request->user()->id, 403);

        $data = $request->validate([
            'monto'  => ['required', 'numeric', 'min:0'],
            'fuente' => ['required', 'in:gastos_entregados,transferencia'],
        ]);

        DieselCarga::create([
            'viaje_id'         => $viaje->id,
            'tipo'             => 'extra',
            'monto'            => $data['monto'],
            'fuente'           => $data['fuente'],
            'origen'           => 'operador',
            'estado_solicitud' => 'pendiente',
            'requested_by'     => $request->user()->id,
        ]);

        return back()->with('success', 'Solicitud de diésel extra enviada. Espera la aprobación de administración.');
    }

    public function storeEvidencia(Request $request, DieselCarga $dieselCarga)
    {
        abort_if($dieselCarga->viaje->operador_id !== $request->user()->id, 403);
        abort_if($dieselCarga->estado_solicitud !== 'aprobada', 403);

        $data = $request->validate([
            'litros'      => ['required', 'numeric', 'min:0'],
            'costo_litro' => ['required', 'numeric', 'min:0'],
            'ticket'      => ['required', 'image', 'max:4096'],
            'foto_litros' => ['required', 'image', 'max:4096'],
        ]);

        if ($dieselCarga->ticket_path) {
            Storage::disk('public')->delete($dieselCarga->ticket_path);
        }
        if ($dieselCarga->foto_litros_path) {
            Storage::disk('public')->delete($dieselCarga->foto_litros_path);
        }

        $dieselCarga->update([
            'litros'            => $data['litros'],
            'costo_litro'       => $data['costo_litro'],
            'ticket_path'       => $request->file('ticket')->store('diesel_cargas', 'public'),
            'foto_litros_path'  => $request->file('foto_litros')->store('diesel_cargas', 'public'),
            'estado_evidencia'  => 'enviada',
            'motivo_rechazo'    => null,
        ]);

        return back()->with('success', 'Evidencia enviada. Queda pendiente de validación por administración.');
    }

    // ===== Lado administración =====

    public function showForViaje(Viaje $viaje)
    {
        abort_if($viaje->esta_pendiente, 403, 'Completa la información del viaje antes de gestionar sus gastos.');

        $viaje->load(['bus', 'operador', 'dieselCargas' => fn($q) => $q->orderByDesc('created_at'), 'dieselCargas.requestedBy', 'dieselCargas.reviewedBy', 'liquidacion.gastos']);

        return view('viajes.gastos', compact('viaje'));
    }

    public function storeExtraByAdmin(Request $request, Viaje $viaje)
    {
        abort_if($viaje->esta_pendiente, 403, 'Completa la información del viaje antes de registrar gastos.');

        $data = $request->validate([
            'monto' => ['required', 'numeric', 'min:0'],
        ]);

        $dieselCarga = DieselCarga::create([
            'viaje_id'         => $viaje->id,
            'tipo'             => 'extra',
            'monto'            => $data['monto'],
            'origen'           => 'administracion',
            'estado_solicitud' => 'aprobada',
            'requested_by'     => $request->user()->id,
            'reviewed_by'      => $request->user()->id,
            'reviewed_at'      => now(),
        ]);

        $dieselCarga->registrarEgresoAutomatico($request->user()->id);

        return back()->with('success', 'Diésel extra registrado. El operador solo deberá anexar la evidencia.');
    }

    public function aprobarSolicitud(Request $request, DieselCarga $dieselCarga)
    {
        abort_if($dieselCarga->estado_solicitud !== 'pendiente', 403);

        $dieselCarga->update([
            'estado_solicitud' => 'aprobada',
            'reviewed_by'      => $request->user()->id,
            'reviewed_at'      => now(),
        ]);

        $dieselCarga->registrarEgresoAutomatico($request->user()->id);

        return back()->with('success', 'Solicitud de diésel extra aprobada.');
    }

    public function rechazarSolicitud(Request $request, DieselCarga $dieselCarga)
    {
        abort_if($dieselCarga->estado_solicitud !== 'pendiente', 403);

        $data = $request->validate([
            'motivo_rechazo' => ['nullable', 'string', 'max:255'],
        ]);

        $dieselCarga->update([
            'estado_solicitud' => 'rechazada',
            'motivo_rechazo'   => $data['motivo_rechazo'] ?? null,
            'reviewed_by'      => $request->user()->id,
            'reviewed_at'      => now(),
        ]);

        return back()->with('success', 'Solicitud de diésel extra rechazada.');
    }

    public function aprobarEvidencia(Request $request, DieselCarga $dieselCarga)
    {
        abort_if($dieselCarga->estado_evidencia !== 'enviada', 403);

        $dieselCarga->update([
            'estado_evidencia' => 'validada',
            'reviewed_by'      => $request->user()->id,
            'reviewed_at'      => now(),
        ]);

        return back()->with('success', 'Evidencia validada.');
    }

    public function rechazarEvidencia(Request $request, DieselCarga $dieselCarga)
    {
        abort_if($dieselCarga->estado_evidencia !== 'enviada', 403);

        $data = $request->validate([
            'motivo_rechazo' => ['nullable', 'string', 'max:255'],
        ]);

        if ($dieselCarga->ticket_path) {
            Storage::disk('public')->delete($dieselCarga->ticket_path);
        }
        if ($dieselCarga->foto_litros_path) {
            Storage::disk('public')->delete($dieselCarga->foto_litros_path);
        }

        $dieselCarga->update([
            'estado_evidencia' => 'rechazada',
            'litros'           => null,
            'ticket_path'      => null,
            'foto_litros_path' => null,
            'motivo_rechazo'   => $data['motivo_rechazo'] ?? null,
            'reviewed_by'      => $request->user()->id,
            'reviewed_at'      => now(),
        ]);

        return back()->with('success', 'Evidencia rechazada. El operador deberá volver a subirla.');
    }
}
