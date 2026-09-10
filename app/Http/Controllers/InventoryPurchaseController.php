<?php

namespace App\Http\Controllers;

use App\Models\IngresoEgreso;
use App\Models\InventoryItem;
use App\Models\InventoryPurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class InventoryPurchaseController extends Controller
{
    public function index()
    {
        $pendientes = InventoryPurchase::with(['item', 'solicitadoPor'])
            ->where('estado', 'pendiente')
            ->latest('id')
            ->get();

        $resueltas = InventoryPurchase::with(['item', 'solicitadoPor', 'revisadoPor'])
            ->whereIn('estado', ['aprobada', 'rechazada'])
            ->latest('revisado_at')
            ->take(20)
            ->get();

        return view('inventario.compras.index', compact('pendientes', 'resueltas'));
    }

    public function store(Request $request, InventoryItem $item)
    {
        $data = $request->validate([
            'quantity'    => ['required', 'integer', 'min:1'],
            'precio'      => ['required', 'numeric', 'min:0'],
            'comprobante' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
            'notas'       => ['nullable', 'string', 'max:255'],
        ]);

        $item->purchases()->create([
            'quantity'         => $data['quantity'],
            'precio'           => $data['precio'],
            'comprobante_path' => $request->file('comprobante')->store('inventario/comprobantes', 'public'),
            'notas'            => $data['notas'] ?? null,
            'estado'           => 'pendiente',
            'solicitado_por'   => $request->user()->id,
        ]);

        return redirect()->route('inventario.edit', $item)
            ->with('success', 'Compra registrada. Quedó pendiente de validación por administración.');
    }

    public function aprobar(Request $request, InventoryPurchase $compra)
    {
        abort_if($compra->estado !== 'pendiente', 403);

        $data = $request->validate([
            'pais' => ['required', Rule::in(array_keys(IngresoEgreso::PAISES))],
        ]);

        DB::transaction(function () use ($compra, $data, $request) {
            $compra->update([
                'estado'       => 'aprobada',
                'pais'         => $data['pais'],
                'revisado_por' => $request->user()->id,
                'revisado_at'  => now(),
            ]);

            $compra->item()->increment('stock_quantity', $compra->quantity);
            $compra->registrarEgresoAutomatico($request->user()->id);
        });

        return back()->with('success', 'Compra validada: se sumó al stock y se registró en gastos.');
    }

    public function rechazar(Request $request, InventoryPurchase $compra)
    {
        abort_if($compra->estado !== 'pendiente', 403);

        $data = $request->validate([
            'motivo_rechazo' => ['nullable', 'string', 'max:255'],
        ]);

        $compra->update([
            'estado'          => 'rechazada',
            'motivo_rechazo'  => $data['motivo_rechazo'] ?? null,
            'revisado_por'    => $request->user()->id,
            'revisado_at'     => now(),
        ]);

        return back()->with('success', 'Compra rechazada. No se modificó el stock.');
    }
}
