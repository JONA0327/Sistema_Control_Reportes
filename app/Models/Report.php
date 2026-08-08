<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'bus_id',
        'user_id',
        'km_actual',
        'categorias',
        'urgencia',
        'description',
        'status',
        'created_at',
        'notified_to_agency_at',
        'resolved_at',
    ];

    protected $casts = [
        'categorias'             => 'array',
        'created_at'            => 'datetime',
        'notified_to_agency_at' => 'datetime',
        'resolved_at'           => 'datetime',
    ];

    const CATEGORIAS = [
        'frenos_aire'            => 'Frenos / Aire',
        'motor_manejo'           => 'Motor / Manejo',
        'direccion_suspension'   => 'Dirección / Suspensión',
        'transmision_clutch'     => 'Transmisión / Clutch',
        'llantas'                => 'Llantas',
        'electrico_luces'        => 'Eléctrico / Luces',
        'pasajeros_carroceria'   => 'Pasajeros / Carrocería',
        'fugas'                  => 'Fugas',
    ];

    const URGENCIAS = [
        'verde'    => 'Ruta normal',
        'amarillo' => 'Revisión prioritaria',
        'rojo'     => 'Unidad detenida',
    ];

    public function getFolioAttribute(): string
    {
        return 'RPT-' . str_pad((string) $this->id, 6, '0', STR_PAD_LEFT);
    }

    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }

    public function operador()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function evidences()
    {
        return $this->hasMany(ReportEvidence::class);
    }

    public function photos()
    {
        return $this->hasMany(ReportEvidence::class)->where('evidence_type', 'foto');
    }

    public function ordenTrabajo()
    {
        return $this->hasOne(OrdenTrabajo::class);
    }

    public function partsUsed()
    {
        return $this->hasMany(ReportPartUsed::class);
    }
}
