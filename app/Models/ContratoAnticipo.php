<?php

namespace App\Models;

use App\Services\GroqAiService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ContratoAnticipo extends Model
{
    protected $table = 'contrato_anticipos';

    protected $fillable = [
        'contrato_id',
        'folio',
        'monto',
        'fecha_anticipo',
        'metodo_pago',
        'evidencia_path',
        'evidencia_nombre_original',
        'notas',
        'user_id',
        'cancelado_at',
        'motivo_cancelacion',
        'cancelado_por',
    ];

    protected function casts(): array
    {
        return [
            'fecha_anticipo' => 'date',
            'monto' => 'decimal:2',
            'cancelado_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        // Genera el folio ANT-{contrato.folio}-{seq} antes de crear el registro.
        // El folio del contrato ya viene como "CT-00042", así que el anticipo
        // queda "ANT-CT-00042-001" / "ANT-CT-00042-002" / etc.
        static::creating(function (self $anticipo) {
            if (! $anticipo->folio) {
                $anticipo->folio = self::nextFolioFor($anticipo->contrato_id);
            }
        });

        // Refleja cada anticipo en ingresos_egresos para que cuadre con
        // la contabilidad sin necesidad de un sync manual. Se hace con
        // morph porque IngresoEgreso ya tiene la columna origenable.
        static::created(function (self $anticipo) {
            $anticipo->loadMissing('contrato');
            IngresoEgreso::registrarDesdeOrigen($anticipo, [
                'tipo' => 'ingreso',
                'concepto' => "Anticipo {$anticipo->folio} contrato {$anticipo->contrato->folio} - {$anticipo->contrato->cliente_nombre}",
                'monto' => $anticipo->monto,
                'fecha' => $anticipo->fecha_anticipo,
                'categoria' => 'Contratos',
                'pais' => app(GroqAiService::class)->determinarPais($anticipo->contrato->destino),
                'user_id' => $anticipo->user_id,
            ]);
        });

        static::updated(function (self $anticipo) {
            if ($anticipo->cancelado) {
                IngresoEgreso::eliminarDesdeOrigen($anticipo);

                return;
            }

            IngresoEgreso::actualizarMontoDesdeOrigen($anticipo, (float) $anticipo->monto);
        });

        static::deleted(function (self $anticipo) {
            IngresoEgreso::eliminarDesdeOrigen($anticipo);
        });
    }

    public static function nextFolioFor(int $contratoId): string
    {
        $contrato = Contrato::find($contratoId);

        if (! $contrato) {
            throw new \RuntimeException("No se puede generar folio de anticipo: contrato {$contratoId} no existe.");
        }

        $secuencia = (int) self::where('contrato_id', $contratoId)->count() + 1;

        return sprintf('ANT-%s-%03d', $contrato->folio, $secuencia);
    }

    public function contrato(): BelongsTo
    {
        return $this->belongsTo(Contrato::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function canceladoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelado_por');
    }

    public function getCanceladoAttribute(): bool
    {
        return ! is_null($this->cancelado_at);
    }

    public function tieneEvidencia(): bool
    {
        return ! empty($this->evidencia_path)
            && Storage::disk('public')->exists($this->evidencia_path);
    }

    public function evidenciaUrl(): ?string
    {
        return $this->tieneEvidencia()
            ? Storage::disk('public')->url($this->evidencia_path)
            : null;
    }

    public function evidenciaEsImagen(): bool
    {
        if (! $this->evidencia_path) {
            return false;
        }

        $extension = strtolower(pathinfo($this->evidencia_path, PATHINFO_EXTENSION));

        return in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true);
    }
}
