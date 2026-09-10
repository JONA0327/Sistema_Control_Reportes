<?php

namespace App\Http\Controllers;

use App\Models\IngresoEgreso;
use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use App\Models\InventoryPurchase;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class InventoryItemController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $items = InventoryItem::when($search, fn($q) => $q
                ->where('code', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%")
                ->orWhere('category', 'like', "%{$search}%")
            )
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('inventario.index', compact('items', 'search'));
    }

    public function create()
    {
        return view('inventario.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'            => ['required', 'string', 'max:255', 'unique:inventory_items'],
            'name'            => ['required', 'string', 'max:255'],
            'description'     => ['nullable', 'string', 'max:1000'],
            'category'        => ['required', 'string', 'max:255'],
            'foto'            => ['nullable', 'image', 'max:2048'],
            'stock_quantity'  => ['required', 'integer', 'min:0'],
            'min_stock'       => ['required', 'integer', 'min:0'],
            'origen_registro' => ['required', Rule::in(['nueva_compra', 'existente'])],
            'precio'          => ['required_if:origen_registro,nueva_compra', 'nullable', 'numeric', 'min:0'],
            'comprobante'     => ['required_if:origen_registro,nueva_compra', 'nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ]);

        if ($request->hasFile('foto')) {
            $data['photo_path'] = $request->file('foto')->store('inventario', 'public');
        }
        unset($data['foto']);

        $esNuevaCompra = $data['origen_registro'] === 'nueva_compra';
        $stockInicial  = $data['stock_quantity'];
        $precio        = $data['precio'] ?? null;
        $comprobante   = $request->file('comprobante');

        unset($data['precio'], $data['comprobante'], $data['origen_registro']);

        $data['created_at']    = now();
        $data['stock_quantity'] = $esNuevaCompra ? 0 : $stockInicial;

        $item = InventoryItem::create($data);

        if ($esNuevaCompra) {
            InventoryPurchase::create([
                'item_id'          => $item->id,
                'quantity'         => $stockInicial,
                'precio'           => $precio,
                'comprobante_path' => $comprobante->store('inventario/comprobantes', 'public'),
                'estado'           => 'pendiente',
                'solicitado_por'   => $request->user()->id,
            ]);
        }

        $mensaje = $esNuevaCompra
            ? "Refacción \"{$item->name}\" registrada. La compra quedó pendiente de validación por administración."
            : "Refacción \"{$item->name}\" registrada correctamente.";

        return redirect()->route('inventario.index')->with('success', $mensaje);
    }

    public function edit(InventoryItem $item)
    {
        $movements = $item->movements()->with(['user', 'responsable'])->latest('id')->take(15)->get();
        $purchases = $item->purchases()->with(['solicitadoPor', 'revisadoPor'])->latest('id')->take(15)->get();
        $usuarios = User::where('is_active', true)->orderBy('name')->get();

        return view('inventario.edit', compact('item', 'movements', 'purchases', 'usuarios'));
    }

    public function update(Request $request, InventoryItem $item)
    {
        $data = $request->validate([
            'code'        => ['required', 'string', 'max:255', Rule::unique('inventory_items')->ignore($item->id)],
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'category'    => ['required', 'string', 'max:255'],
            'foto'        => ['nullable', 'image', 'max:2048'],
            'min_stock'   => ['required', 'integer', 'min:0'],
        ]);

        if ($request->hasFile('foto')) {
            if ($item->photo_path) {
                Storage::disk('public')->delete($item->photo_path);
            }
            $data['photo_path'] = $request->file('foto')->store('inventario', 'public');
        }
        unset($data['foto']);

        $item->update($data);

        return redirect()->route('inventario.edit', $item)
            ->with('success', "Refacción actualizada correctamente.");
    }

    public function destroy(InventoryItem $item)
    {
        $tieneUsoEnReportes = DB::table('report_parts_used')->where('item_id', $item->id)->exists();
        $tieneOrdenesCompra = DB::table('purchase_order_details')->where('item_id', $item->id)->exists();

        if ($tieneUsoEnReportes || $tieneOrdenesCompra) {
            return back()->withErrors([
                'delete' => 'No se puede eliminar esta refacción porque está registrada en órdenes de trabajo u órdenes de compra. Puedes seguir editándola, pero no eliminarla.',
            ]);
        }

        if ($item->photo_path) {
            Storage::disk('public')->delete($item->photo_path);
        }
        if ($item->comprobante_path) {
            Storage::disk('public')->delete($item->comprobante_path);
        }
        IngresoEgreso::eliminarDesdeOrigen($item);

        foreach ($item->purchases as $compra) {
            Storage::disk('public')->delete($compra->comprobante_path);
            IngresoEgreso::eliminarDesdeOrigen($compra);
        }

        $item->delete();

        return redirect()->route('inventario.index')
            ->with('success', 'Refacción eliminada correctamente.');
    }

    public function storeMovement(Request $request, InventoryItem $item)
    {
        $data = $request->validate([
            'movement_type'  => ['required', Rule::in(['entrada', 'salida'])],
            'quantity'       => ['required', 'integer', 'min:1'],
            'notes'          => ['nullable', 'string', 'max:255'],
            'responsable_id' => ['nullable', 'exists:users,id'],
        ]);

        if ($data['movement_type'] === 'salida' && $data['quantity'] > $item->stock_quantity) {
            return back()->withErrors(['quantity' => 'No hay suficiente stock disponible para esta salida.'])->withInput();
        }

        if ($data['movement_type'] === 'salida' && $item->is_herramienta && empty($data['responsable_id'])) {
            return back()->withErrors(['responsable_id' => 'Selecciona el usuario responsable de esta herramienta.'])->withInput();
        }

        DB::transaction(function () use ($item, $data, $request) {
            $item->movements()->create([
                'user_id'        => $request->user()->id,
                'movement_type'  => $data['movement_type'],
                'quantity'       => $data['quantity'],
                'movement_date'  => now(),
                'notes'          => $data['notes'] ?? null,
                'responsable_id' => $data['movement_type'] === 'salida' ? (($data['responsable_id'] ?? null) ?: null) : null,
            ]);

            $item->increment(
                'stock_quantity',
                $data['movement_type'] === 'entrada' ? $data['quantity'] : -$data['quantity']
            );
        });

        return redirect()->route('inventario.edit', $item)
            ->with('success', 'Movimiento registrado correctamente.');
    }

    public function marcarDevuelto(InventoryMovement $movimiento)
    {
        abort_if(! $movimiento->pendiente, 403);

        DB::transaction(function () use ($movimiento) {
            $movimiento->update(['devuelto_at' => now()]);
            $movimiento->item()->increment('stock_quantity', $movimiento->quantity);
        });

        return back()->with('success', 'Marcado como devuelto. Se actualizó el stock.');
    }
}
