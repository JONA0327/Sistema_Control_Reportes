<?php

namespace App\Console\Commands;

use App\Models\Contrato;
use App\Models\ContratoAnticipo;
use App\Support\MoneyNormalizer;
use Illuminate\Console\Command;

class FixRoundedMoney extends Command
{
    protected $signature = 'money:fix-rounding {--apply : Actually save the changes (default is dry-run)}';

    protected $description = 'Snap costo_viaje/anticipo/monto amounts that are 1-3 pesos off a round hundred back to it';

    public function handle(): int
    {
        $apply = $this->option('apply');
        $changes = 0;

        foreach (Contrato::all() as $c) {
            $newCosto = MoneyNormalizer::snapToHundred((int) $c->costo_viaje);
            $newAnt = MoneyNormalizer::snapToHundred((int) $c->anticipo);

            if ($newCosto != $c->costo_viaje) {
                $this->line("Contrato {$c->folio} costo_viaje {$c->costo_viaje} -> {$newCosto}");
                $changes++;
                if ($apply) { $c->costo_viaje = $newCosto; }
            }
            if ($newAnt != $c->anticipo) {
                $this->line("Contrato {$c->folio} anticipo {$c->anticipo} -> {$newAnt}");
                $changes++;
                if ($apply) { $c->anticipo = $newAnt; }
            }
            if ($apply) { $c->save(); }
        }

        foreach (ContratoAnticipo::all() as $a) {
            $new = MoneyNormalizer::snapToHundred((int) $a->monto);
            if ($new != $a->monto) {
                $this->line("Anticipo {$a->folio} monto {$a->monto} -> {$new}");
                $changes++;
                if ($apply) { $a->monto = $new; $a->save(); }
            }
        }

        $this->info($apply ? "Corregidos: {$changes}" : "Encontrados: {$changes} (dry-run, usa --apply para guardar)");

        return self::SUCCESS;
    }
}
