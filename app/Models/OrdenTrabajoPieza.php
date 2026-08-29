<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrdenTrabajoPieza extends Model
{
    protected $table = 'orden_trabajo_piezas';

    const ACCIONES = [
        'reemplazada' => 'Reemplazada',
        'retirada' => 'Retirada',
        'ajustada' => 'Ajustada / Reparada',
    ];

    protected $fillable = [
        'orden_trabajo_id',
        'accion',
        'pieza',
        'notas',
        'evidencia_path',
        'user_id',
    ];

    public function ordenTrabajo(): BelongsTo
    {
        return $this->belongsTo(OrdenTrabajo::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
