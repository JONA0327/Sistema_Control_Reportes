<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Movimiento extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'modulo',
        'accion',
        'descripcion',
        'motivo',
        'movible_type',
        'movible_id',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    const MODULOS = [
        'anticipos' => 'Anticipos',
        'inventario' => 'Inventario',
        'reportes' => 'Reportes',
        'contratos' => 'Contratos',
    ];

    const ACCIONES = [
        'creado' => 'Creado',
        'editado' => 'Editado',
        'eliminado' => 'Eliminado',
        'cancelado' => 'Cancelado',
        'estado_cambiado' => 'Cambio de estado',
        'rechazado' => 'Rechazado',
        'aprobado' => 'Aprobado',
    ];

    /**
     * Registra un movimiento. $movible es opcional: el modelo afectado
     * (para poder referenciarlo si en el futuro se necesita), útil sobre
     * todo cuando el registro ya fue borrado y solo queda la descripción.
     */
    public static function registrar(
        string $modulo,
        string $accion,
        string $descripcion,
        ?string $motivo = null,
        ?Model $movible = null,
        ?int $userId = null,
    ): self {
        return self::create([
            'modulo' => $modulo,
            'accion' => $accion,
            'descripcion' => $descripcion,
            'motivo' => $motivo,
            'movible_type' => $movible?->getMorphClass(),
            'movible_id' => $movible?->getKey(),
            'user_id' => $userId ?? auth()->id(),
            'created_at' => now(),
        ]);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function movible(): MorphTo
    {
        return $this->morphTo();
    }

    public function getModuloLabelAttribute(): string
    {
        return self::MODULOS[$this->modulo] ?? ucfirst($this->modulo);
    }

    public function getAccionLabelAttribute(): string
    {
        return self::ACCIONES[$this->accion] ?? ucfirst(str_replace('_', ' ', $this->accion));
    }
}
