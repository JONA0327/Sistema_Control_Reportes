<?php

namespace App\Http\Controllers;

use App\Models\ContratoHistorico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContratoHistoricoController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $contratos = ContratoHistorico::with('user')
            ->when($search, fn ($q) => $q
                ->where('folio', 'like', "%{$search}%")
                ->orWhere('cliente_nombre', 'like', "%{$search}%")
                ->orWhere('destino', 'like', "%{$search}%")
            )
            ->orderByDesc('fecha_salida')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('contratos-historicos.index', compact('contratos', 'search'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $data['archivo_path'] = $data['archivo']->store('contratos-historicos', 'public');
        $data['nombre_original'] = $data['archivo']->getClientOriginalName();
        unset($data['archivo']);
        $data['user_id'] = $request->user()->id;

        ContratoHistorico::create($data);

        return redirect()->route('contratos-historicos.index')
            ->with('success', 'Contrato histórico registrado correctamente.');
    }

    public function edit(ContratoHistorico $contrato)
    {
        return view('contratos-historicos.edit', ['contrato' => $contrato]);
    }

    public function update(Request $request, ContratoHistorico $contrato)
    {
        $data = $this->validated($request, archivoRequerido: false);

        if ($request->hasFile('archivo')) {
            Storage::disk('public')->delete($contrato->archivo_path);
            $data['archivo_path'] = $data['archivo']->store('contratos-historicos', 'public');
            $data['nombre_original'] = $data['archivo']->getClientOriginalName();
        }
        unset($data['archivo']);

        $contrato->update($data);

        return redirect()->route('contratos-historicos.index')
            ->with('success', 'Contrato histórico actualizado correctamente.');
    }

    public function destroy(ContratoHistorico $contrato)
    {
        Storage::disk('public')->delete($contrato->archivo_path);
        $contrato->delete();

        return redirect()->route('contratos-historicos.index')
            ->with('success', 'Contrato histórico eliminado correctamente.');
    }

    private function validated(Request $request, bool $archivoRequerido = true): array
    {
        return $request->validate([
            'folio' => ['required', 'string', 'max:255'],
            'cliente_nombre' => ['required', 'string', 'max:255'],
            'destino' => ['nullable', 'string', 'max:255'],
            'fecha_salida' => ['nullable', 'date'],
            'fecha_regreso' => ['nullable', 'date', 'after_or_equal:fecha_salida'],
            'precio_viaje' => ['nullable', 'numeric', 'min:0'],
            'anticipo' => ['nullable', 'numeric', 'min:0'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'archivo' => [$archivoRequerido ? 'required' : 'nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:8192'],
        ]);
    }
}
