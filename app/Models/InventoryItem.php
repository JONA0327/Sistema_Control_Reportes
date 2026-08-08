<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'code',
        'name',
        'description',
        'category',
        'photo_path',
        'stock_quantity',
        'min_stock',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function movements()
    {
        return $this->hasMany(InventoryMovement::class, 'item_id');
    }

    public function getLowStockAttribute(): bool
    {
        return $this->stock_quantity <= $this->min_stock;
    }

    public function getIsHerramientaAttribute(): bool
    {
        return str_contains(strtolower($this->category), 'herramienta');
    }
}
