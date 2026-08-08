<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ReporteEstadoActualizadoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    const ESTADOS = [
        'nuevo'      => 'Nuevo',
        'en_proceso' => 'En proceso',
        'resuelto'   => 'Resuelto',
    ];

    public function __construct(
        public Report $report,
        public string $estadoAnterior,
        public string $estadoNuevo,
    ) {
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
        $puedeVerOrden = method_exists($notifiable, 'hasAnyRole')
            && $notifiable->hasAnyRole(['administrador', 'administracion', 'mecanico']);

        return [
            'title'   => "Reporte {$this->report->folio} actualizado",
            'message' => (self::ESTADOS[$this->estadoAnterior] ?? $this->estadoAnterior)
                . ' → ' . (self::ESTADOS[$this->estadoNuevo] ?? $this->estadoNuevo)
                . " · Bus #{$this->report->bus->num_bus}",
            'url'     => $puedeVerOrden
                ? route('reports.orden.show', $this->report)
                : route('reports.index'),
        ];
    }
}
