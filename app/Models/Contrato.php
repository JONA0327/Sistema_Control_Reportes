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
        return (float) $this->costo_viaje - $this->anticipo_efectivo;
    }

    public function getTotalPagadoAttribute(): float
    {
        return $this->anticipo_efectivo + (float) $this->pagos->sum('monto');
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

    /**
     * Anticipos formalizados del contrato (cada uno con su folio,
     * evidencia y comprobante PDF). La columna `anticipo` del contrato
     * sigue existiendo por compatibilidad con los contratos generados
     * antes de esta tabla; el accessor `anticipo_total` es la suma
     * de los anticipos formalizados.
     */
    public function anticipos(): HasMany
    {
        return $this->hasMany(ContratoAnticipo::class)->latest('fecha_anticipo')->latest('id');
    }

    public function getAnticipoTotalAttribute(): float
    {
        return (float) $this->anticipos->whereNull('cancelado_at')->sum('monto');
    }

    /**
     * Anticipo efectivo que se usa en cálculos y en la UI. Si ya hay
     * anticipos formalizados (no cancelados), su suma es la fuente de
     * verdad. Si no hay formales (contratos legacy), se usa la columna
     * `anticipo` del contrato. Esto evita doble conteo en contratos
     * nuevos (que se crean con anticipo + formal al mismo tiempo).
     */
    public function getAnticipoEfectivoAttribute(): float
    {
        $formalizados = (float) $this->anticipos->whereNull('cancelado_at')->sum('monto');

        return $formalizados > 0 ? $formalizados : (float) $this->anticipo;
    }
}
