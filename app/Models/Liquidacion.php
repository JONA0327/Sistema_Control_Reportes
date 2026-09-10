<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Liquidacion extends Model
{
    protected $table = 'liquidaciones';

    protected $fillable = [
        'viaje_id',
        'km_inicial',
        'km_final',
        'estado',
        'cerrada_at',
        'cerrada_by',
    ];

    protected $casts = [
        'cerrada_at' => 'datetime',
    ];

    public function viaje()
    {
        return $this->belongsTo(Viaje::class);
    }

    public function gastos()
    {
        return $this->hasMany(LiquidacionGasto::class);
    }

    public function cerradaPor()
    {
        return $this->belongsTo(User::class, 'cerrada_by');
    }

    public function kmTotal(): ?float
    {
        if (is_null($this->km_inicial) || is_null($this->km_final)) {
            return null;
        }

        return (float) $this->km_final - (float) $this->km_inicial;
    }

    public function litrosConsumidos(): float
    {
        return (float) $this->viaje->dieselCargas()->whereNotNull('litros')->sum('litros');
    }

    public function rendimientoKmPorLitro(): ?float
    {
        $kmTotal = $this->kmTotal();
        $litros = $this->litrosConsumidos();

        if (is_null($kmTotal) || $litros <= 0) {
            return null;
        }

        return $kmTotal / $litros;
    }

    public function consumoL100km(): ?float
    {
        $kmTotal = $this->kmTotal();
        $litros = $this->litrosConsumidos();

        if (is_null($kmTotal) || $kmTotal <= 0) {
            return null;
        }

        return ($litros / $kmTotal) * 100;
    }

    public function totalGastosRealizados(): float
    {
        $gastosVarios = (float) $this->gastos()->where('estado', '!=', 'rechazado')->sum('monto');

        $dieselDesdeGastos = (float) $this->viaje->dieselCargas()
            ->where('tipo', 'extra')
            ->where('fuente', 'gastos_entregados')
            ->where('estado_solicitud', 'aprobada')
            ->sum('monto');

        return $gastosVarios + $dieselDesdeGastos;
    }

    public function saldo(): float
    {
        return (float) $this->viaje->gastos_entregados - $this->totalGastosRealizados();
    }

    public function sobranteADevolver(): float
    {
        return max(0, $this->saldo());
    }

    public function excesoGasto(): float
    {
        return max(0, -$this->saldo());
    }

    public function resumen(): array
    {
        return [
            'km_total'               => $this->kmTotal(),
            'litros_consumidos'      => $this->litrosConsumidos(),
            'rendimiento_km_l'       => $this->rendimientoKmPorLitro(),
            'consumo_l_100km'        => $this->consumoL100km(),
            'total_gastos_realizados'=> $this->totalGastosRealizados(),
            'saldo'                  => $this->saldo(),
            'sobrante_a_devolver'    => $this->sobranteADevolver(),
            'exceso_gasto'           => $this->excesoGasto(),
            'ganancia_estimada'      => $this->viaje->gananciaEstimada(),
            'ganancia_porcentaje'    => $this->viaje->porcentajeGananciaPorOperador(),
            'doble_operador'         => $this->viaje->doble_operador,
        ];
    }
}
