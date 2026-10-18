<?php

namespace App\Http\Controllers;

use App\Models\Ajuste;
use App\Models\Contrato;
use App\Models\ContratoAnticipo;
use App\Models\ContratoPago;
use App\Models\DieselCarga;
use App\Models\IngresoEgreso;
use App\Models\MerloCorte;
use App\Models\Viaje;
use App\Services\CorteHistorico;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Contratos del sistema que quedan antes del corte contable: no cuentan en
 * ingresos/egresos y aquí se les capturan, con la fecha que corresponda,
 * las liquidaciones y demás gastos que el sistema no registró.
 */
class ContratoHistoricoSistemaController extends Controller
{
    const CATEGORIAS = ['Liquidación', 'Refacciones / piezas', 'Combustible', 'Gastos de operador', 'Anticipo', 'Otro'];

    public function index(Request $request)
    {
        $search = $request->get('search');

        $contratos = Contrato::historicos()
            ->withCount(['movimientosManuales as manuales_count'])
            ->when($search, fn ($q) => $q->where(fn ($w) => $w
                ->where('cliente_nombre', 'like', "%{$search}%")
                ->orWhere('destino', 'like', "%{$search}%")
                ->orWhere('id', 'like', "%{$search}%")))
            ->orderByDesc('fecha_salida')
            ->paginate(15)
            ->withQueryString();

        return view('contratos-historicos.sistema', [
            'contratos' => $contratos,
            'search' => $search,
            'corte' => Ajuste::corteHistorico(),
        ]);
    }

    /** Solo super admin (ver ruta): activa, mueve o apaga el corte. */
    public function updateCorte(Request $request)
    {
        $data = $request->validate(['fecha' => ['nullable', 'date']]);

        Ajuste::guardar(Ajuste::CORTE_HISTORICO, $data['fecha'] ?? null);
        $cambiados = CorteHistorico::aplicar();

        return back()->with('success', ($data['fecha'] ?? null)
            ? "Corte aplicado: contratos con salida hasta el {$data['fecha']} son históricos ({$cambiados} movimientos ajustados)."
            : "Corte desactivado ({$cambiados} movimientos restaurados).");
    }

    public function show(Contrato $contrato)
    {
        abort_unless($contrato->es_historico, 404);

        return view('contratos-historicos.sistema-show', [
            'contrato' => $contrato,
            'excluidos' => $this->movimientosExcluidos($contrato),
            'manuales' => $contrato->movimientosManuales()->orderBy('fecha')->get(),
            'categorias' => self::CATEGORIAS,
        ]);
    }

    public function storeMovimiento(Request $request, Contrato $contrato)
    {
        abort_unless($contrato->es_historico, 404);

        $data = $request->validate([
            'tipo' => ['required', Rule::in(['ingreso', 'egreso'])],
            'categoria' => ['required', Rule::in(self::CATEGORIAS)],
            'concepto' => ['required', 'string', 'max:255'],
            'monto' => ['required', 'numeric', 'min:0.01'],
            'fecha' => ['required', 'date'],
        ]);

        IngresoEgreso::create($data + [
            'contrato_id' => $contrato->id,
            'clasificacion' => $contrato->tipo,
            'pais' => $contrato->pais_ingreso,
            'user_id' => $request->user()->id,
        ]);

        return back()->with('success', 'Movimiento registrado.');
    }

    public function destroyMovimiento(IngresoEgreso $movimiento)
    {
        abort_if(is_null($movimiento->contrato_id) || $movimiento->automatico, 404);

        $movimiento->delete();

        return back()->with('success', 'Movimiento eliminado.');
    }

    /** Lo que el sistema ya había generado para el contrato y quedó fuera de ingresos/egresos. */
    private function movimientosExcluidos(Contrato $contrato)
    {
        $viajeIds = Viaje::where('contrato_id', $contrato->id)->pluck('id');

        $origenes = [
            [Contrato::class, collect([$contrato->id])],
            [ContratoAnticipo::class, $contrato->anticipos()->pluck('id')],
            [ContratoPago::class, $contrato->pagos()->pluck('id')],
            [Viaje::class, $viajeIds],
            [DieselCarga::class, DieselCarga::whereIn('viaje_id', $viajeIds)->pluck('id')],
            [MerloCorte::class, MerloCorte::where('contrato_id', $contrato->id)->pluck('id')],
            [\App\Models\MerloCorteLine::class, \App\Models\MerloCorteLine::whereIn('merlo_corte_id', MerloCorte::where('contrato_id', $contrato->id)->select('id'))->pluck('id')],
        ];

        return IngresoEgreso::withoutGlobalScopes()
            ->where('excluido_historico', true)
            ->where(function ($q) use ($origenes) {
                foreach ($origenes as [$clase, $ids]) {
                    $q->orWhere(fn ($w) => $w->where('origenable_type', (new $clase)->getMorphClass())->whereIn('origenable_id', $ids));
                }
            })
            ->orderBy('fecha')
            ->get();
    }
}
