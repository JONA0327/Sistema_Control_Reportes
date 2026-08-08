<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiquidacionGasto extends Model
{
    const TIPOS = [
        'caseta' => [
            'label' => 'Caseta',
            'concepto_label' => 'Nombre de la caseta',
        ],
        'hospedaje' => [
            'label' => 'Hospedaje',
            'concepto_label' => 'Nombre del hotel',
        ],
        'comida' => [
            'label' => 'Comida',
            'concepto_label' => null,
        ],
        'estacionamiento' => [
            'label' => 'Estacionamiento',
            'concepto_label' => 'Nombre del estacionamiento',
        ],
        'lavada' => [
            'label' => 'Lavada',
            'concepto_label' => null,
        ],
        'otro' => [
            'label' => 'Otro gasto',
            'concepto_label' => 'Concepto',
        ],
    ];

    protected $fillable = [
        'liquidacion_id',
        'tipo',
        'concepto',
        'monto',
        'evidencia_path',
        'estado',
        'motivo_rechazo',
    ];

    public function liquidacion()
    {
        return $this->belongsTo(Liquidacion::class);
    }

    public function label(): string
    {
        return self::TIPOS[$this->tipo]['label'] ?? $this->tipo;
    }
}
