<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Contrato extends Model
{
    protected $table = 'contratos';

    const TIPOS = [
        'turismo' => 'Turismo',
        'compras' => 'Compras',
        'americano' => 'Americano',
    ];

    protected $fillable = [
        'cliente_nombre',
        'tipo',
        'cliente_telefono',
        'cliente_domicilio',
        'cliente_ciudad',
        'bus_id',
        'num_plazas',
        'fecha_salida',
        'hora_salida',
        'fecha_regreso',
        'hora_regreso',
        'salida',
        'destino',
        'punto_partida_llegada',
        'itinerario',
        'incluye_estacionamiento',
        'costo_viaje',
        'anticipo',
        'notas',
        'lugar_firma',
        'fecha_firma',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'fecha_salida' => 'date',
            'fecha_regreso' => 'date',
            'fecha_firma' => 'date',
            'costo_viaje' => 'decimal:2',
            'anticipo' => 'decimal:2',
            'incluye_estacionamiento' => 'boolean',
        ];
    }

    public function getSalidaDestinoAttribute(): string
    {
        return trim("{$this->salida} → {$this->destino}", ' →');
    }

    public function getFolioAttribute(): string
    {
        return 'CT-'.str_pad((string) $this->id, 5, '0', STR_PAD_LEFT);
    }

    public function getTipoLabelAttribute(): string
    {
        return self::TIPOS[$this->tipo] ?? 'Sin tipo';
    }

    /**
     * País para los ingresos/egresos generados por este contrato: los
     * contratos "americano" son USA, turismo y compras son México (ahí
     * se distinguen entre sí por la categoría, no por el país).
     */
    public function getPaisIngresoAttribute(): string
    {
        return $this->tipo === 'americano' ? 'usa' : 'mexico';
    }

    /**
     * Estos tres montos se redondean al peso porque los contratos ya
     * manejan enteros; así los centavos que quedaron en datos viejos
     * (de cuando sí se permitían decimales) no dejan un saldo pendiente
     * fantasma de unos centavos en un contrato que en la práctica ya
     * está liquidado. El costo total del contrato es la referencia.
     */
    public function getRestoAttribute(): float
    {
        return round((float) $this->costo_viaje - $this->anticipo_efectivo);
    }

    public function getTotalPagadoAttribute(): float
    {
        return round($this->anticipo_efectivo + (float) $this->pagos->sum('monto'));
    }

    public function getSaldoPendienteAttribute(): float
    {
        return max(0.0, round((float) $this->costo_viaje - $this->total_pagado));
    }

    public function getEstaLiquidadoAttribute(): bool
    {
        return $this->saldo_pendiente <= 0;
    }

    /**
     * Compras = viaje propio de la empresa: lo que se paga por el contrato es
     * EGRESO (inversión) y no ingreso, y no entra a ingresos_egresos hasta
     * que se cierra el viaje (Viaje::cerrar()). Turismo/americano: ingreso, al momento.
     */
    public function getEsComprasAttribute(): bool
    {
        return $this->tipo === 'compras';
    }

    public function getTipoMovimientoAttribute(): string
    {
        return $this->es_compras ? 'egreso' : 'ingreso';
    }

    public function getRetieneMovimientosAttribute(): bool
    {
        return $this->es_compras
            && ! Viaje::where('contrato_id', $this->id)->whereNotNull('cerrado_at')->exists();
    }

    /** Movimientos del contrato (anticipos y pagos) que el cierre mandará a ingresos_egresos. */
    public function movimientosDelCierre(): Collection
    {
        $this->load('anticipos', 'pagos');

        $lineas = $this->anticipos->whereNull('cancelado_at')->map(fn (ContratoAnticipo $a) => [
            'origen' => $a,
            'concepto' => "Anticipo {$a->folio} contrato {$this->folio} - {$this->cliente_nombre}",
            'monto' => (float) $a->monto,
            'fecha' => $a->fecha_anticipo,
            'categoria' => 'Anticipo',
            'user_id' => $a->user_id,
        ]);

        // Contratos viejos sin anticipos formales: cuenta la columna `anticipo`.
        if ($lineas->isEmpty() && $this->anticipo > 0) {
            $lineas->push([
                'origen' => $this,
                'concepto' => "Anticipo contrato {$this->folio} - {$this->cliente_nombre}",
                'monto' => (float) $this->anticipo,
                'fecha' => $this->fecha_firma,
                'categoria' => 'Anticipo',
                'user_id' => $this->user_id,
            ]);
        }

        foreach ($this->pagos as $pago) {
            $lineas->push([
                'origen' => $pago,
                'concepto' => "Abono contrato {$this->folio} - {$this->cliente_nombre}",
                'monto' => (float) $pago->monto,
                'fecha' => $pago->fecha_pago,
                'categoria' => 'Abono',
                'user_id' => $pago->user_id,
            ]);
        }

        return $lineas->values();
    }

    /**
     * Al cambiar el tipo (p. ej. clasificar un contrato "sin tipo", aunque ya esté
     * cerrado), corrige lo ya registrado en ingresos_egresos: clasificación, país y,
     * en anticipos/abonos, si es ingreso o egreso.
     */
    public function resincronizarMovimientos(): void
    {
        $viajeIds = Viaje::where('contrato_id', $this->id)->pluck('id');

        $origenes = collect([$this])
            ->concat($this->anticipos()->get())
            ->concat($this->pagos()->get())
            ->concat(Viaje::whereIn('id', $viajeIds)->get())
            ->concat(DieselCarga::whereIn('viaje_id', $viajeIds)->get())
            ->concat(MerloCorte::where('contrato_id', $this->id)->get())
            ->concat(MerloCorteLine::whereIn('merlo_corte_id', MerloCorte::where('contrato_id', $this->id)->select('id'))->get());

        foreach ($origenes as $origen) {
            IngresoEgreso::actualizarClasificacionDesdeOrigen($origen, $this->tipo, $this->pais_ingreso);

            if ($origen instanceof self || $origen instanceof ContratoAnticipo || $origen instanceof ContratoPago) {
                IngresoEgreso::actualizarTipoDesdeOrigen($origen, $this->tipo_movimiento);
            }
        }
    }

    public function publicarMovimientos(): void
    {
        foreach ($this->movimientosDelCierre() as $l) {
            IngresoEgreso::registrarDesdeOrigen($l['origen'], [
                'tipo' => $this->tipo_movimiento,
                'concepto' => $l['concepto'],
                'monto' => $l['monto'],
                'fecha' => $l['fecha'],
                'categoria' => $l['categoria'],
                'clasificacion' => $this->tipo,
                'pais' => $this->pais_ingreso,
                'user_id' => $l['user_id'],
            ]);
            // Si ya existía (registrado antes como ingreso), lo deja con el tipo y monto correctos.
            IngresoEgreso::actualizarMontoDesdeOrigen($l['origen'], $l['monto']);
            IngresoEgreso::actualizarTipoDesdeOrigen($l['origen'], $this->tipo_movimiento);
        }

        // La columna legacy no debe sumar si ya hay anticipos formales.
        if ($this->anticipos->whereNull('cancelado_at')->isNotEmpty()) {
            IngresoEgreso::eliminarDesdeOrigen($this);
        }
    }

    /**
     * Corte contable (Ajuste::CORTE_HISTORICO): contratos con salida hasta esa
     * fecha no cuentan en ingresos/egresos y se trabajan desde Históricos.
     */
    public function getEsHistoricoAttribute(): bool
    {
        $corte = Ajuste::corteHistorico();

        return $corte && $this->fecha_salida && $this->fecha_salida->lte($corte);
    }

    public function scopeNoHistoricos($query)
    {
        $corte = Ajuste::corteHistorico();

        return $corte
            ? $query->where(fn ($q) => $q->whereNull('fecha_salida')->orWhereDate('fecha_salida', '>', $corte))
            : $query;
    }

    public function scopeHistoricos($query)
    {
        $corte = Ajuste::corteHistorico();

        return $corte ? $query->whereDate('fecha_salida', '<=', $corte) : $query->whereRaw('1 = 0');
    }

    /** Liquidaciones y gastos capturados a mano desde Históricos. */
    public function movimientosManuales(): HasMany
    {
        return $this->hasMany(IngresoEgreso::class, 'contrato_id');
    }

    public function bus(): BelongsTo
    {
        return $this->belongsTo(Bus::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(ContratoPago::class);
    }

    /**
     * Anticipos formalizados del contrato (cada uno con su folio,
     * evidencia y comprobante PDF). La columna `anticipo` del contrato
     * sigue existiendo por compatibilidad con los contratos generados
     * antes de esta tabla; el accessor `anticipo_total` es la suma
     * de los anticipos formalizados.
     */
    public function anticipos(): HasMany
    {
        return $this->hasMany(ContratoAnticipo::class)->latest('fecha_anticipo')->latest('id');
    }

    public function getAnticipoTotalAttribute(): float
    {
        return (float) $this->anticipos->whereNull('cancelado_at')->sum('monto');
    }

    /**
     * Anticipo efectivo que se usa en cálculos y en la UI. Si ya hay
     * anticipos formalizados (no cancelados), su suma es la fuente de
     * verdad. Si no hay formales (contratos legacy), se usa la columna
     * `anticipo` del contrato. Esto evita doble conteo en contratos
     * nuevos (que se crean con anticipo + formal al mismo tiempo).
     */
    public function getAnticipoEfectivoAttribute(): float
    {
        $formalizados = (float) $this->anticipos->whereNull('cancelado_at')->sum('monto');

        return $formalizados > 0 ? $formalizados : (float) $this->anticipo;
    }
}
