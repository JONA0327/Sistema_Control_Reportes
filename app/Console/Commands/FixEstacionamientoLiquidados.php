<?php

namespace App\Console\Commands;

use App\Models\Contrato;
use Illuminate\Console\Command;

class FixEstacionamientoLiquidados extends Command
{
    protected $signature = 'contratos:fix-estacionamiento {--apply : Actually save the changes (default is dry-run)}';

    protected $description = 'Set incluye_estacionamiento=false on already-liquidated contracts (new default: not included)';

    public function handle(): int
    {
        $apply = $this->option('apply');
        $changes = 0;

        foreach (Contrato::all() as $c) {
            if ($c->esta_liquidado && $c->incluye_estacionamiento) {
                $this->line("Contrato {$c->folio}: incluye_estacionamiento true -> false");
                $changes++;
                if ($apply) {
                    $c->incluye_estacionamiento = false;
                    $c->save();
                }
            }
        }

        $this->info($apply ? "Corregidos: {$changes}" : "Encontrados: {$changes} (dry-run, usa --apply para guardar)");

        return self::SUCCESS;
    }
}
