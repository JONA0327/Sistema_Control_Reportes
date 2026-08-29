<?php

namespace App\Console\Commands;

use App\Models\Contrato;
use App\Models\DieselCarga;
use App\Models\IngresoEgreso;
use App\Models\Viaje;
use Illuminate\Console\Command;

class BackfillIngresosEgresos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backfill-ingresos-egresos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera en Egresos y Ingresos los movimientos automáticos que faltan para contratos, viajes y diésel ya existentes';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $creados = 0;

        Contrato::with('pagos')->chunkById(50, function ($contratos) use (&$creados) {
            foreach ($contratos as $contrato) {
                if ($contrato->esta_liquidado) {
                    $antes = IngresoEgreso::count();
                    IngresoEgreso::registrarDesdeOrigen($contrato, [
                        'tipo' => 'ingreso',
                        'concepto' => "Contrato {$contrato->folio} liquidado - {$contrato->cliente_nombre}",
                        'monto' => $contrato->costo_viaje,
                        'fecha' => $contrato->fecha_firma,
                        'categoria' => 'Contratos',
                        'user_id' => $contrato->user_id,
                    ]);
                    $creados += IngresoEgreso::count() - $antes;

                    continue;
                }

                if ($contrato->anticipo > 0) {
                    $antes = IngresoEgreso::count();
                    IngresoEgreso::registrarDesdeOrigen($contrato, [
                        'tipo' => 'ingreso',
                        'concepto' => "Anticipo contrato {$contrato->folio} - {$contrato->cliente_nombre}",
                        'monto' => $contrato->anticipo,
                        'fecha' => $contrato->fecha_firma,
                        'categoria' => 'Contratos',
                        'user_id' => $contrato->user_id,
                    ]);
                    $creados += IngresoEgreso::count() - $antes;
                }

                foreach ($contrato->pagos as $pago) {
                    $antes = IngresoEgreso::count();
                    IngresoEgreso::registrarDesdeOrigen($pago, [
                        'tipo' => 'ingreso',
                        'concepto' => "Abono contrato {$contrato->folio} - {$contrato->cliente_nombre}",
                        'monto' => $pago->monto,
                        'fecha' => $pago->fecha_pago,
                        'categoria' => 'Contratos',
                        'user_id' => $pago->user_id,
                    ]);
                    $creados += IngresoEgreso::count() - $antes;
                }
            }
        });

        Viaje::chunkById(50, function ($viajes) use (&$creados) {
            foreach ($viajes as $viaje) {
                if ($viaje->gastos_entregados <= 0) {
                    continue;
                }

                $antes = IngresoEgreso::count();
                IngresoEgreso::registrarDesdeOrigen($viaje, [
                    'tipo' => 'egreso',
                    'concepto' => "Gastos entregados - Viaje {$viaje->no_contrato}",
                    'monto' => $viaje->gastos_entregados,
                    'fecha' => $viaje->fecha_salida,
                    'categoria' => 'Gastos de operador',
                    'user_id' => $viaje->operador_id,
                ]);
                $creados += IngresoEgreso::count() - $antes;
            }
        });

        DieselCarga::with('viaje')->where('estado_solicitud', 'aprobada')->chunkById(50, function ($cargas) use (&$creados) {
            foreach ($cargas as $carga) {
                if (! $carga->viaje) {
                    continue;
                }

                $antes = IngresoEgreso::count();
                $carga->registrarEgresoAutomatico($carga->reviewed_by ?? $carga->requested_by);
                $creados += IngresoEgreso::count() - $antes;
            }
        });

        $this->info("Backfill completado. Movimientos nuevos creados: {$creados}.");

        return self::SUCCESS;
    }
}
