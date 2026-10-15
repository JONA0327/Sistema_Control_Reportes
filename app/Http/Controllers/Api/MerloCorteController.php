<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contrato;
use App\Models\MerloCorte;
use App\Models\MerloCortePaqueteria;
use App\Models\Viaje;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MerloCorteController extends Controller
{
    /** Corte diario de paquetería: sin contrato, entra directo como ingreso. */
    public function storePaqueteria(Request $request): JsonResponse
    {
        $d = $request->validate([
            'fecha' => ['required', 'date'],
            'payments_count' => ['required', 'integer', 'min:0'],
            'total_amount' => ['required', 'numeric', 'min:0'],
        ]);

        $userId = (int) config('services.merlo.user_id');
        if (! $userId) {
            return response()->json(['message' => 'Falta MERLO_API_USER_ID en .env de Control.'], 500);
        }

        $corte = DB::transaction(function () use ($d, $userId) {
            $corte = MerloCortePaqueteria::updateOrCreate(['fecha' => $d['fecha']], $d);
            $corte->registrarIngreso($userId);

            return $corte;
        });

        return response()->json(['data' => ['id' => $corte->id]], 201);
    }

    /** Contratos tipo "compras" a los que Merlo puede ligar sus abonos. */
    public function contratosCompras(): JsonResponse
    {
        $data = Contrato::where('tipo', 'compras')->orderByDesc('id')->get(['id', 'cliente_nombre'])
            ->map(fn (Contrato $c) => ['id' => $c->id, 'folio' => $c->folio, 'cliente_nombre' => $c->cliente_nombre]);

        return response()->json(['data' => $data]);
    }

    /**
     * Recibe el corte de un viaje (un día). Genera:
     * Guarda el corte ligado a un contrato de compras. Su ingreso (ventas +
     * abonos, fechado el día del viaje) entra a ingresos_egresos al cerrar
     * el viaje en Control (Viaje::cerrar()).
     */
    public function store(Request $request): JsonResponse
    {
        $d = $request->validate([
            'trip_id' => ['required', 'integer'],
            'fecha' => ['required', 'date'],
            'ruta' => ['required', 'string', 'max:255'],
            'total_seats' => ['required', 'integer', 'min:0'],
            'sold_seats' => ['required', 'integer', 'min:0'],
            'late_sold_seats' => ['required', 'integer', 'min:0'],
            'apartado_seats' => ['required', 'integer', 'min:0'],
            'free_seats' => ['required', 'integer', 'min:0'],
            'sold_amount' => ['required', 'numeric', 'min:0'],
            'late_amount' => ['required', 'numeric', 'min:0'],
            'abonos_amount' => ['required', 'numeric', 'min:0'],
            'total_cost' => ['required', 'numeric', 'min:0'],
            'external_seats' => ['nullable', 'integer', 'min:0'],
            'external_amount' => ['nullable', 'numeric', 'min:0'],
            'contrato_id' => ['required', 'integer', Rule::exists('contratos', 'id')->where('tipo', 'compras')],
        ]);

        $userId = (int) config('services.merlo.user_id');
        if (! $userId) {
            return response()->json(['message' => 'Falta MERLO_API_USER_ID en .env de Control.'], 500);
        }

        $corte = DB::transaction(function () use ($d, $userId) {
            $d += ['external_seats' => 0, 'external_amount' => 0];
            $d['external_seats'] ??= 0;
            $d['external_amount'] ??= 0;
            $corte = MerloCorte::updateOrCreate(['trip_id' => $d['trip_id']], $d);

            // El ingreso entra al cierre del viaje; si ya se cerró, entra de inmediato.
            if (Viaje::where('contrato_id', $corte->contrato_id)->whereNotNull('cerrado_at')->exists()) {
                $corte->registrarIngreso($userId);
            }

            return $corte;
        });

        return response()->json(['data' => ['id' => $corte->id]], 201);
    }
}
