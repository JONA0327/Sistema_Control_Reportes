<?php

namespace App\Console\Commands;

use App\Models\Bus;
use App\Models\Contrato;
use App\Models\Viaje;
use Illuminate\Console\Command;

class BackfillViajesDesdeContratos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backfill-viajes-desde-contratos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea el viaje pendiente de cualquier contrato que todavía no tenga uno vinculado';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $creados = 0;

        Contrato::chunkById(50, function ($contratos) use (&$creados) {
            foreach ($contratos as $contrato) {
                if (Viaje::where('contrato_id', $contrato->id)->exists()) {
                    continue;
                }

                $operadorId = $contrato->bus_id ? Bus::find($contrato->bus_id)?->operator_id : null;

                Viaje::create([
                    'contrato_id' => $contrato->id,
                    'no_contrato' => $contrato->folio,
                    'bus_id' => $contrato->bus_id,
                    'operador_id' => $operadorId,
                    'origen' => $contrato->salida,
                    'destino' => $contrato->destino,
                    'recorridos' => $contrato->itinerario,
                    'fecha_salida' => $contrato->fecha_salida,
                    'fecha_regreso' => $contrato->fecha_regreso,
                    'costo_viaje' => $contrato->costo_viaje,
                ]);

                $creados++;
            }
        });

        $this->info("Viajes pendientes creados: {$creados}.");

        return self::SUCCESS;
    }
}
