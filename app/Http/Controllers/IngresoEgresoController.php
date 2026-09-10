<?php

namespace App\Http\Controllers;

use App\Models\IngresoEgreso;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class IngresoEgresoController extends Controller
{
    private const DIAS_ES = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
    private const MESES_ES = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

    public function historico(Request $request)
    {
        $pais = $request->get('pais');

        if (! in_array($pais, array_keys(IngresoEgreso::PAISES), true)) {
            return redirect()->route('ingresos-egresos.index');
        }

        $periodo = in_array($request->get('periodo'), ['semana', 'mes', 'anio', 'todos'], true)
            ? $request->get('periodo')
            : 'mes';

        $hoy = Carbon::now();
        $inicio = null;
        $fin = null;
        $semana = null;
        $mes = null;
        $anio = null;
        $etiquetas = collect(); // clave interna => etiqueta visible
        $agrupador = null;

        if ($periodo === 'semana') {
            if ($request->get('semana') && preg_match('/^(\d{4})-W(\d{2})$/', $request->get('semana'), $m)) {
                $inicio = Carbon::now()->setISODate((int) $m[1], (int) $m[2])->startOfWeek();
            } else {
                $inicio = $hoy->copy()->startOfWeek();
            }
            $semana = $inicio->format('o-\WW');
            $fin = $inicio->copy()->endOfWeek();
            $agrupador = fn (Carbon $f) => $f->format('Y-m-d');
            for ($d = $inicio->copy(); $d->lte($fin); $d->addDay()) {
                $etiquetas->put($d->format('Y-m-d'), self::DIAS_ES[$d->dayOfWeekIso - 1].' '.$d->format('d/m'));
            }
        } elseif ($periodo === 'anio') {
            $anio = (int) ($request->get('anio') ?: $hoy->year);
            $inicio = Carbon::create($anio, 1, 1)->startOfDay();
            $fin = Carbon::create($anio, 12, 31)->endOfDay();
            $agrupador = fn (Carbon $f) => $f->format('Y-m');
            for ($mNum = 1; $mNum <= 12; $mNum++) {
                $etiquetas->put(sprintf('%04d-%02d', $anio, $mNum), self::MESES_ES[$mNum - 1]);
            }
        } elseif ($periodo === 'todos') {
            $agrupador = fn (Carbon $f) => $f->format('Y');
            // $etiquetas se arma después, según los años que realmente tengan datos.
        } else { // mes
            if ($request->get('mes') && preg_match('/^(\d{4})-(\d{2})$/', $request->get('mes'))) {
                $inicio = Carbon::createFromFormat('Y-m-d', $request->get('mes').'-01')->startOfMonth();
            } else {
                $inicio = $hoy->copy()->startOfMonth();
            }
            $mes = $inicio->format('Y-m');
            $fin = $inicio->copy()->endOfMonth();
            $agrupador = fn (Carbon $f) => $f->format('Y-m-d');
            for ($d = $inicio->copy(); $d->lte($fin); $d->addDay()) {
                $etiquetas->put($d->format('Y-m-d'), $d->format('d'));
            }
        }

        $query = IngresoEgreso::where('pais', $pais);
        if ($inicio && $fin) {
            $query->whereBetween('fecha', [$inicio->format('Y-m-d'), $fin->format('Y-m-d')]);
        }
        $movimientos = $query->get();

        $ingresosPorGrupo = [];
        $egresosPorGrupo = [];
        foreach ($movimientos as $mov) {
            $clave = $agrupador($mov->fecha);
            if ($mov->tipo === 'ingreso') {
                $ingresosPorGrupo[$clave] = ($ingresosPorGrupo[$clave] ?? 0) + (float) $mov->monto;
            } else {
                $egresosPorGrupo[$clave] = ($egresosPorGrupo[$clave] ?? 0) + (float) $mov->monto;
            }
        }

        if ($periodo === 'todos') {
            $claves = collect(array_keys($ingresosPorGrupo))->merge(array_keys($egresosPorGrupo))->unique()->sort()->values();
            if ($claves->isEmpty()) {
                $claves = collect([$hoy->format('Y')]);
            }
            foreach ($claves as $clave) {
                $etiquetas->put($clave, $clave);
            }
        }

        $chartLabels = $etiquetas->values();
        $chartKeys = $etiquetas->keys();
        $chartIngresos = $chartKeys->map(fn ($k) => round($ingresosPorGrupo[$k] ?? 0, 2))->values();
        $chartEgresos = $chartKeys->map(fn ($k) => round($egresosPorGrupo[$k] ?? 0, 2))->values();

        $categoriasEgresos = $movimientos->where('tipo', 'egreso')
            ->groupBy(fn ($m) => $m->categoria ?: 'Sin categoría')
            ->map(fn ($grupo) => round((float) $grupo->sum('monto'), 2))
            ->sortDesc();

        if ($categoriasEgresos->count() > 8) {
            $top = $categoriasEgresos->take(7);
            $otros = $categoriasEgresos->slice(7)->sum();
            $categoriasEgresos = $top->put('Otros', $otros);
        }

        $totalIngresos = array_sum($ingresosPorGrupo);
        $totalEgresos = array_sum($egresosPorGrupo);

        return view('ingresos-egresos.historico', [
            'pais' => $pais,
            'periodo' => $periodo,
            'semana' => $semana,
            'mes' => $mes,
            'anio' => $anio,
            'chartLabels' => $chartLabels,
            'chartIngresos' => $chartIngresos,
            'chartEgresos' => $chartEgresos,
            'categoriasEgresos' => $categoriasEgresos,
            'totalIngresos' => $totalIngresos,
            'totalEgresos' => $totalEgresos,
            'balance' => $totalIngresos - $totalEgresos,
            'movimientosCount' => $movimientos->count(),
        ]);
    }

    public function index(Request $request)
    {
        $pais = $request->get('pais');

        if (! in_array($pais, array_keys(IngresoEgreso::PAISES), true)) {
            $resumenPorPais = [];
            foreach (array_keys(IngresoEgreso::PAISES) as $codigoPais) {
                $resumenPorPais[$codigoPais] = [
                    'ingresos' => IngresoEgreso::where('tipo', 'ingreso')->where('pais', $codigoPais)->sum('monto'),
                    'egresos' => IngresoEgreso::where('tipo', 'egreso')->where('pais', $codigoPais)->sum('monto'),
                ];
            }

            return view('ingresos-egresos.selector', compact('resumenPorPais'));
        }

        $search = $request->get('search');
        $tipo = $request->get('tipo');

        $movimientos = IngresoEgreso::with('user')
            ->where('pais', $pais)
            ->when($search, fn ($q) => $q
                ->where('concepto', 'like', "%{$search}%")
                ->orWhere('categoria', 'like', "%{$search}%")
            )
            ->when($tipo, fn ($q) => $q->where('tipo', $tipo))
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $totalIngresos = IngresoEgreso::where('pais', $pais)->where('tipo', 'ingreso')->sum('monto');
        $totalEgresos = IngresoEgreso::where('pais', $pais)->where('tipo', 'egreso')->sum('monto');

        return view('ingresos-egresos.index', [
            'movimientos' => $movimientos,
            'search' => $search,
            'tipo' => $tipo,
            'pais' => $pais,
            'totalIngresos' => $totalIngresos,
            'totalEgresos' => $totalEgresos,
            'balance' => $totalIngresos - $totalEgresos,
        ]);
    }

    public function create(Request $request)
    {
        $pais = in_array($request->get('pais'), array_keys(IngresoEgreso::PAISES), true) ? $request->get('pais') : 'mexico';

        return view('ingresos-egresos.create', ['pais' => $pais]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        if ($request->hasFile('comprobante')) {
            $data['comprobante_path'] = $request->file('comprobante')->store('ingresos-egresos', 'public');
        }
        unset($data['comprobante']);

        $data['user_id'] = $request->user()->id;

        $movimiento = IngresoEgreso::create($data);

        return redirect()->route('ingresos-egresos.index', ['pais' => $movimiento->pais])
            ->with('success', 'Movimiento registrado correctamente.');
    }

    public function edit(IngresoEgreso $movimiento)
    {
        abort_if($movimiento->automatico, 403, 'Este movimiento se generó automáticamente y solo puede modificarse desde su origen.');

        return view('ingresos-egresos.edit', ['movimiento' => $movimiento, 'pais' => $movimiento->pais]);
    }

    public function update(Request $request, IngresoEgreso $movimiento)
    {
        abort_if($movimiento->automatico, 403, 'Este movimiento se generó automáticamente y solo puede modificarse desde su origen.');

        $data = $this->validated($request);

        if ($request->hasFile('comprobante')) {
            if ($movimiento->comprobante_path) {
                Storage::disk('public')->delete($movimiento->comprobante_path);
            }
            $data['comprobante_path'] = $request->file('comprobante')->store('ingresos-egresos', 'public');
        }
        unset($data['comprobante']);

        $movimiento->update($data);

        return redirect()->route('ingresos-egresos.index', ['pais' => $movimiento->pais])
            ->with('success', 'Movimiento actualizado correctamente.');
    }

    public function destroy(IngresoEgreso $movimiento)
    {
        abort_if($movimiento->automatico, 403, 'Este movimiento se generó automáticamente y solo puede eliminarse desde su origen.');

        if ($movimiento->comprobante_path) {
            Storage::disk('public')->delete($movimiento->comprobante_path);
        }

        $pais = $movimiento->pais;
        $movimiento->delete();

        return redirect()->route('ingresos-egresos.index', ['pais' => $pais])
            ->with('success', 'Movimiento eliminado correctamente.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'tipo' => ['required', Rule::in(['ingreso', 'egreso'])],
            'concepto' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'monto' => ['required', 'numeric', 'min:0.01'],
            'fecha' => ['required', 'date'],
            'categoria' => ['nullable', 'string', 'max:255'],
            'pais' => ['nullable', Rule::in(array_keys(\App\Models\IngresoEgreso::PAISES))],
            'comprobante' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ]);
    }
}
