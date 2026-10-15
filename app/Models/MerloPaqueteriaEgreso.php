<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Egreso del día de paquetería (lo que se dio de lo recaudado en Merlo Web). Empata con su ingreso del mismo día. */
class MerloPaqueteriaEgreso extends Model
{
    protected $table = 'merlo_paqueteria_egresos';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['fecha' => 'date', 'monto' => 'decimal:2'];
    }

    public function registrarEgreso(int $userId): void
    {
        IngresoEgreso::registrarDesdeOrigen($this, [
            'tipo' => 'egreso',
            'concepto' => "Paquetería Merlo ({$this->fecha->format('d/m/Y')}): {$this->concepto}",
            'monto' => $this->monto,
            'fecha' => $this->fecha,
            'categoria' => 'Paquetería',
            'pais' => 'mexico',
            'user_id' => $userId,
        ]);
        IngresoEgreso::actualizarMontoDesdeOrigen($this, (float) $this->monto);
    }
}
