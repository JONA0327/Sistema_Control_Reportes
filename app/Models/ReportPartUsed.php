<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportPartUsed extends Model
{
    protected $table = 'report_parts_used';

    public $timestamps = false;

    protected $fillable = [
        'report_id',
        'item_id',
        'movement_id',
        'quantity_used',
        'installed_by',
        'installed_at',
    ];

    protected $casts = [
        'installed_at' => 'datetime',
    ];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }

    public function movement()
    {
        return $this->belongsTo(InventoryMovement::class, 'movement_id');
    }

    public function installedBy()
    {
        return $this->belongsTo(User::class, 'installed_by');
    }
}
