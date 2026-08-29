<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContratoHistorico extends Model
{
    protected $table = 'contratos_historicos';

    protected $fillable = [
        'folio',
        'cliente_nombre',
        'destino',
        'fecha_salida',
        'fecha_regreso',
        'precio_viaje',
        'anticipo',
        'archivo_path',
        'nombre_original',
        'descripcion',
        'user_id',
    ];

    protected $casts = [
        'fecha_salida' => 'date',
        'fecha_regreso' => 'date',
        'precio_viaje' => 'decimal:2',
        'anticipo' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function esImagen(): bool
    {
        return in_array(strtolower(pathinfo($this->archivo_path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    }
}
