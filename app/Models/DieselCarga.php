<?php

namespace App\Models;

use App\Services\GroqAiService;
use Illuminate\Database\Eloquent\Model;

class DieselCarga extends Model
{
    protected $fillable = [
        'viaje_id',
        'tipo',
        'monto',
        'fuente',
        'origen',
        'estado_solicitud',
        'estado_evidencia',
        'litros',
        'costo_litro',
        'ticket_path',
        'foto_litros_path',
        'motivo_rechazo',
        'requested_by',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function viaje()
    {
        return $this->belongsTo(Viaje::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function registrarEgresoAutomatico(int $userId): void
    {
        $etiqueta = $this->tipo === 'inicial' ? 'Diésel inicial' : 'Diésel extra';

        IngresoEgreso::registrarDesdeOrigen($this, [
            'tipo' => 'egreso',
            'concepto' => "{$etiqueta} - Viaje {$this->viaje->no_contrato}",
            'monto' => $this->monto,
            'fecha' => $this->reviewed_at ?? now(),
            'categoria' => 'Combustible',
            'pais' => app(GroqAiService::class)->determinarPais($this->viaje->destino),
            'user_id' => $userId,
        ]);
    }
}
