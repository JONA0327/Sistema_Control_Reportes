<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class BusController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $buses = Bus::with(['operator', 'copiloto'])
            ->withCount('pendingLoans')
            ->when($search, fn($q) => $q
                ->where('num_bus', 'like', "%{$search}%")
                ->orWhere('placa', 'like', "%{$search}%")
                ->orWhere('status', 'like', "%{$search}%")
            )
            ->orderBy('num_bus')
            ->paginate(10)
            ->withQueryString();

        return view('buses.index', compact('buses', 'search'));
    }

    public function create()
    {
        $operadores = User::role('operador')->where('is_active', true)->orderBy('name')->get();
        return view('buses.create', compact('operadores'));
    }

    public function show(Bus $bus)
    {
        $bus->load(['operator', 'copiloto']);
        $prestamos = $bus->pendingLoans()->with('item')->orderByDesc('movement_date')->get();

        return view('buses.show', compact('bus', 'prestamos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'num_bus'      => ['required', 'integer', 'unique:buses'],
            'placa'        => ['required', 'string', 'max:20', 'unique:buses'],
            'num_asientos' => ['nullable', 'integer', 'min:1', 'max:100'],
            'status'       => ['required', 'string', Rule::in(['activo', 'inactivo', 'mantenimiento'])],
            'operator_id'  => ['nullable', 'exists:users,id'],
            'copiloto_id'  => ['nullable', 'exists:users,id', 'different:operator_id'],
            'foto'         => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('buses', 'public');
        }

        Bus::create($data);

        return redirect()->route('buses.index')
            ->with('success', "Unidad #{$data['num_bus']} registrada correctamente.");
    }

    public function edit(Bus $bus)
    {
        $operadores = User::role('operador')->where('is_active', true)->orderBy('name')->get();
        return view('buses.edit', compact('bus', 'operadores'));
    }

    public function update(Request $request, Bus $bus)
    {
        $data = $request->validate([
            'num_bus'      => ['required', 'integer', Rule::unique('buses')->ignore($bus->id)],
            'placa'        => ['required', 'string', 'max:20', Rule::unique('buses')->ignore($bus->id)],
            'num_asientos' => ['nullable', 'integer', 'min:1', 'max:100'],
            'status'       => ['required', 'string', Rule::in(['activo', 'inactivo', 'mantenimiento'])],
            'operator_id'  => ['nullable', 'exists:users,id'],
            'copiloto_id'  => ['nullable', 'exists:users,id', 'different:operator_id'],
            'foto'         => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('foto')) {
            if ($bus->foto) {
                Storage::disk('public')->delete($bus->foto);
            }
            $data['foto'] = $request->file('foto')->store('buses', 'public');
        } else {
            unset($data['foto']);
        }

        $bus->update($data);

        return redirect()->route('buses.index')
            ->with('success', "Unidad #{$bus->num_bus} actualizada correctamente.");
    }

    public function destroy(Bus $bus)
    {
        if ($bus->foto) {
            Storage::disk('public')->delete($bus->foto);
        }

        $bus->delete();

        return redirect()->route('buses.index')
            ->with('success', "Unidad eliminada correctamente.");
    }
}
