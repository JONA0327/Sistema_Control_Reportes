<?php

namespace App\Http\Controllers;

use App\Models\DieselCarga;
use App\Models\IngresoEgreso;
use App\Models\Liquidacion;
use App\Models\Movimiento;
use App\Models\Viaje;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DieselCargaController extends Controller
{
    // ===== Lado operador =====

    public function index(Request $request)
    {
        $userId = $request->user()->id;

        // Administración (y superadmin) ve todos los viajes con liquidación abierta,
        // tengan o no operador asignado, y los gestiona desde su pantalla de gastos.
        $esAdmin = $request->user()->can('gastos.aprobar');
        $vista = $request->query('estado') === 'cerrados' ? 'cerrados' : 'curso';

        $viajes = Viaje::with(['bus', 'operador', 'segundoOperador', 'dieselCargas' => fn($q) => $q->orderByDesc('created_at'), 'liquidacion.gastos'])
            ->where(function ($q) use ($userId, $esAdmin, $vista) {
                $q->where('operador_id', $userId)->orWhere('segundo_operador_id', $userId);

                if ($esAdmin && $vista === 'cerrados') {
                    $q->orWhereHas('liquidacion', fn ($l) => $l->where('estado', 'cerrada'));
                } elseif ($esAdmin) {
                    $q->orWhereHas('liquidacion', fn ($l) => $l->where('estado', 'abierta'))->orWhereDoesntHave('liquidacion');
                }
            })
            ->orderByDesc('fecha_salida')
            ->get()
            ->reject(fn ($v) => $esAdmin && ($v->esta_pendiente || ($vista === 'cerrados' ? $v->liquidacion?->estado !== 'cerrada' : $v->liquidacion?->estado === 'cerrada')))
            ->values();

        return view('gastos.index', compact('viajes', 'userId', 'esAdmin', 'vista'));
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

        Liquidacion::firstOrCreate(['viaje_id' => $viaje->id], ['estado' => 'abierta']);

        $viaje->load(['bus', 'operador', 'dieselCargas' => fn($q) => $q->orderByDesc('created_at'), 'dieselCargas.requestedBy', 'dieselCargas.reviewedBy', 'liquidacion.gastos']);

        return view('viajes.gastos', compact('viaje'));
    }

    /**
     * Diésel del viaje, que se captura al cerrar la liquidación: salida
     * (siempre) y regreso (si ya había extras se jalan, si no se pone a
     * mano). Se puede corregir después; cada cambio queda en movimientos.
     */
    public function guardarDieselCierre(Request $request, Viaje $viaje)
    {
        abort_unless($viaje->liquidacion?->estado === 'cerrada', 403, 'Cierra primero la liquidación.');

        $request->validate([
            'cargas'                  => ['nullable', 'array'],
            'cargas.*.monto'          => ['required', 'numeric', 'min:0'],
            'cargas.*.litros'         => ['nullable', 'numeric', 'min:0'],
            'cargas.*.costo_litro'    => ['nullable', 'numeric', 'min:0'],
            'salida'                  => ['nullable', 'array'],
            'salida.monto'            => ['nullable', 'numeric', 'min:0'],
            'salida.litros'           => ['nullable', 'numeric', 'min:0'],
            'salida.costo_litro'      => ['nullable', 'numeric', 'min:0'],
            'regreso'                 => ['nullable', 'array'],
            'regreso.monto'           => ['nullable', 'numeric', 'min:0'],
            'regreso.litros'          => ['nullable', 'numeric', 'min:0'],
            'regreso.costo_litro'     => ['nullable', 'numeric', 'min:0'],
        ]);

        $userId = $request->user()->id;
        $cargas = $viaje->dieselCargas()->where('estado_solicitud', 'aprobada')->get()->keyBy('id');
        $valores = [];

        foreach ($request->input('cargas', []) as $id => $fila) {
            if ($cargas->has($id)) {
                $valores[$id] = $fila;
            }
        }

        $fmt = fn ($monto, $litros, $costo) => sprintf('$%s · %s L · $%s/L', number_format((float) $monto, 2), $litros ?? '—', $costo ?? '—');
        $normalizar = function (array $fila): array {
            $monto = (float) $fila['monto'];
            $litros = ($fila['litros'] ?? '') !== '' ? (float) $fila['litros'] : null;
            $costo = ($fila['costo_litro'] ?? '') !== '' ? (float) $fila['costo_litro']
                : ($litros > 0 ? round($monto / $litros, 2) : null);

            return ['monto' => $monto, 'litros' => $litros, 'costo_litro' => $costo];
        };
        $nombres = ['inicial' => 'Diésel de salida', 'extra' => 'Diésel de regreso (extra)'];

        // Si aún no existía la carga de salida, o ningún extra, se crean con lo capturado.
        foreach (['salida' => 'inicial', 'regreso' => 'extra'] as $campo => $tipo) {
            $fila = $request->input($campo);
            if (! $fila || ($fila['monto'] ?? '') === '') {
                continue;
            }

            $v = $normalizar($fila);
            $carga = $viaje->dieselCargas()->create($v + [
                'tipo'             => $tipo,
                'origen'           => 'administracion',
                'estado_solicitud' => 'aprobada',
                'requested_by'     => $userId,
                'reviewed_by'      => $userId,
                'reviewed_at'      => now(),
            ]);
            $carga->registrarEgresoAutomatico($userId);

            Movimiento::registrar('viajes', 'creado', "{$nombres[$tipo]} del viaje {$viaje->no_contrato}: ".$fmt($v['monto'], $v['litros'], $v['costo_litro']), null, $carga, $userId);
        }

        foreach ($valores as $id => $fila) {
            $carga = $cargas[$id];
            $v = $normalizar($fila);

            if ((float) $carga->monto === $v['monto'] && (float) $carga->litros === (float) $v['litros'] && (float) $carga->costo_litro === (float) $v['costo_litro']) {
                continue;
            }

            $antes = $fmt($carga->monto, $carga->litros, $carga->costo_litro);
            $carga->update($v);
            IngresoEgreso::actualizarMontoDesdeOrigen($carga, $v['monto']);

            Movimiento::registrar('viajes', 'editado', "{$nombres[$carga->tipo]} del viaje {$viaje->no_contrato}: {$antes} → ".$fmt($v['monto'], $v['litros'], $v['costo_litro']), null, $carga, $userId);
        }

        return back()->with('success', 'Diésel del viaje guardado.');
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
