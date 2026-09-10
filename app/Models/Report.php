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
        'frecuencia',
        'condiciones',
        'sintomas',
        'description',
        'description_resumen',
        'status',
        'created_at',
        'notified_to_agency_at',
        'resolved_at',
    ];

    protected $casts = [
        'categorias'             => 'array',
        'condiciones'            => 'array',
        'sintomas'               => 'array',
        'created_at'            => 'datetime',
        'notified_to_agency_at' => 'datetime',
        'resolved_at'           => 'datetime',
    ];

    const CATEGORIAS = [
        'frenos_aire'            => 'Frenos / Aire (Secador, Válvulas, Balatas, Cámaras)',
        'motor_manejo'           => 'Motor / Manejo (Inyección, Turbo, Enfriamiento, Admisión)',
        'emisiones'              => 'Sistema de Emisiones (DEF / AdBlue, DPF, EGR)',
        'direccion_suspension'   => 'Dirección / Suspensión (Bolsas de aire, Caja de dirección, Muelles)',
        'transmision_clutch'     => 'Transmisión / Clutch (Caja de cambios, Diferencial, Cardán)',
        'llantas'                => 'Llantas',
        'electrico_luces'        => 'Eléctrico / Luces (Baterías, Alternador, Sensores, Arnés)',
        'aire_acondicionado'     => 'Aire Acondicionado / HVAC',
        'pasajeros_carroceria'   => 'Pasajeros / Carrocería',
        'fugas'                  => 'Fugas',
    ];

    const URGENCIAS = [
        'verde'    => 'Ruta normal',
        'amarillo' => 'Revisión prioritaria',
        'rojo'     => 'Unidad detenida',
    ];

    const CONDICIONES = [
        'arranque' => 'Al arrancar el motor',
        'marcha'   => 'Mientras circula en ruta',
        'frenado'  => 'Al frenar',
        'curva'    => 'Al dar vuelta / en curva',
        'carga'    => 'Con pasajeros o carga / en pendiente',
        'reposo'   => 'Estando detenida / en reposo',
    ];

    const SINTOMAS = [
        'ruido'     => 'Ruido anormal',
        'olor'      => 'Olor a quemado o combustible',
        'humo'      => 'Humo',
        'vibracion' => 'Vibración',
        'fuga'      => 'Fuga de algún líquido',
        'testigo'   => 'Luz / testigo encendido en el tablero',
        'ninguno'   => 'Ninguno de los anteriores',
    ];

    const FRECUENCIAS = [
        'primera_vez'   => 'Es la primera vez',
        'intermitente'  => 'Va y viene (intermitente)',
        'constante'     => 'Pasa todo el tiempo',
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

    public function videos()
    {
        return $this->hasMany(ReportEvidence::class)->where('evidence_type', 'video');
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
