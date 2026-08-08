<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'num_bus',
        'placa',
        'foto',
        'operator_id',
        'status',
    ];

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function pendingLoans()
    {
        return $this->hasMany(InventoryMovement::class, 'responsable_id', 'operator_id')
            ->where('movement_type', 'salida')
            ->whereNull('devuelto_at');
    }
}
