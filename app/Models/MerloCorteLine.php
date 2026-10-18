<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Renglón del corte de un viaje de Merlo: gasto u otro ingreso del día. */
class MerloCorteLine extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['cuenta' => 'boolean', 'monto' => 'decimal:2', 'monto_unitario' => 'decimal:2', 'cantidad' => 'decimal:2'];
    }

    public function corte(): BelongsTo
    {
        return $this->belongsTo(MerloCorte::class, 'merlo_corte_id');
    }

    public function esGasto(): bool
    {
        return $this->kind === 'gasto';
    }

    /** Lo manda (o lo quita, si ya no cuenta) a ingresos_egresos. Idempotente. */
    public function sincronizarMovimiento(int $userId): void
    {
        if (! $this->cuenta) {
            IngresoEgreso::eliminarDesdeOrigen($this);

            return;
        }

        $corte = $this->corte()->with('contrato')->first();
        $tipo = $this->esGasto() ? 'egreso' : 'ingreso';
        $detalle = ((float) $this->cantidad !== 1.0 ? "{$this->cantidad} × \${$this->monto_unitario}" : null);

        IngresoEgreso::registrarDesdeOrigen($this, [
            'tipo' => $tipo,
            'concepto' => "Corte Merlo {$corte->ruta} ({$corte->fecha->format('d/m/Y')}): {$this->concepto}",
            'descripcion' => trim(($detalle ? $detalle.'. ' : '').($this->nota ?? '')) ?: null,
            'monto' => $this->monto,
            'fecha' => $corte->fecha,
            'categoria' => 'Corte de viaje',
            'clasificacion' => $corte->contrato->tipo,
            'pais' => $corte->contrato->pais_ingreso,
            'user_id' => $userId,
        ]);
        IngresoEgreso::actualizarMontoDesdeOrigen($this, (float) $this->monto);
        IngresoEgreso::actualizarTipoDesdeOrigen($this, $tipo);
    }
}
