<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\Contrato;
use App\Models\ContratoPago;
use App\Models\IngresoEgreso;
use App\Models\Viaje;
use App\Services\GroqAiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ContratoController extends Controller
{
    public function __construct(private readonly GroqAiService $groqAi)
    {
    }

    public function index(Request $request)
    {
        $search = $request->get('search');

        $contratos = Contrato::with(['bus', 'pagos'])
            ->when($search, fn ($q) => $q
                ->where('cliente_nombre', 'like', "%{$search}%")
                ->orWhere('salida', 'like', "%{$search}%")
                ->orWhere('destino', 'like', "%{$search}%")
                ->orWhere('id', 'like', "%{$search}%")
            )
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('contratos.index', compact('contratos', 'search'));
    }

    public function create()
    {
        $buses = Bus::orderBy('num_bus')->get();

        return view('contratos.create', compact('buses'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['incluye_estacionamiento'] = $request->boolean('incluye_estacionamiento', true);
        $data['user_id'] = $request->user()->id;

        $contrato = Contrato::create($data);
        $this->syncAnticipoIngreso($contrato);
        $this->syncViajePendiente($contrato);

        return redirect()->route('contratos.index')
            ->with('success', "Contrato {$contrato->folio} generado correctamente. Se creó su viaje pendiente.");
    }

    public function edit(Contrato $contrato)
    {
        $buses = Bus::orderBy('num_bus')->get();
        $contrato->load([
            'pagos' => fn ($q) => $q->with('user')->latest('fecha_pago')->latest('id'),
        ]);

        return view('contratos.edit', compact('contrato', 'buses'));
    }

    public function update(Request $request, Contrato $contrato)
    {
        $data = $this->validated($request);
        $data['incluye_estacionamiento'] = $request->boolean('incluye_estacionamiento', true);

        $contrato->update($data);
        $this->syncAnticipoIngreso($contrato);
        $this->syncViajePendiente($contrato);

        return redirect()->route('contratos.index')
            ->with('success', "Contrato {$contrato->folio} actualizado correctamente.");
    }

    public function destroy(Contrato $contrato)
    {
        IngresoEgreso::eliminarDesdeOrigen($contrato);
        foreach ($contrato->pagos as $pago) {
            IngresoEgreso::eliminarDesdeOrigen($pago);
        }

        $viaje = Viaje::where('contrato_id', $contrato->id)->first();
        if ($viaje && $viaje->esta_pendiente) {
            $viaje->delete();
        }

        $contrato->delete();

        return redirect()->route('contratos.index')
            ->with('success', 'Contrato eliminado correctamente.');
    }

    public function storePago(Request $request, Contrato $contrato)
    {
        $data = $request->validate([
            'monto' => ['required', 'numeric', 'min:0.01', 'max:'.max($contrato->saldo_pendiente, 0.01)],
            'fecha_pago' => ['required', 'date'],
            'metodo_pago' => ['nullable', 'string', 'max:255'],
            'notas' => ['nullable', 'string', 'max:255'],
        ], [
            'monto.max' => 'El abono no puede ser mayor al saldo pendiente ($'.number_format($contrato->saldo_pendiente, 2).').',
        ]);

        $pago = $contrato->pagos()->create($data + ['user_id' => $request->user()->id]);

        IngresoEgreso::registrarDesdeOrigen($pago, [
            'tipo' => 'ingreso',
            'concepto' => "Abono contrato {$contrato->folio} - {$contrato->cliente_nombre}",
            'monto' => $pago->monto,
            'fecha' => $pago->fecha_pago,
            'categoria' => 'Contratos',
            'pais' => $this->groqAi->determinarPais($contrato->destino),
            'user_id' => $request->user()->id,
        ]);

        return back()->with('success', 'Abono registrado correctamente.');
    }

    public function destroyPago(ContratoPago $pago)
    {
        IngresoEgreso::eliminarDesdeOrigen($pago);
        $pago->delete();

        return back()->with('success', 'Abono eliminado correctamente.');
    }

    /**
     * Mantiene sincronizado el ingreso automático del anticipo con el contrato.
     */
    private function syncAnticipoIngreso(Contrato $contrato): void
    {
        if ($contrato->anticipo <= 0) {
            IngresoEgreso::eliminarDesdeOrigen($contrato);

            return;
        }

        IngresoEgreso::registrarDesdeOrigen($contrato, [
            'tipo' => 'ingreso',
            'concepto' => "Anticipo contrato {$contrato->folio} - {$contrato->cliente_nombre}",
            'monto' => $contrato->anticipo,
            'fecha' => $contrato->fecha_firma,
            'categoria' => 'Contratos',
            'pais' => $this->groqAi->determinarPais($contrato->destino),
            'user_id' => $contrato->user_id,
        ]);

        IngresoEgreso::actualizarMontoDesdeOrigen($contrato, (float) $contrato->anticipo);
    }

    /**
     * Crea el viaje "pendiente" ligado al contrato, o refresca sus datos derivados
     * (unidad, operador, fechas, costo, origen/destino, itinerario) mientras siga
     * pendiente. Una vez que el viaje se completa desde Viajes, deja de tocarse
     * automáticamente.
     */
    private function syncViajePendiente(Contrato $contrato): void
    {
        $operadorId = $contrato->bus_id ? Bus::find($contrato->bus_id)?->operator_id : null;

        $viaje = Viaje::where('contrato_id', $contrato->id)->first();

        if (! $viaje) {
            Viaje::create([
                'contrato_id' => $contrato->id,
                'no_contrato' => $contrato->folio,
                'bus_id' => $contrato->bus_id,
                'operador_id' => $operadorId,
                'origen' => $contrato->salida,
                'destino' => $contrato->destino,
                'recorridos' => $contrato->itinerario,
                'fecha_salida' => $contrato->fecha_salida,
                'fecha_regreso' => $contrato->fecha_regreso,
                'costo_viaje' => $contrato->costo_viaje,
            ]);

            return;
        }

        if ($viaje->esta_pendiente) {
            $viaje->update([
                'bus_id' => $contrato->bus_id,
                'operador_id' => $operadorId,
                'origen' => $contrato->salida,
                'destino' => $contrato->destino,
                'recorridos' => $contrato->itinerario,
                'fecha_salida' => $contrato->fecha_salida,
                'fecha_regreso' => $contrato->fecha_regreso,
                'costo_viaje' => $contrato->costo_viaje,
            ]);
        }
    }

    public function exportPdf(Contrato $contrato)
    {
        $contrato->load('bus');

        $logoBase64 = base64_encode(file_get_contents(public_path('Logo.png')));
        $autobusBase64 = base64_encode(file_get_contents(public_path('autobus.png')));

        $pdf = Pdf::loadView('contratos.pdf', [
            'contrato' => $contrato,
            'logoBase64' => $logoBase64,
            'autobusBase64' => $autobusBase64,
        ])->setPaper([0, 0, 612, 936]); // Oficio (8.5" x 13")

        return $pdf->stream("contrato-{$contrato->folio}.pdf");
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'cliente_nombre' => ['required', 'string', 'max:255'],
            'cliente_telefono' => ['nullable', 'string', 'max:50'],
            'cliente_domicilio' => ['nullable', 'string', 'max:255'],
            'cliente_ciudad' => ['nullable', 'string', 'max:255'],
            'bus_id' => ['nullable', 'exists:buses,id'],
            'num_plazas' => ['nullable', 'integer', 'min:1'],
            'fecha_salida' => ['required', 'date'],
            'hora_salida' => ['nullable', 'string', 'max:20'],
            'fecha_regreso' => ['required', 'date', 'after_or_equal:fecha_salida'],
            'hora_regreso' => ['nullable', 'string', 'max:20'],
            'salida' => ['required', 'string', 'max:255'],
            'destino' => ['required', 'string', 'max:255'],
            'punto_partida_llegada' => ['nullable', 'string', 'max:255'],
            'itinerario' => ['nullable', 'string', 'max:2000'],
            'costo_viaje' => ['required', 'numeric', 'min:0'],
            'anticipo' => ['required', 'numeric', 'min:0', 'lte:costo_viaje'],
            'notas' => ['nullable', 'string', 'max:1000'],
            'lugar_firma' => ['nullable', 'string', 'max:255'],
            'fecha_firma' => ['required', 'date'],
        ]);
    }
}
