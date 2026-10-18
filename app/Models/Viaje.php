<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

// Compras = viajes propios de la empresa: sus egresos/ingresos automáticos esperan
// al cierre (cerrar()) para entrar a ingresos_egresos; turismo y americano entran al momento.
class Viaje extends Model
{
    protected $fillable = [
        'contrato_id',
        'no_contrato',
        'bus_id',
        'operador_id',
        'segundo_operador_id',
        'origen',
        'destino',
        'recorridos',
        'fecha_salida',
        'fecha_regreso',
        'costo_viaje',
        'gastos_entregados',
        'gasto_diesel_inicio',
        'litros_diesel_inicio',
        'costo_litro_diesel_inicio',
    ];

    protected $casts = [
        'fecha_salida' => 'date',
        'fecha_regreso' => 'date',
        'cerrado_at' => 'datetime',
    ];

    public function getEstaPendienteAttribute(): bool
    {
        return is_null($this->operador_id)
            || is_null($this->origen)
            || is_null($this->destino)
            || is_null($this->recorridos)
            || is_null($this->gastos_entregados)
            || is_null($this->gasto_diesel_inicio);
    }

    public function contrato()
    {
        return $this->belongsTo(Contrato::class);
    }

    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }

    public function operador()
    {
        return $this->belongsTo(User::class, 'operador_id');
    }

    public function segundoOperador()
    {
        return $this->belongsTo(User::class, 'segundo_operador_id');
    }

    public function dieselCargas()
    {
        return $this->hasMany(DieselCarga::class);
    }

    public function liquidacion()
    {
        return $this->hasOne(Liquidacion::class);
    }

    public function getEsComprasAttribute(): bool
    {
        return $this->contrato?->tipo === 'compras';
    }

    /** Compras sin cerrar: nada se manda todavía a ingresos_egresos. */
    public function getRetieneMovimientosAttribute(): bool
    {
        return $this->es_compras && is_null($this->cerrado_at);
    }

    public function cortesMerlo(): Collection
    {
        return MerloCorte::with('contrato', 'lines')->where('contrato_id', $this->contrato_id)->orderBy('fecha')->get();
    }

    private function cargasParaEgreso(): Collection
    {
        return $this->dieselCargas()->where('estado_solicitud', 'aprobada')->where('monto', '>', 0)->get();
    }

    /** Lo que el cierre mandará como egreso: [concepto, monto]. */
    public function egresosDelCierre(): Collection
    {
        $lineas = $this->cargasParaEgreso()->map(fn (DieselCarga $c) => [
            'concepto' => $c->tipo === 'inicial' ? 'Diésel inicial' : 'Diésel extra',
            'monto' => (float) $c->monto,
        ]);

        if ($this->gastos_entregados > 0) {
            $lineas->push(['concepto' => 'Gastos entregados al operador', 'monto' => (float) $this->gastos_entregados]);
        }

        // Gastos del corte de Merlo (sueldos, casetas, estacionamiento…) que cuentan como egreso.
        if ($this->es_compras) {
            foreach ($this->cortesMerlo() as $corte) {
                foreach ($corte->lines->where('kind', 'gasto')->where('cuenta', true) as $l) {
                    $lineas->push(['concepto' => 'Corte Merlo: '.$l->concepto, 'monto' => (float) $l->monto]);
                }
            }
        }

        // En turismo/americano el contrato es ingreso (lo paga el cliente), no un egreso del viaje.
        if ($this->es_compras) {
            foreach ($this->contrato->movimientosDelCierre() as $l) {
                $lineas->push(['concepto' => $l['concepto'], 'monto' => $l['monto']]);
            }
        }

        return $lineas->values();
    }

    public function motivoSinCierre(): ?string
    {
        return match (true) {
            ! is_null($this->cerrado_at) => 'El viaje ya está cerrado.',
            $this->esta_pendiente => 'El viaje sigue pendiente de capturarse.',
            $this->liquidacion?->estado !== 'cerrada' => 'La liquidación del operador aún no está cerrada.',
            default => null,
        };
    }

    /** Manda a ingresos_egresos los egresos del viaje y los ingresos de Merlo, y lo marca cerrado. */
    public function cerrar(int $userId): void
    {
        DB::transaction(function () use ($userId) {
            foreach ($this->cargasParaEgreso() as $carga) {
                $carga->publicarEgreso($userId);
            }

            if ($this->gastos_entregados > 0) {
                IngresoEgreso::registrarDesdeOrigen($this, [
                    'tipo' => 'egreso',
                    'concepto' => "Gastos entregados - Viaje {$this->no_contrato}",
                    'monto' => $this->gastos_entregados,
                    'fecha' => $this->fecha_salida,
                    'categoria' => 'Gastos de operador',
                    'clasificacion' => $this->contrato->tipo,
                    'pais' => $this->contrato->pais_ingreso,
                    'user_id' => $userId,
                ]);
                IngresoEgreso::actualizarMontoDesdeOrigen($this, (float) $this->gastos_entregados);
            }

            $this->contrato->publicarMovimientos();

            foreach ($this->cortesMerlo() as $corte) {
                $corte->registrarIngreso($userId);
                $corte->lines->each->sincronizarMovimiento($userId);
            }

            $this->forceFill(['cerrado_at' => now(), 'cerrado_by' => $userId])->save();
        });
    }

    public function getDobleOperadorAttribute(): bool
    {
        return ! is_null($this->segundo_operador_id);
    }

    /**
     * Porcentaje del costo del viaje que gana CADA operador. Con un solo
     * operador se lleva el 15%; si va acompañado de un segundo operador,
     * cada uno gana 10% (20% entre los dos, en vez del 15% de uno solo).
     */
    public function porcentajeGananciaPorOperador(): float
    {
        return $this->doble_operador ? 0.10 : 0.15;
    }

    /**
     * Ganancia total estimada del viaje (la suma de lo que gana cada
     * operador). Los gastos/liquidación del viaje se administran desde
     * la cuenta del operador titular; el segundo operador solo puede
     * consultarlos.
     */
    public function gananciaEstimada(): float
    {
        $operadores = $this->doble_operador ? 2 : 1;

        return (float) $this->costo_viaje * $this->porcentajeGananciaPorOperador() * $operadores;
    }
}
