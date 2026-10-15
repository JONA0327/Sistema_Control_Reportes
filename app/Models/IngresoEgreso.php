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
        'clasificacion',
        'pais',
        'comprobante_path',
        'user_id',
        'automatico',
        'contrato_id',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'monto' => 'decimal:2',
            'automatico' => 'boolean',
            'excluido_historico' => 'boolean',
        ];
    }

    /** Los movimientos de contratos antes del corte contable no cuentan en ningún total. */
    protected static function booted(): void
    {
        static::addGlobalScope('visibles', fn ($q) => $q->where('ingresos_egresos.excluido_historico', false));
    }

    /** Contrato al que pertenece el movimiento automático de ese origen (null si no aplica). */
    public static function contratoDeOrigen(?Model $origen): ?Contrato
    {
        return match (true) {
            $origen instanceof Contrato => $origen,
            $origen instanceof ContratoAnticipo, $origen instanceof ContratoPago,
            $origen instanceof MerloCorte, $origen instanceof Viaje => $origen->contrato,
            $origen instanceof DieselCarga => $origen->viaje?->contrato,
            default => null,
        };
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
        $existente = static::withoutGlobalScopes()->where('origenable_type', $origen->getMorphClass())
            ->where('origenable_id', $origen->getKey())
            ->first();

        if ($existente) {
            return $existente;
        }

        $movimiento = new static($atributos + ['automatico' => true]);
        $movimiento->origenable()->associate($origen);
        $movimiento->excluido_historico = (bool) static::contratoDeOrigen($origen)?->es_historico;
        $movimiento->save();

        return $movimiento;
    }

    public static function actualizarMontoDesdeOrigen(Model $origen, float $monto): void
    {
        static::withoutGlobalScopes()->where('origenable_type', $origen->getMorphClass())
            ->where('origenable_id', $origen->getKey())
            ->update(['monto' => $monto]);
    }

    public static function actualizarClasificacionDesdeOrigen(Model $origen, ?string $clasificacion, string $pais): void
    {
        static::withoutGlobalScopes()->where('origenable_type', $origen->getMorphClass())
            ->where('origenable_id', $origen->getKey())
            ->update(['clasificacion' => $clasificacion, 'pais' => $pais]);
    }

    public static function actualizarTipoDesdeOrigen(Model $origen, string $tipo): void
    {
        static::withoutGlobalScopes()->where('origenable_type', $origen->getMorphClass())
            ->where('origenable_id', $origen->getKey())
            ->update(['tipo' => $tipo]);
    }

    public static function eliminarDesdeOrigen(Model $origen): void
    {
        static::withoutGlobalScopes()->where('origenable_type', $origen->getMorphClass())
            ->where('origenable_id', $origen->getKey())
            ->delete();
    }
}
