<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'item_id',
        'user_id',
        'purchase_order_id',
        'movement_type',
        'quantity',
        'movement_date',
        'notes',
        'responsable_id',
        'devuelto_at',
    ];

    protected $casts = [
        'movement_date' => 'datetime',
        'devuelto_at' => 'datetime',
    ];

    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function getPendienteAttribute(): bool
    {
        return $this->movement_type === 'salida'
            && ! is_null($this->responsable_id)
            && is_null($this->devuelto_at);
    }
}
