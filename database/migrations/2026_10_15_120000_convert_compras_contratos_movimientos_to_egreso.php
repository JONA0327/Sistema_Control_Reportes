<?php

use App\Models\Contrato;
use App\Models\ContratoAnticipo;
use App\Models\ContratoPago;
use App\Models\IngresoEgreso;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    // Los contratos de compras son viajes propios: lo ya registrado de ellos
    // (anticipos, abonos, contrato) pasa de ingreso a egreso.
    public function up(): void
    {
        $contratos = Contrato::where('tipo', 'compras')->select('id');

        IngresoEgreso::withoutGlobalScopes()->where('tipo', 'ingreso')
            ->where(function ($q) use ($contratos) {
                $q->where(fn ($q) => $q->where('origenable_type', (new Contrato)->getMorphClass())
                    ->whereIn('origenable_id', (clone $contratos)))
                    ->orWhere(fn ($q) => $q->where('origenable_type', (new ContratoAnticipo)->getMorphClass())
                        ->whereIn('origenable_id', ContratoAnticipo::whereIn('contrato_id', (clone $contratos))->select('id')))
                    ->orWhere(fn ($q) => $q->where('origenable_type', (new ContratoPago)->getMorphClass())
                        ->whereIn('origenable_id', ContratoPago::whereIn('contrato_id', (clone $contratos))->select('id')));
            })
            ->update(['tipo' => 'egreso']);
    }

    // Irreversible a propósito: no se puede saber cuáles eran ingreso originalmente
    // sin volver a clasificar por tipo de contrato.
    public function down(): void
    {
        //
    }
};
