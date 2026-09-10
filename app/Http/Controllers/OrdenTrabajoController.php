<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\OrdenTrabajo;
use App\Models\OrdenTrabajoPieza;
use App\Models\Report;
use App\Models\ReportPartUsed;
use App\Models\User;
use App\Notifications\ReporteEstadoActualizadoNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class OrdenTrabajoController extends Controller
{
    public function show(Request $request, Report $report)
    {
        $orden = $report->ordenTrabajo;

        if (! $orden) {
            $orden = OrdenTrabajo::create([
                'report_id'     => $report->id,
                'mecanico_id'   => $request->user()->id,
                'recibido_at'   => now(),
                'tipo_atencion' => 'taller_interno',
            ]);

            if ($report->status === 'nuevo') {
                $estadoAnterior = $report->status;
                $report->update(['status' => 'en_proceso']);
                $this->notificarCambioEstado($report, $estadoAnterior, 'en_proceso', $request);
            }
        }

        $report->load(['bus', 'operador', 'photos', 'videos', 'partsUsed.item', 'partsUsed.installedBy']);
        $orden->load(['piezas' => fn ($q) => $q->with('user')->latest('id')]);
        $mecanicos = User::role('mecanico')->where('is_active', true)->orderBy('name')->get();
        $mecanicosExternos = User::role('mecanico_externo')->where('is_active', true)->orderBy('name')->get();
        $inventario = InventoryItem::where('stock_quantity', '>', 0)->orderBy('name')->get();

        return view('reports.orden', compact('report', 'orden', 'mecanicos', 'mecanicosExternos', 'inventario'));
    }

    public function update(Request $request, Report $report)
    {
        $orden = $report->ordenTrabajo()->firstOrFail();

        $data = $request->validate([
            'mecanico_id'                => ['required', 'exists:users,id'],
            'tipo_atencion'              => ['required', Rule::in(array_keys(OrdenTrabajo::TIPOS_ATENCION))],
            'falla_confirmada'           => ['nullable', Rule::in(array_keys(OrdenTrabajo::FALLA_CONFIRMADA))],
            'subsistemas'                => ['nullable', 'array'],
            'subsistemas.*'              => [Rule::in(array_keys(OrdenTrabajo::SUBSISTEMAS))],
            'codigo_falla'               => ['nullable', 'string', 'max:255'],
            'diagnostico'                => ['nullable', 'string', 'max:2000'],
            'proveedor_externo'          => ['nullable', 'string', 'max:255'],
            'proveedor_externo_user_id'  => ['nullable', 'exists:users,id'],
            'folio_proveedor'            => ['nullable', 'string', 'max:255'],
            'motivo_externo'             => ['nullable', 'array'],
            'motivo_externo.*'           => [Rule::in(array_keys(OrdenTrabajo::MOTIVOS_EXTERNO))],
            'fecha_promesa_entrega'      => ['nullable', 'date'],
        ]);

        $orden->update($data);

        return redirect()->route('reports.orden.show', $report)
            ->with('success', 'Orden de trabajo actualizada correctamente.');
    }

    public function completar(Request $request, Report $report)
    {
        $orden = $report->ordenTrabajo()->firstOrFail();

        abort_if(! $orden->updated_at->gt($orden->created_at), 403, 'Guarda el avance de la orden antes de marcarla como completada.');
        abort_if($report->status === 'resuelto', 403);

        $estadoAnterior = $report->status;
        $report->update([
            'status'      => 'resuelto',
            'resolved_at' => now(),
        ]);
        $this->notificarCambioEstado($report, $estadoAnterior, 'resuelto', $request);

        return redirect()->route('reports.orden.show', $report)
            ->with('success', "Reporte {$report->folio} marcado como completado.");
    }

    public function storeParte(Request $request, Report $report)
    {
        $orden = $report->ordenTrabajo()->firstOrFail();
        abort_if($orden->tipo_atencion !== 'taller_interno', 403, 'Solo se pueden registrar refacciones cuando la atención es en taller interno.');

        $data = $request->validate([
            'item_id'       => ['required', 'exists:inventory_items,id'],
            'quantity_used' => ['required', 'integer', 'min:1'],
        ]);

        $item = InventoryItem::findOrFail($data['item_id']);

        if ($data['quantity_used'] > $item->stock_quantity) {
            return back()->withErrors(['quantity_used' => 'No hay suficiente stock disponible para esta refacción.'])->withInput();
        }

        DB::transaction(function () use ($report, $item, $data, $request) {
            $movement = $item->movements()->create([
                'user_id'       => $request->user()->id,
                'movement_type' => 'salida',
                'quantity'      => $data['quantity_used'],
                'movement_date' => now(),
                'notes'         => "Usado en reporte {$report->folio}",
            ]);

            $item->decrement('stock_quantity', $data['quantity_used']);

            $report->partsUsed()->create([
                'item_id'       => $item->id,
                'movement_id'   => $movement->id,
                'quantity_used' => $data['quantity_used'],
                'installed_by'  => $request->user()->id,
                'installed_at'  => now(),
            ]);
        });

        return redirect()->route('reports.orden.show', $report)
            ->with('success', "Refacción \"{$item->name}\" registrada y descontada del inventario.");
    }

    public function storePieza(Request $request, Report $report)
    {
        $orden = $report->ordenTrabajo()->firstOrFail();
        $esExterno = $this->autorizarAccesoExterno($request, $orden);

        $data = $request->validate([
            'accion' => ['required', Rule::in(array_keys(OrdenTrabajoPieza::ACCIONES))],
            'pieza' => ['required', 'string', 'max:255'],
            'notas' => ['nullable', 'string', 'max:1000'],
            'evidencia' => ['required', 'file', 'image', 'max:4096'],
        ]);

        $data['evidencia_path'] = $request->file('evidencia')->store('ordenes-trabajo/piezas', 'public');
        unset($data['evidencia']);
        $data['user_id'] = $request->user()->id;

        $orden->piezas()->create($data);

        $ruta = $esExterno ? 'reports.orden.externo.show' : 'reports.orden.show';

        return redirect()->route($ruta, $report)
            ->with('success', 'Pieza registrada correctamente.');
    }

    public function destroyPieza(Request $request, OrdenTrabajoPieza $pieza)
    {
        $orden = $pieza->ordenTrabajo;
        $report = $orden->report;
        $esExterno = $this->autorizarAccesoExterno($request, $orden);

        // Un mecánico externo solo puede borrar sus propios registros.
        abort_if($esExterno && $pieza->user_id !== $request->user()->id, 403);

        if ($pieza->evidencia_path) {
            Storage::disk('public')->delete($pieza->evidencia_path);
        }
        $pieza->delete();

        $ruta = $esExterno ? 'reports.orden.externo.show' : 'reports.orden.show';

        return redirect()->route($ruta, $report)
            ->with('success', 'Registro eliminado correctamente.');
    }

    public function externoIndex(Request $request)
    {
        $reports = Report::whereHas('ordenTrabajo', fn ($q) => $q->where('proveedor_externo_user_id', $request->user()->id))
            ->with(['bus', 'ordenTrabajo'])
            ->latest('created_at')
            ->paginate(10);

        return view('reports.orden-externo-index', compact('reports'));
    }

    public function externoShow(Request $request, Report $report)
    {
        $orden = $report->ordenTrabajo()->firstOrFail();
        $this->autorizarAccesoExterno($request, $orden);

        $report->load(['bus', 'operador', 'photos', 'videos']);
        $orden->load(['piezas' => fn ($q) => $q->with('user')->latest('id')]);

        return view('reports.orden-externo', compact('report', 'orden'));
    }

    /**
     * Verifica que la orden esté canalizada a taller externo y asignada al usuario autenticado
     * cuando este tiene el rol mecanico_externo. Devuelve true si el usuario es ese mecánico externo.
     */
    private function autorizarAccesoExterno(Request $request, OrdenTrabajo $orden): bool
    {
        if (! $request->user()->hasRole('mecanico_externo')) {
            return false;
        }

        abort_unless(
            $orden->tipo_atencion === 'taller_externo' && $orden->proveedor_externo_user_id === $request->user()->id,
            403,
            'No tienes acceso a esta orden de trabajo.'
        );

        return true;
    }

    public function destroyParte(ReportPartUsed $parte)
    {
        $report = $parte->report;

        DB::transaction(function () use ($parte) {
            $parte->item()->increment('stock_quantity', $parte->quantity_used);
            $parte->movement?->delete();
            $parte->delete();
        });

        return redirect()->route('reports.orden.show', $report)
            ->with('success', 'Refacción eliminada y stock restaurado.');
    }

    private function notificarCambioEstado(Report $report, string $estadoAnterior, string $estadoNuevo, Request $request): void
    {
        $report->loadMissing('operador');

        $destinatarios = collect([$report->operador])
            ->merge(User::role(['superadmin', 'administracion'])->where('is_active', true)->get())
            ->unique('id')
            ->reject(fn($u) => $u->id === $request->user()->id);

        Notification::send($destinatarios, new ReporteEstadoActualizadoNotification($report, $estadoAnterior, $estadoNuevo));
    }

    public function exportPdf(Report $report)
    {
        $orden = $report->ordenTrabajo()->firstOrFail();
        $orden->load(['mecanico', 'piezas.user']);
        $report->load(['bus', 'operador', 'photos', 'videos', 'partsUsed.item']);

        $logoBase64 = base64_encode(file_get_contents(public_path('Logo.png')));

        if ($orden->tipo_atencion === 'taller_externo' && $orden->proveedor_externo) {
            $firmanteNombre = $orden->proveedor_externo;
            $firmanteRol    = 'Encargado de taller externo';
        } else {
            $firmanteNombre = $orden->mecanico->name . ' ' . $orden->mecanico->last_name;
            $firmanteRol    = 'Mecánico responsable';
        }

        $pdf = Pdf::loadView('reports.pdf', [
            'report'         => $report,
            'orden'          => $orden,
            'logoBase64'     => $logoBase64,
            'firmanteNombre' => $firmanteNombre,
            'firmanteRol'    => $firmanteRol,
        ])->setPaper([0, 0, 612, 936]); // Oficio (8.5" x 13")

        return $pdf->download("orden-trabajo-{$report->folio}.pdf");
    }
}
