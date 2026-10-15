<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MerloCorte extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['fecha' => 'date'];
    }

    public function contrato(): BelongsTo
    {
        return $this->belongsTo(Contrato::class);
    }

    /** Ventas + abonos + lo cobrado fuera del sistema: lo que ese viaje dejó. */
    public function getMontoAttribute(): float
    {
        return (float) $this->sold_amount + (float) $this->abonos_amount + (float) $this->external_amount;
    }

    public function getConceptoAttribute(): string
    {
        return "Venta de boletos Merlo {$this->ruta} ({$this->fecha->format('d/m/Y')}) - contrato {$this->contrato->folio}";
    }

    /** Lo manda a ingresos_egresos (idempotente: reenviar solo actualiza el monto). */
    public function registrarIngreso(int $userId): void
    {
        if ($this->monto <= 0) {
            IngresoEgreso::eliminarDesdeOrigen($this);

            return;
        }

        IngresoEgreso::registrarDesdeOrigen($this, [
            'tipo' => 'ingreso',
            'concepto' => $this->concepto,
            'descripcion' => "{$this->sold_seats} vendidos, {$this->late_sold_seats} vendidos después, {$this->free_seats} sobraron de {$this->total_seats}; ventas \${$this->sold_amount} + abonos \${$this->abonos_amount} + fuera del sistema \${$this->external_amount} ({$this->external_seats} asientos)",
            'monto' => $this->monto,
            'fecha' => $this->fecha,
            'categoria' => 'Venta de boletos',
            'clasificacion' => $this->contrato->tipo,
            'pais' => $this->contrato->pais_ingreso,
            'user_id' => $userId,
        ]);
        IngresoEgreso::actualizarMontoDesdeOrigen($this, $this->monto);
    }
}
