<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Viaje extends Model
{
    protected $fillable = [
        'contrato_id',
        'no_contrato',
        'bus_id',
        'operador_id',
        'segundo_operador_id',
        'origen',
        'destino',
        'recorridos',
        'fecha_salida',
        'fecha_regreso',
        'costo_viaje',
        'gastos_entregados',
        'gasto_diesel_inicio',
        'litros_diesel_inicio',
        'costo_litro_diesel_inicio',
    ];

    protected $casts = [
        'fecha_salida' => 'date',
        'fecha_regreso' => 'date',
    ];

    public function getEstaPendienteAttribute(): bool
    {
        return is_null($this->operador_id)
            || is_null($this->origen)
            || is_null($this->destino)
            || is_null($this->recorridos)
            || is_null($this->gastos_entregados)
            || is_null($this->gasto_diesel_inicio);
    }

    public function contrato()
    {
        return $this->belongsTo(Contrato::class);
    }

    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }

    public function operador()
    {
        return $this->belongsTo(User::class, 'operador_id');
    }

    public function segundoOperador()
    {
        return $this->belongsTo(User::class, 'segundo_operador_id');
    }

    public function dieselCargas()
    {
        return $this->hasMany(DieselCarga::class);
    }

    public function liquidacion()
    {
        return $this->hasOne(Liquidacion::class);
    }

    public function getDobleOperadorAttribute(): bool
    {
        return ! is_null($this->segundo_operador_id);
    }

    /**
     * Porcentaje del costo del viaje que gana CADA operador. Con un solo
     * operador se lleva el 15%; si va acompañado de un segundo operador,
     * cada uno gana 10% (20% entre los dos, en vez del 15% de uno solo).
     */
    public function porcentajeGananciaPorOperador(): float
    {
        return $this->doble_operador ? 0.10 : 0.15;
    }

    /**
     * Ganancia total estimada del viaje (la suma de lo que gana cada
     * operador). Los gastos/liquidación del viaje se administran desde
     * la cuenta del operador titular; el segundo operador solo puede
     * consultarlos.
     */
    public function gananciaEstimada(): float
    {
        $operadores = $this->doble_operador ? 2 : 1;

        return (float) $this->costo_viaje * $this->porcentajeGananciaPorOperador() * $operadores;
    }
}
