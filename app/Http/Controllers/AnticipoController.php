<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\ContratoAnticipo;
use App\Models\Movimiento;
use App\Support\MoneyNormalizer;
use App\Support\PdfPaperSize;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AnticipoController extends Controller
{
    /**
     * Listado global de anticipos, independiente del módulo de contratos.
     * Se agrupa por contrato (no un renglón por cada anticipo): cada fila
     * es un contrato con al menos un anticipo, y su historial detallado
     * (con evidencia y comprobante PDF de cada uno) se ve al expandirla.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');

        $contratos = Contrato::withCount('anticipos')
            ->withCount(['anticipos as anticipos_activos_count' => fn ($q) => $q->whereNull('cancelado_at')])
            ->withSum(['anticipos as anticipos_activos_sum'  => fn ($q) => $q->whereNull('cancelado_at')], 'monto')
            ->withMax('anticipos', 'fecha_anticipo')
            ->having('anticipos_count', '>', 0)
            ->when($search, fn ($q) => $q
                ->where('cliente_nombre', 'like', "%{$search}%")
                ->orWhereHas('anticipos', fn ($a) => $a->where('folio', 'like', "%{$search}%"))
            )
            ->orderByDesc('anticipos_max_fecha_anticipo')
            ->paginate(15)
            ->withQueryString();

        $contratosParaSelector = Contrato::orderByDesc('id')->get(['id', 'cliente_nombre']);

        return view('anticipos.index', compact('contratos', 'search', 'contratosParaSelector'));
    }

    public function store(Request $request, Contrato $contrato)
    {
        $data = $request->validate([
            'monto' => ['required', 'integer', 'min:1'],
            'fecha_anticipo' => ['required', 'date'],
            'metodo_pago' => ['nullable', 'string', 'max:255'],
            'notas' => ['nullable', 'string', 'max:1000'],
            'evidencia' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,pdf', 'max:5120'],
        ]);

        $data['monto'] = MoneyNormalizer::snapToHundred($data['monto']);
        $data['user_id'] = $request->user()->id;

        if ($request->hasFile('evidencia')) {
            $archivo = $request->file('evidencia');
            $extension = $archivo->getClientOriginalExtension();
            $nombre = pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME);
            $nombreLimpio = \Illuminate\Support\Str::slug($nombre);
            $filename = sprintf(
                'anticipos/%d/%s-%s.%s',
                $contrato->id,
                $nombreLimpio ?: 'evidencia',
                now()->format('Ymd-His'),
                $extension
            );
            $archivo->storeAs(dirname($filename), basename($filename), 'public');
            $data['evidencia_path'] = $filename;
            $data['evidencia_nombre_original'] = $archivo->getClientOriginalName();
        }

        $anticipo = $contrato->anticipos()->create($data);

        Movimiento::registrar(
            'anticipos',
            'creado',
            "Anticipo {$anticipo->folio} registrado por \${$anticipo->monto} en el contrato {$contrato->folio}",
            movible: $anticipo,
        );

        return back()->with('success', "Anticipo {$anticipo->folio} registrado correctamente.");
    }

    public function update(Request $request, Contrato $contrato, ContratoAnticipo $anticipo)
    {
        abort_unless($anticipo->contrato_id === $contrato->id, 404);
        abort_if($anticipo->cancelado, 403, 'No se puede editar un anticipo cancelado.');

        $data = $request->validate([
            'monto' => ['required', 'integer', 'min:1'],
            'fecha_anticipo' => ['required', 'date'],
            'metodo_pago' => ['nullable', 'string', 'max:255'],
            'notas' => ['nullable', 'string', 'max:1000'],
            'evidencia' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,pdf', 'max:5120'],
            'motivo_edicion' => ['required', 'string', 'max:500'],
        ]);

        $data['monto'] = MoneyNormalizer::snapToHundred($data['monto']);
        $data['editado_at'] = now();

        if ($request->hasFile('evidencia')) {
            // Borra la evidencia anterior antes de subir la nueva para no
            // dejar archivos huérfanos en storage/app/public.
            if ($anticipo->evidencia_path) {
                Storage::disk('public')->delete($anticipo->evidencia_path);
            }
            $archivo = $request->file('evidencia');
            $extension = $archivo->getClientOriginalExtension();
            $nombre = pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME);
            $nombreLimpio = \Illuminate\Support\Str::slug($nombre);
            $filename = sprintf(
                'anticipos/%d/%s-%s.%s',
                $contrato->id,
                $nombreLimpio ?: 'evidencia',
                now()->format('Ymd-His'),
                $extension
            );
            $archivo->storeAs(dirname($filename), basename($filename), 'public');
            $data['evidencia_path'] = $filename;
            $data['evidencia_nombre_original'] = $archivo->getClientOriginalName();
        } elseif ($request->boolean('eliminar_evidencia')) {
            if ($anticipo->evidencia_path) {
                Storage::disk('public')->delete($anticipo->evidencia_path);
            }
            $data['evidencia_path'] = null;
            $data['evidencia_nombre_original'] = null;
        }

        $montoAnterior = $anticipo->monto;

        $anticipo->update($data);

        Movimiento::registrar(
            'anticipos',
            'editado',
            "Anticipo {$anticipo->folio} del contrato {$contrato->folio} editado: monto de \${$montoAnterior} a \${$anticipo->monto}",
            motivo: $data['motivo_edicion'],
            movible: $anticipo,
        );

        return back()->with('success', "Anticipo {$anticipo->folio} actualizado correctamente.");
    }

    public function cancelar(Request $request, Contrato $contrato, ContratoAnticipo $anticipo)
    {
        abort_unless($anticipo->contrato_id === $contrato->id, 404);
        abort_if($anticipo->cancelado, 403, 'Este anticipo ya está cancelado.');

        $data = $request->validate([
            'motivo_cancelacion' => ['required', 'string', 'max:500'],
        ]);

        $anticipo->update([
            'cancelado_at' => now(),
            'motivo_cancelacion' => $data['motivo_cancelacion'],
            'cancelado_por' => $request->user()->id,
        ]);

        Movimiento::registrar(
            'anticipos',
            'cancelado',
            "Anticipo {$anticipo->folio} del contrato {$contrato->folio} cancelado",
            motivo: $data['motivo_cancelacion'],
            movible: $anticipo,
        );

        return back()->with('success', "Anticipo {$anticipo->folio} cancelado.");
    }

    /**
     * Comprobante PDF simple, a media carta, para entregar al cliente.
     * Es un recibo del anticipo, no un estado de cuenta: lleva los
     * logos, el monto y los datos mínimos del anticipo, sin el detalle
     * completo del contrato.
     */
    public function comprobantePdf(Contrato $contrato, ContratoAnticipo $anticipo)
    {
        abort_unless($anticipo->contrato_id === $contrato->id, 404);

        $anticipo->load('user');

        $logoBase64 = base64_encode(file_get_contents(public_path('Logo.png')));
        $autobusBase64 = base64_encode(file_get_contents(public_path('autobus.png')));

        $pdf = Pdf::loadView('contratos.anticipos.pdf', [
            'contrato' => $contrato,
            'anticipo' => $anticipo,
            'logoBase64' => $logoBase64,
            'autobusBase64' => $autobusBase64,
        ])->setPaper(...PdfPaperSize::forDompdf('media_carta'));

        return $pdf->stream("comprobante-anticipo-{$anticipo->folio}.pdf");
    }

    public function evidencia(Contrato $contrato, ContratoAnticipo $anticipo)
    {
        abort_unless($anticipo->contrato_id === $contrato->id, 404);

        if (! $anticipo->tieneEvidencia()) {
            abort(404, 'No hay evidencia adjunta para este anticipo.');
        }

        return Storage::disk('public')->response(
            $anticipo->evidencia_path,
            $anticipo->evidencia_nombre_original
        );
    }
}
