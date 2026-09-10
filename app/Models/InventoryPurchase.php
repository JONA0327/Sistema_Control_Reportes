<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryPurchase extends Model
{
    protected $fillable = [
        'item_id',
        'quantity',
        'precio',
        'comprobante_path',
        'estado',
        'pais',
        'notas',
        'motivo_rechazo',
        'solicitado_por',
        'revisado_por',
        'revisado_at',
    ];

    protected $casts = [
        'precio'      => 'decimal:2',
        'revisado_at' => 'datetime',
    ];

    const ESTADOS = [
        'pendiente' => 'Pendiente de validar',
        'aprobada'  => 'Aprobada',
        'rechazada' => 'Rechazada',
    ];

    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }

    public function solicitadoPor()
    {
        return $this->belongsTo(User::class, 'solicitado_por');
    }

    public function revisadoPor()
    {
        return $this->belongsTo(User::class, 'revisado_por');
    }

    public function registrarEgresoAutomatico(int $userId): void
    {
        IngresoEgreso::registrarDesdeOrigen($this, [
            'tipo'      => 'egreso',
            'concepto'  => "Compra de refacción: {$this->item->name} ({$this->item->code})",
            'monto'     => $this->precio,
            'fecha'     => $this->revisado_at ?? now(),
            'categoria' => 'Inventario',
            'pais'      => $this->pais,
            'user_id'   => $userId,
        ]);
    }
}
