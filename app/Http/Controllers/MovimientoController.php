<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use Illuminate\Http\Request;

class MovimientoController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $modulo = $request->get('modulo');
        $accion = $request->get('accion');

        $movimientos = Movimiento::with('user')
            ->when($search, fn ($q) => $q->where('descripcion', 'like', "%{$search}%"))
            ->when($modulo, fn ($q) => $q->where('modulo', $modulo))
            ->when($accion, fn ($q) => $q->where('accion', $accion))
            ->latest('created_at')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('movimientos.index', compact('movimientos', 'search', 'modulo', 'accion'));
    }
}
