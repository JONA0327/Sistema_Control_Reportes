<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Cobros de paquetería de un día (Merlo Web). No va ligado a contratos: entra directo a ingresos_egresos. */
class MerloCortePaqueteria extends Model
{
    protected $table = 'merlo_cortes_paqueteria';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['fecha' => 'date'];
    }

    public function registrarIngreso(int $userId): void
    {
        if ((float) $this->total_amount <= 0) {
            IngresoEgreso::eliminarDesdeOrigen($this);

            return;
        }

        IngresoEgreso::registrarDesdeOrigen($this, [
            'tipo' => 'ingreso',
            'concepto' => "Paquetería Merlo ({$this->fecha->format('d/m/Y')})",
            'descripcion' => "{$this->payments_count} cobro(s) de paquetería",
            'monto' => $this->total_amount,
            'fecha' => $this->fecha,
            'categoria' => 'Paquetería',
            'pais' => 'mexico',
            'user_id' => $userId,
        ]);
        IngresoEgreso::actualizarMontoDesdeOrigen($this, (float) $this->total_amount);
    }
}
