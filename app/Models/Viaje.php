<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Viaje extends Model
{
    protected $fillable = [
        'no_contrato',
        'bus_id',
        'operador_id',
        'origen',
        'destino',
        'recorridos',
        'fecha_salida',
        'fecha_regreso',
        'costo_viaje',
        'gastos_entregados',
        'gasto_diesel_inicio',
    ];

    protected $casts = [
        'fecha_salida' => 'date',
        'fecha_regreso' => 'date',
    ];

    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }

    public function operador()
    {
        return $this->belongsTo(User::class, 'operador_id');
    }

    public function dieselCargas()
    {
        return $this->hasMany(DieselCarga::class);
    }

    public function liquidacion()
    {
        return $this->hasOne(Liquidacion::class);
    }

    public function gananciaEstimada(): float
    {
        return (float) $this->costo_viaje * 0.15;
    }
}
