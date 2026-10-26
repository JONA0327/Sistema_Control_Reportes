<?php

namespace App\Http\Controllers;

use App\Models\DieselCarga;
use App\Models\IngresoEgreso;
use App\Models\Liquidacion;
use App\Models\LiquidacionGasto;
use App\Support\LiquidacionExcelParser;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class LiquidacionController extends Controller
{
    /** El operador del viaje, o administración (gastos.aprobar) capturando por él cuando aún no lo hace. */
    private function puedeCapturar(Request $request, Liquidacion $liquidacion): bool
    {
        return $liquidacion->viaje->operador_id === $request->user()->id || $request->user()->can('gastos.aprobar');
    }

    public function updateKm(Request $request, Liquidacion $liquidacion)
    {
        abort_unless($this->puedeCapturar($request, $liquidacion), 403);
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
        abort_unless($this->puedeCapturar($request, $liquidacion), 403);
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
            // Lo que captura administración ya viene validado; el del operador espera su aceptación.
            'estado' => $liquidacion->viaje->operador_id === $request->user()->id ? 'pendiente' : 'aceptado',
            'capturado_por' => $liquidacion->viaje->operador_id === $request->user()->id ? null : $request->user()->id,
        ]);

        return back()->with('success', 'Gasto registrado.');
    }

    public function destroyGasto(Request $request, LiquidacionGasto $liquidacionGasto)
    {
        $liquidacion = $liquidacionGasto->liquidacion;

        abort_unless($this->puedeCapturar($request, $liquidacion), 403);
        abort_if($liquidacion->estado !== 'abierta', 403);

        if ($liquidacionGasto->evidencia_path) {
            Storage::disk('public')->delete($liquidacionGasto->evidencia_path);
        }

        $liquidacionGasto->delete();

        return back()->with('success', 'Gasto eliminado.');
    }

    public function cerrar(Request $request, Liquidacion $liquidacion)
    {
        abort_unless($this->puedeCapturar($request, $liquidacion), 403);
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

    /**
     * Importa la hoja de liquidación en Excel que el operador ya traía
     * llenada fuera del sistema (ver liquidacion.xlsx). Llena km, diésel y
     * los gastos del viaje a partir del Excel, guarda el archivo como
     * respaldo, y cierra la liquidación si quedó todo lo necesario (km
     * inicial y final). El costo del viaje y los gastos entregados que ya
     * estaban capturados en el sistema no se sobrescriben con el Excel para
     * no desajustar los ingresos/egresos ya generados desde el contrato;
     * si no coinciden se avisa, nada más.
     */
    public function importarExcel(Request $request, Liquidacion $liquidacion)
    {
        abort_unless($this->puedeCapturar($request, $liquidacion), 403);
        abort_if($liquidacion->estado !== 'abierta', 403);

        $request->validate([
            'excel' => ['required', 'file', 'mimes:xlsx,xls', 'max:5120'],
        ]);

        $path = $request->file('excel')->store('liquidacion_excel', 'public');
        $datos = LiquidacionExcelParser::leer(Storage::disk('public')->path($path));

        $viaje = $liquidacion->viaje;
        $userId = $request->user()->id;

        $liquidacion->fill([
            'excel_path' => $path,
            'km_inicial' => $datos['km_inicial'] ?? $liquidacion->km_inicial,
            'km_final'   => $datos['km_final'] ?? $liquidacion->km_final,
        ])->save();

        if (! is_null($datos['diesel_viaje_monto']) && $datos['diesel_viaje_monto'] > 0) {
            $inicial = $viaje->dieselCargas()->where('tipo', 'inicial')->first();

            if ($inicial) {
                $inicial->update(['monto' => $datos['diesel_viaje_monto'], 'litros' => $datos['diesel_viaje_litros']]);
                IngresoEgreso::actualizarMontoDesdeOrigen($inicial, (float) $datos['diesel_viaje_monto']);
            } else {
                $inicial = DieselCarga::create([
                    'viaje_id' => $viaje->id,
                    'tipo' => 'inicial',
                    'monto' => $datos['diesel_viaje_monto'],
                    'litros' => $datos['diesel_viaje_litros'],
                    'origen' => 'administracion',
                    'estado_solicitud' => 'aprobada',
                    'requested_by' => $userId,
                    'reviewed_by' => $userId,
                    'reviewed_at' => now(),
                ]);
                $inicial->registrarEgresoAutomatico($userId);
            }
        }

        if (! is_null($datos['diesel_llegada_monto']) && $datos['diesel_llegada_monto'] > 0) {
            $extra = DieselCarga::create([
                'viaje_id' => $viaje->id,
                'tipo' => 'extra',
                'monto' => $datos['diesel_llegada_monto'],
                'litros' => $datos['diesel_llegada_litros'],
                'origen' => 'administracion',
                'estado_solicitud' => 'aprobada',
                'requested_by' => $userId,
                'reviewed_by' => $userId,
                'reviewed_at' => now(),
            ]);
            $extra->registrarEgresoAutomatico($userId);
        }

        if (! is_null($datos['gastos_entregados']) && round((float) $datos['gastos_entregados'], 2) !== round((float) $viaje->gastos_entregados, 2)) {
            $viaje->update(['gastos_entregados' => $datos['gastos_entregados']]);
            IngresoEgreso::actualizarMontoDesdeOrigen($viaje, (float) $viaje->gastos_entregados);
        }

        $mapaGastos = [
            'casetas' => 'caseta',
            'hospedaje' => 'hospedaje',
            'comida' => 'comida',
            'estacionamiento' => 'estacionamiento',
            'lavada' => 'lavada',
            'otros_gastos' => 'otro',
        ];
        foreach ($mapaGastos as $campo => $tipo) {
            if (! empty($datos[$campo]) && $datos[$campo] > 0) {
                LiquidacionGasto::create([
                    'liquidacion_id' => $liquidacion->id,
                    'tipo' => $tipo,
                    'concepto' => is_null(LiquidacionGasto::TIPOS[$tipo]['concepto_label']) ? null : 'Importado de Excel',
                    'monto' => $datos[$campo],
                    'estado' => 'aceptado',
                    'capturado_por' => $userId,
                ]);
            }
        }

        $aviso = null;
        if (! is_null($datos['costo_viaje']) && abs($datos['costo_viaje'] - (float) $viaje->costo_viaje) > 1) {
            $aviso = 'El costo del viaje en el Excel ($'.number_format($datos['costo_viaje'], 2)
                .') no coincide con el del contrato ($'.number_format($viaje->costo_viaje, 2).'). No se modificó, revísalo si hace falta.';
        }

        if (is_null($liquidacion->km_inicial) || is_null($liquidacion->km_final)) {
            return back()->withErrors([
                'km' => 'El Excel no trae km inicial y/o final. El resto de la información ya se importó; agrega el km manualmente para poder cerrar la liquidación.'
                    .($aviso ? ' '.$aviso : ''),
            ]);
        }

        $liquidacion->update(['estado' => 'cerrada', 'cerrada_at' => now(), 'cerrada_by' => $userId]);

        return back()->with('success', 'Liquidación importada desde Excel y cerrada correctamente.'.($aviso ? ' '.$aviso : ''));
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

    public function exportPdf(Liquidacion $liquidacion)
    {
        abort_if($liquidacion->estado !== 'cerrada', 403, 'Solo se puede exportar la hoja de una liquidación cerrada.');

        $liquidacion->load(['viaje.bus', 'viaje.operador', 'viaje.segundoOperador', 'gastos', 'cerradaPor']);

        $viaje = $liquidacion->viaje;
        $d = $liquidacion->desglose();

        $logoBase64 = base64_encode(file_get_contents(public_path('Logo.png')));

        $pdf = Pdf::loadView('liquidacion.pdf', [
            'liquidacion' => $liquidacion,
            'viaje'       => $viaje,
            'logoBase64'  => $logoBase64,
        ] + $d)->setPaper('letter', 'portrait');

        return $pdf->stream("liquidacion-{$viaje->no_contrato}.pdf");
    }
}
