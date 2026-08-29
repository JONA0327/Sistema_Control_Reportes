<?php

namespace App\Http\Controllers;

use App\Models\IngresoEgreso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class IngresoEgresoController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $tipo = $request->get('tipo');
        $pais = $request->get('pais');

        $movimientos = IngresoEgreso::with('user')
            ->when($search, fn ($q) => $q
                ->where('concepto', 'like', "%{$search}%")
                ->orWhere('categoria', 'like', "%{$search}%")
            )
            ->when($tipo, fn ($q) => $q->where('tipo', $tipo))
            ->when($pais, fn ($q) => $q->where('pais', $pais))
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $resumenPorPais = [];
        foreach (array_keys(IngresoEgreso::PAISES) as $codigoPais) {
            $resumenPorPais[$codigoPais] = [
                'ingresos' => IngresoEgreso::where('tipo', 'ingreso')->where('pais', $codigoPais)->sum('monto'),
                'egresos' => IngresoEgreso::where('tipo', 'egreso')->where('pais', $codigoPais)->sum('monto'),
            ];
        }

        $totalIngresos = IngresoEgreso::where('tipo', 'ingreso')->sum('monto');
        $totalEgresos = IngresoEgreso::where('tipo', 'egreso')->sum('monto');

        return view('ingresos-egresos.index', [
            'movimientos' => $movimientos,
            'search' => $search,
            'tipo' => $tipo,
            'pais' => $pais,
            'resumenPorPais' => $resumenPorPais,
            'totalIngresos' => $totalIngresos,
            'totalEgresos' => $totalEgresos,
            'balance' => $totalIngresos - $totalEgresos,
        ]);
    }

    public function create()
    {
        return view('ingresos-egresos.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        if ($request->hasFile('comprobante')) {
            $data['comprobante_path'] = $request->file('comprobante')->store('ingresos-egresos', 'public');
        }
        unset($data['comprobante']);

        $data['user_id'] = $request->user()->id;

        IngresoEgreso::create($data);

        return redirect()->route('ingresos-egresos.index')
            ->with('success', 'Movimiento registrado correctamente.');
    }

    public function edit(IngresoEgreso $movimiento)
    {
        abort_if($movimiento->automatico, 403, 'Este movimiento se generó automáticamente y solo puede modificarse desde su origen.');

        return view('ingresos-egresos.edit', ['movimiento' => $movimiento]);
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

        return redirect()->route('ingresos-egresos.index')
            ->with('success', 'Movimiento actualizado correctamente.');
    }

    public function destroy(IngresoEgreso $movimiento)
    {
        abort_if($movimiento->automatico, 403, 'Este movimiento se generó automáticamente y solo puede eliminarse desde su origen.');

        if ($movimiento->comprobante_path) {
            Storage::disk('public')->delete($movimiento->comprobante_path);
        }

        $movimiento->delete();

        return redirect()->route('ingresos-egresos.index')
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
