<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NuevoReporteNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Report $report)
    {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title'   => "Nuevo reporte {$this->report->folio}",
            'message' => "Bus #{$this->report->bus->num_bus} · " . trim($this->report->operador->name . ' ' . $this->report->operador->last_name),
            'url'     => route('reports.orden.show', $this->report),
        ];
    }
}
