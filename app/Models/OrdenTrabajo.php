<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenTrabajo extends Model
{
    protected $table = 'ordenes_trabajo';

    protected $fillable = [
        'report_id',
        'mecanico_id',
        'recibido_at',
        'tipo_atencion',
        'falla_confirmada',
        'subsistemas',
        'codigo_falla',
        'diagnostico',
        'proveedor_externo',
        'folio_proveedor',
        'motivo_externo',
        'fecha_promesa_entrega',
    ];

    protected $casts = [
        'recibido_at'           => 'datetime',
        'fecha_promesa_entrega' => 'datetime',
        'subsistemas'           => 'array',
        'motivo_externo'        => 'array',
    ];

    const TIPOS_ATENCION = [
        'taller_interno' => 'Taller interno',
        'taller_externo' => 'Taller externo / Agencia especializada',
        'auxilio_vial'   => 'Auxilio vial / Rescate en ruta',
    ];

    const FALLA_CONFIRMADA = [
        'si'           => 'Sí',
        'no'           => 'No',
        'intermitente' => 'Intermitente / No replicada',
    ];

    const SUBSISTEMAS = [
        'motor'                  => 'Motor (Inyección, Turbo, Enfriamiento, Admisión)',
        'emisiones'              => 'Sistema de Emisiones (DEF / AdBlue, DPF, EGR)',
        'neumatico_frenos'       => 'Sistema Neumático / Frenos (Secador, Válvulas, Balatas, Cámaras)',
        'tren_motriz'            => 'Tren Motriz (Caja de cambios, Diferencial, Cardán, Clutch)',
        'suspension_direccion'   => 'Suspensión / Dirección (Bolsas de aire, Caja de dirección, Muelles)',
        'electrico_electronico'  => 'Eléctrico / Electrónico (Baterías, Alternador, Sensores, Arnés)',
        'aire_acondicionado'     => 'Sistema de Aire Acondicionado / HVAC',
    ];

    const MOTIVOS_EXTERNO = [
        'diagnostico_marca'   => 'Diagnóstico por escáner propietario / software de marca',
        'garantia'            => 'Garantía',
        'reparacion_mayor'    => 'Reparación mayor de motor / transmisión',
        'falta_herramienta'   => 'Falta de herramienta especializada / torno',
        'hojalateria_pintura' => 'Hojalatería, pintura o cristales',
    ];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function mecanico()
    {
        return $this->belongsTo(User::class, 'mecanico_id');
    }
}
