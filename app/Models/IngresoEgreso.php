<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class IngresoEgreso extends Model
{
    protected $table = 'ingresos_egresos';

    const PAISES = [
        'mexico' => 'México',
        'usa' => 'Estados Unidos',
    ];

    protected $fillable = [
        'tipo',
        'concepto',
        'descripcion',
        'monto',
        'fecha',
        'categoria',
        'pais',
        'comprobante_path',
        'user_id',
        'automatico',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'monto' => 'decimal:2',
            'automatico' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function origenable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Crea (una sola vez) un movimiento automático ligado a un modelo origen.
     * Si ya existe un movimiento para ese origen, lo devuelve sin duplicar.
     */
    public static function registrarDesdeOrigen(Model $origen, array $atributos): self
    {
        $existente = static::where('origenable_type', $origen->getMorphClass())
            ->where('origenable_id', $origen->getKey())
            ->first();

        if ($existente) {
            return $existente;
        }

        $movimiento = new static($atributos + ['automatico' => true]);
        $movimiento->origenable()->associate($origen);
        $movimiento->save();

        return $movimiento;
    }

    public static function actualizarMontoDesdeOrigen(Model $origen, float $monto): void
    {
        static::where('origenable_type', $origen->getMorphClass())
            ->where('origenable_id', $origen->getKey())
            ->update(['monto' => $monto]);
    }

    public static function eliminarDesdeOrigen(Model $origen): void
    {
        static::where('origenable_type', $origen->getMorphClass())
            ->where('origenable_id', $origen->getKey())
            ->delete();
    }
}
