<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\IngresoEgreso;
use App\Models\Liquidacion;
use App\Models\LiquidacionGasto;
use App\Models\User;
use App\Models\Viaje;
use App\Services\GroqAiService;
use App\Support\LiquidacionExcelParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ViajeController extends Controller
{
    public function __construct(private readonly GroqAiService $groqAi)
    {
    }

    public function index(Request $request)
    {
        $search = $request->get('search');

        $viajes = Viaje::with(['bus', 'operador', 'liquidacion', 'contrato'])
            ->where(fn ($q) => $q->whereNull('contrato_id')
                ->orWhereHas('contrato', fn ($c) => $c->noHistoricos()))
            ->when($search, fn($q) => $q->where(fn ($w) => $w
                ->where('no_contrato', 'like', "%{$search}%")
                ->orWhere('origen', 'like', "%{$search}%")
                ->orWhere('destino', 'like', "%{$search}%")
            ))
            ->orderByDesc('fecha_salida')
            ->paginate(10)
            ->withQueryString();

        return view('viajes.index', compact('viajes', 'search'));
    }

    public function edit(Viaje $viaje)
    {
        $buses = Bus::where('status', 'activo')->orderBy('num_bus')->get();
        $operadores = User::role('operador')->where('is_active', true)->orderBy('name')->get();
        $viaje->load('contrato');

        return view('viajes.edit', compact('viaje', 'buses', 'operadores'));
    }

    public function update(Request $request, Viaje $viaje)
    {
        $data = $this->validateData($request, $viaje);

        $viaje->update($data);

        Liquidacion::firstOrCreate(['viaje_id' => $viaje->id]);

        $dieselInicial = $viaje->dieselCargas()->where('tipo', 'inicial')->first();

        if ($dieselInicial) {
            $dieselInicial->update([
                'monto'       => $viaje->gasto_diesel_inicio,
                'litros'      => $viaje->litros_diesel_inicio,
                'costo_litro' => $viaje->costo_litro_diesel_inicio,
            ]);
            IngresoEgreso::actualizarMontoDesdeOrigen($dieselInicial, (float) $viaje->gasto_diesel_inicio);
        } else {
            $dieselInicial = $viaje->dieselCargas()->create([
                'tipo'             => 'inicial',
                'monto'            => $viaje->gasto_diesel_inicio,
                'litros'           => $viaje->litros_diesel_inicio,
                'costo_litro'      => $viaje->costo_litro_diesel_inicio,
                'origen'           => 'administracion',
                'estado_solicitud' => 'aprobada',
                'requested_by'     => $request->user()->id,
                'reviewed_by'      => $request->user()->id,
                'reviewed_at'      => now(),
            ]);
            $dieselInicial->registrarEgresoAutomatico($request->user()->id);
        }

        if ($viaje->retiene_movimientos) {
            // Compras sin cerrar: se registra en el cierre del viaje.
        } elseif ($viaje->gastos_entregados > 0) {
            IngresoEgreso::registrarDesdeOrigen($viaje, [
                'tipo' => 'egreso',
                'concepto' => "Gastos entregados - Viaje {$viaje->no_contrato}",
                'monto' => $viaje->gastos_entregados,
                'fecha' => $viaje->fecha_salida,
                'categoria' => 'Gastos de operador',
                'clasificacion' => $viaje->contrato?->tipo,
                'pais' => $viaje->contrato?->pais_ingreso ?? $this->groqAi->determinarPais($viaje->destino),
                'user_id' => $request->user()->id,
            ]);
            IngresoEgreso::actualizarMontoDesdeOrigen($viaje, (float) $viaje->gastos_entregados);
        } else {
            IngresoEgreso::eliminarDesdeOrigen($viaje);
        }

        return redirect()->route('viajes.index')
            ->with('success', "Viaje {$viaje->no_contrato} actualizado correctamente.");
    }

    /**
     * Sube la hoja de liquidación en Excel que el operador ya traía llenada
     * fuera del sistema (ver liquidacion.xlsx). Funciona tanto para activar
     * un viaje pendiente (toma gastos entregados y diésel de salida, que son
     * los únicos datos que faltan — origen/destino/recorridos/costo ya
     * vienen del contrato) como para cerrar su liquidación (km, diésel de
     * llegada, casetas, hospedaje, comida, estacionamiento, lavada, otros).
     * El costo del viaje ya capturado no se sobrescribe con el Excel para no
     * desajustar los ingresos/egresos ya generados desde el contrato; si no
     * coincide, solo se avisa.
     */
    public function importarExcel(Request $request, Viaje $viaje)
    {
        $liquidacion = Liquidacion::firstOrCreate(['viaje_id' => $viaje->id], ['estado' => 'abierta']);

        abort_unless($viaje->operador_id === $request->user()->id || $request->user()->can('gastos.aprobar'), 403);
        abort_if($liquidacion->estado !== 'abierta', 403);

        $request->validate([
            'excel' => ['required', 'file', 'mimes:xlsx,xls', 'max:5120'],
        ]);

        $path = $request->file('excel')->store('liquidacion_excel', 'public');
        $datos = LiquidacionExcelParser::leer(Storage::disk('public')->path($path));
        $userId = $request->user()->id;

        $avisos = [];
        if (! empty($datos['no_contrato']) && $datos['no_contrato'] !== $viaje->no_contrato) {
            $avisos[] = "El Excel es del contrato {$datos['no_contrato']}, pero este viaje es {$viaje->no_contrato}. Verifica que sea el archivo correcto.";
        }

        // Activa el viaje si todavía estaba pendiente: lo único que puede
        // faltarle son estos dos campos (el resto ya viene del contrato).
        $cambiosViaje = array_filter([
            'gastos_entregados' => $datos['gastos_entregados'],
            'gasto_diesel_inicio' => $datos['diesel_viaje_monto'],
            'litros_diesel_inicio' => $datos['diesel_viaje_litros'],
        ], fn ($v) => ! is_null($v));

        if ($cambiosViaje) {
            $viaje->update($cambiosViaje);
        }

        if (! is_null($viaje->gasto_diesel_inicio)) {
            $dieselInicial = $viaje->dieselCargas()->where('tipo', 'inicial')->first();

            if ($dieselInicial) {
                $dieselInicial->update(['monto' => $viaje->gasto_diesel_inicio, 'litros' => $viaje->litros_diesel_inicio]);
                IngresoEgreso::actualizarMontoDesdeOrigen($dieselInicial, (float) $viaje->gasto_diesel_inicio);
            } else {
                $dieselInicial = $viaje->dieselCargas()->create([
                    'tipo' => 'inicial',
                    'monto' => $viaje->gasto_diesel_inicio,
                    'litros' => $viaje->litros_diesel_inicio,
                    'origen' => 'administracion',
                    'estado_solicitud' => 'aprobada',
                    'requested_by' => $userId,
                    'reviewed_by' => $userId,
                    'reviewed_at' => now(),
                ]);
                $dieselInicial->registrarEgresoAutomatico($userId);
            }
        }

        if ($viaje->retiene_movimientos) {
            // Compras sin cerrar: se registra en el cierre del viaje.
        } elseif ($viaje->gastos_entregados > 0) {
            IngresoEgreso::registrarDesdeOrigen($viaje, [
                'tipo' => 'egreso',
                'concepto' => "Gastos entregados - Viaje {$viaje->no_contrato}",
                'monto' => $viaje->gastos_entregados,
                'fecha' => $viaje->fecha_salida,
                'categoria' => 'Gastos de operador',
                'clasificacion' => $viaje->contrato?->tipo,
                'pais' => $viaje->contrato?->pais_ingreso ?? $this->groqAi->determinarPais($viaje->destino),
                'user_id' => $userId,
            ]);
            IngresoEgreso::actualizarMontoDesdeOrigen($viaje, (float) $viaje->gastos_entregados);
        } else {
            IngresoEgreso::eliminarDesdeOrigen($viaje);
        }

        if (! is_null($datos['diesel_llegada_monto']) && $datos['diesel_llegada_monto'] > 0) {
            $extra = $viaje->dieselCargas()->create([
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

        if (! is_null($datos['costo_viaje']) && abs($datos['costo_viaje'] - (float) $viaje->costo_viaje) > 1) {
            $avisos[] = 'El costo del viaje en el Excel ($'.number_format($datos['costo_viaje'], 2)
                .') no coincide con el del contrato ($'.number_format($viaje->costo_viaje, 2).'). No se modificó, revísalo si hace falta.';
        }

        $liquidacion->fill([
            'excel_path' => $path,
            'km_inicial' => $datos['km_inicial'] ?? $liquidacion->km_inicial,
            'km_final'   => $datos['km_final'] ?? $liquidacion->km_final,
        ])->save();

        $sufijoAvisos = $avisos ? ' '.implode(' ', $avisos) : '';

        if ($viaje->esta_pendiente) {
            return back()->withErrors([
                'excel' => 'El Excel no trae gastos entregados y/o diésel de salida. Complétalos a mano para activar el viaje.'.$sufijoAvisos,
            ]);
        }

        if (is_null($liquidacion->km_inicial) || is_null($liquidacion->km_final)) {
            return back()->with('success', 'Viaje activado desde el Excel. Falta el km inicial y/o final para poder cerrar la liquidación.'.$sufijoAvisos);
        }

        $liquidacion->update(['estado' => 'cerrada', 'cerrada_at' => now(), 'cerrada_by' => $userId]);

        return back()->with('success', 'Viaje activado y liquidación importada y cerrada desde Excel.'.$sufijoAvisos);
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
            IngresoEgreso::eliminarDesdeOrigen($carga);
        }

        if ($viaje->liquidacion) {
            foreach ($viaje->liquidacion->gastos as $gasto) {
                if ($gasto->evidencia_path) {
                    Storage::disk('public')->delete($gasto->evidencia_path);
                }
            }
        }

        IngresoEgreso::eliminarDesdeOrigen($viaje);

        $viaje->delete();

        return redirect()->route('viajes.index')
            ->with('success', 'Viaje eliminado correctamente.');
    }

    private function validateData(Request $request, ?Viaje $viaje = null): array
    {
        return $request->validate([
            'no_contrato'          => ['required', 'string', 'max:255'],
            'bus_id'                => ['nullable', 'exists:buses,id'],
            'operador_id'           => ['nullable', 'exists:users,id'],
            'segundo_operador_id'   => ['nullable', 'exists:users,id', 'different:operador_id'],
            'origen'                => ['required', 'string', 'max:255'],
            'destino'               => ['required', 'string', 'max:255'],
            'recorridos'            => ['required', 'string'],
            'fecha_salida'          => ['required', 'date'],
            'fecha_regreso'         => ['required', 'date', 'after_or_equal:fecha_salida'],
            'costo_viaje'           => ['required', 'numeric', 'min:0'],
            'gastos_entregados'     => ['required', 'numeric', 'min:0'],
            'gasto_diesel_inicio'   => ['required', 'numeric', 'min:0'],
            'litros_diesel_inicio'      => ['nullable', 'numeric', 'min:0'],
            'costo_litro_diesel_inicio' => ['nullable', 'numeric', 'min:0'],
        ]);
    }
}
