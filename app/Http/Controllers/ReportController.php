<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\Report;
use App\Models\ReportEvidence;
use App\Models\User;
use App\Notifications\NuevoReporteNotification;
use App\Services\GroqAiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    public function __construct(private readonly GroqAiService $groqAi)
    {
    }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $reports = Report::with(['bus', 'operador', 'photos'])
            ->when($search, fn($q) => $q
                ->where('description', 'like', "%{$search}%")
                ->orWhereHas('bus', fn($b) => $b->where('num_bus', 'like', "%{$search}%"))
            )
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('reports.index', compact('reports', 'search', 'status'));
    }

    public function create(Request $request)
    {
        $buses = Bus::where('status', 'activo')->orderBy('num_bus')->get();
        $assignedBusId = Bus::where('operator_id', $request->user()->id)->value('id');

        return view('reports.create', compact('buses', 'assignedBusId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'bus_id'        => ['required', 'exists:buses,id'],
            'km_actual'     => ['required', 'integer', 'min:0'],
            'categorias'    => ['required', 'array', 'min:1'],
            'categorias.*'  => [Rule::in(array_keys(Report::CATEGORIAS))],
            'frecuencia'    => ['required', Rule::in(array_keys(Report::FRECUENCIAS))],
            'condiciones'   => ['required', 'array', 'min:1'],
            'condiciones.*' => [Rule::in(array_keys(Report::CONDICIONES))],
            'sintomas'      => ['required', 'array', 'min:1'],
            'sintomas.*'    => [Rule::in(array_keys(Report::SINTOMAS))],
            'urgencia'      => ['required', Rule::in(array_keys(Report::URGENCIAS))],
            'description'   => ['required', 'string', 'max:1000'],
            'fotos'         => ['nullable', 'array', 'max:10'],
            'fotos.*'       => ['image', 'max:4096'],
            'videos'        => ['nullable', 'array', 'max:3'],
            'videos.*'      => ['file', 'mimetypes:video/mp4,video/quicktime,video/webm,video/x-msvideo', 'max:20480'],
        ]);

        $data['user_id']    = $request->user()->id;
        $data['status']     = 'nuevo';
        $data['created_at'] = now();
        $data['description_resumen'] = $this->groqAi->resumirFalla($data['description']);
        unset($data['fotos'], $data['videos']);

        $report = Report::create($data);

        $this->storeEvidencias($request, $report);

        $report->load(['bus', 'operador']);
        $destinatarios = User::role(['mecanico', 'superadmin', 'administracion'])
            ->where('is_active', true)
            ->where('id', '!=', $request->user()->id)
            ->get();
        Notification::send($destinatarios, new NuevoReporteNotification($report));

        return redirect()->route('reports.index')
            ->with('success', "Reporte {$report->folio} creado correctamente.");
    }

    public function edit(Report $report)
    {
        $buses  = Bus::where('status', 'activo')->orderBy('num_bus')->get();
        $photos = $report->photos()->get();
        $videos = $report->videos()->get();
        return view('reports.edit', compact('report', 'buses', 'photos', 'videos'));
    }

    public function update(Request $request, Report $report)
    {
        $canEditStatus = $request->user()->can('reportes.estado');

        $rules = [
            'bus_id'        => ['required', 'exists:buses,id'],
            'km_actual'     => ['required', 'integer', 'min:0'],
            'categorias'    => ['required', 'array', 'min:1'],
            'categorias.*'  => [Rule::in(array_keys(Report::CATEGORIAS))],
            'frecuencia'    => ['nullable', Rule::in(array_keys(Report::FRECUENCIAS))],
            'condiciones'   => ['nullable', 'array'],
            'condiciones.*' => [Rule::in(array_keys(Report::CONDICIONES))],
            'sintomas'      => ['nullable', 'array'],
            'sintomas.*'    => [Rule::in(array_keys(Report::SINTOMAS))],
            'urgencia'      => ['required', Rule::in(array_keys(Report::URGENCIAS))],
            'description'   => ['required', 'string', 'max:1000'],
            'fotos'         => ['nullable', 'array', 'max:10'],
            'fotos.*'       => ['image', 'max:4096'],
            'videos'        => ['nullable', 'array', 'max:3'],
            'videos.*'      => ['file', 'mimetypes:video/mp4,video/quicktime,video/webm,video/x-msvideo', 'max:20480'],
        ];

        if ($canEditStatus) {
            $rules['status'] = ['required', Rule::in(['nuevo', 'en_proceso', 'resuelto'])];
        }

        $data = $request->validate($rules);

        if ($canEditStatus) {
            if ($data['status'] === 'resuelto' && ! $report->resolved_at) {
                $data['resolved_at'] = now();
            } elseif ($data['status'] !== 'resuelto') {
                $data['resolved_at'] = null;
            }
        }

        unset($data['fotos'], $data['videos']);

        if ($data['description'] !== $report->description) {
            $data['description_resumen'] = $this->groqAi->resumirFalla($data['description']);
        }

        $report->update($data);

        $this->storeEvidencias($request, $report);

        return redirect()->route('reports.edit', $report)
            ->with('success', "Reporte actualizado correctamente.");
    }

    public function destroy(Report $report)
    {
        foreach ($report->evidences as $evidencia) {
            Storage::disk('public')->delete($evidencia->evidence_path);
        }
        $report->delete();

        return redirect()->route('reports.index')
            ->with('success', "Reporte eliminado correctamente.");
    }

    public function destroyPhoto(ReportEvidence $evidence)
    {
        Storage::disk('public')->delete($evidence->evidence_path);
        $reportId = $evidence->report_id;
        $esVideo = $evidence->evidence_type === 'video';
        $evidence->delete();

        return redirect()->route('reports.edit', $reportId)
            ->with('success', $esVideo ? 'Video eliminado correctamente.' : 'Foto eliminada correctamente.');
    }

    private function storeEvidencias(Request $request, Report $report): void
    {
        foreach ($request->file('fotos', []) as $foto) {
            ReportEvidence::create([
                'report_id'     => $report->id,
                'evidence_path' => $foto->store('reports', 'public'),
                'evidence_type' => 'foto',
                'uploaded_at'   => now(),
            ]);
        }

        foreach ($request->file('videos', []) as $video) {
            ReportEvidence::create([
                'report_id'     => $report->id,
                'evidence_path' => $video->store('reports/videos', 'public'),
                'evidence_type' => 'video',
                'uploaded_at'   => now(),
            ]);
        }
    }

    public function transcribe(Request $request)
    {
        $request->validate([
            'audio' => ['required', 'file', 'max:25600'],
        ]);

        $file = $request->file('audio');

        $response = Http::withToken(config('services.groq.key'))
            ->attach('file', fopen($file->getRealPath(), 'r'), 'recording.webm')
            ->post('https://api.groq.com/openai/v1/audio/transcriptions', [
                'model'           => 'whisper-large-v3-turbo',
                'language'        => 'es',
                'response_format' => 'json',
            ]);

        if ($response->failed()) {
            return response()->json([
                'error' => 'Error al transcribir: ' . ($response->json('error.message') ?? 'sin respuesta'),
            ], 500);
        }

        return response()->json(['text' => $response->json('text')]);
    }
}
