<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contrato extends Model
{
    protected $table = 'contratos';

    protected $fillable = [
        'cliente_nombre',
        'cliente_telefono',
        'cliente_domicilio',
        'cliente_ciudad',
        'bus_id',
        'num_plazas',
        'fecha_salida',
        'hora_salida',
        'fecha_regreso',
        'hora_regreso',
        'salida',
        'destino',
        'punto_partida_llegada',
        'itinerario',
        'incluye_estacionamiento',
        'costo_viaje',
        'anticipo',
        'notas',
        'lugar_firma',
        'fecha_firma',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'fecha_salida' => 'date',
            'fecha_regreso' => 'date',
            'fecha_firma' => 'date',
            'costo_viaje' => 'decimal:2',
            'anticipo' => 'decimal:2',
            'incluye_estacionamiento' => 'boolean',
        ];
    }

    public function getSalidaDestinoAttribute(): string
    {
        return trim("{$this->salida} → {$this->destino}", ' →');
    }

    public function getFolioAttribute(): string
    {
        return 'CT-'.str_pad((string) $this->id, 5, '0', STR_PAD_LEFT);
    }

    public function getRestoAttribute(): float
    {
        return (float) $this->costo_viaje - (float) $this->anticipo;
    }

    public function getTotalPagadoAttribute(): float
    {
        return (float) $this->anticipo + (float) $this->pagos->sum('monto');
    }

    public function getSaldoPendienteAttribute(): float
    {
        return max(0.0, (float) $this->costo_viaje - $this->total_pagado);
    }

    public function getEstaLiquidadoAttribute(): bool
    {
        return $this->saldo_pendiente <= 0;
    }

    public function bus(): BelongsTo
    {
        return $this->belongsTo(Bus::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(ContratoPago::class);
    }
}
