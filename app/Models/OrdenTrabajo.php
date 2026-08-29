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
        'proveedor_externo_user_id',
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

    /**
     * Mismo catálogo que Report::CATEGORIAS, para que las opciones del
     * levantamiento del operador coincidan con las del diagnóstico del mecánico.
     */
    const SUBSISTEMAS = Report::CATEGORIAS;

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

    public function piezas()
    {
        return $this->hasMany(OrdenTrabajoPieza::class);
    }

    public function proveedorExternoUser()
    {
        return $this->belongsTo(User::class, 'proveedor_externo_user_id');
    }
}
