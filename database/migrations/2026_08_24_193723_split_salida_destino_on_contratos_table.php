<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contratos', function (Blueprint $table) {
            $table->string('salida')->nullable()->after('num_plazas');
            $table->string('destino')->nullable()->after('salida');
        });

        foreach (DB::table('contratos')->select('id', 'salida_destino')->get() as $contrato) {
            $partes = preg_split('/\s*[\/\-–—]\s*| a | hacia /i', $contrato->salida_destino, 2);

            DB::table('contratos')->where('id', $contrato->id)->update([
                'salida' => trim($partes[0] ?? '') ?: $contrato->salida_destino,
                'destino' => trim($partes[1] ?? ''),
            ]);
        }

        Schema::table('contratos', function (Blueprint $table) {
            $table->dropColumn('salida_destino');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contratos', function (Blueprint $table) {
            $table->string('salida_destino')->nullable()->after('num_plazas');
        });

        foreach (DB::table('contratos')->select('id', 'salida', 'destino')->get() as $contrato) {
            DB::table('contratos')->where('id', $contrato->id)->update([
                'salida_destino' => trim(($contrato->salida ?? '').' / '.($contrato->destino ?? ''), ' /'),
            ]);
        }

        Schema::table('contratos', function (Blueprint $table) {
            $table->dropColumn(['salida', 'destino']);
        });
    }
};
