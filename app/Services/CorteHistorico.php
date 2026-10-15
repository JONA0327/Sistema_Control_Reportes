<?php

namespace App\Services;

use App\Models\IngresoEgreso;

class CorteHistorico
{
    /**
     * Recalcula qué movimientos automáticos se excluyen según el corte actual
     * (sirve para activarlo, moverlo o apagarlo). Devuelve cuántos cambiaron.
     */
    public static function aplicar(): int
    {
        $cambiados = 0;

        IngresoEgreso::withoutGlobalScopes()
            ->whereNotNull('origenable_type')
            ->chunkById(200, function ($movimientos) use (&$cambiados) {
                foreach ($movimientos as $m) {
                    $excluir = (bool) IngresoEgreso::contratoDeOrigen($m->origenable)?->es_historico;

                    if ($m->excluido_historico !== $excluir) {
                        $m->excluido_historico = $excluir;
                        $m->save();
                        $cambiados++;
                    }
                }
            });

        return $cambiados;
    }
}
