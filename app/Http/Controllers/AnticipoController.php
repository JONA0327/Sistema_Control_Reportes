<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\ContratoAnticipo;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AnticipoController extends Controller
{
    public function store(Request $request, Contrato $contrato)
    {
        $data = $request->validate([
            'monto' => ['required', 'numeric', 'min:0.01'],
            'fecha_anticipo' => ['required', 'date'],
            'metodo_pago' => ['nullable', 'string', 'max:255'],
            'notas' => ['nullable', 'string', 'max:1000'],
            'evidencia' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,pdf', 'max:5120'],
        ]);

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

        return back()->with('success', "Anticipo {$anticipo->folio} registrado correctamente.");
    }

    public function update(Request $request, Contrato $contrato, ContratoAnticipo $anticipo)
    {
        abort_unless($anticipo->contrato_id === $contrato->id, 404);

        $data = $request->validate([
            'monto' => ['required', 'numeric', 'min:0.01'],
            'fecha_anticipo' => ['required', 'date'],
            'metodo_pago' => ['nullable', 'string', 'max:255'],
            'notas' => ['nullable', 'string', 'max:1000'],
            'evidencia' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,pdf', 'max:5120'],
        ]);

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

        $anticipo->update($data);

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

        return back()->with('success', "Anticipo {$anticipo->folio} cancelado.");
    }

    /**
     * Comprobante PDF (media carta) para entregar al cliente. Lleva
     * el logo, los datos del cliente, el detalle del anticipo y, si
     * hay evidencia, una nota de que se adjuntó al sistema.
     */
    public function comprobantePdf(Contrato $contrato, ContratoAnticipo $anticipo)
    {
        abort_unless($anticipo->contrato_id === $contrato->id, 404);

        $contrato->load('bus');
        $anticipo->load('user');

        $logoBase64 = base64_encode(file_get_contents(public_path('Logo.png')));

        $pdf = Pdf::loadView('contratos.anticipos.pdf', [
            'contrato' => $contrato,
            'anticipo' => $anticipo,
            'logoBase64' => $logoBase64,
        ])->setPaper('letter', 'portrait'); // letter ≈ media carta en US/MX

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
