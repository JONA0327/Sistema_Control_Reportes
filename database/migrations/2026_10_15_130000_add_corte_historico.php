<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Corte contable: los contratos con fecha de salida <= `corte_historico_hasta`
    // se ignoran en ingresos/egresos (excluido_historico) y se trabajan desde
    // Históricos, donde los movimientos manuales llevan contrato_id.
    public function up(): void
    {
        Schema::create('ajustes', function (Blueprint $table) {
            $table->string('clave')->primary();
            $table->string('valor')->nullable();
            $table->timestamps();
        });

        Schema::table('ingresos_egresos', function (Blueprint $table) {
            $table->boolean('excluido_historico')->default(false)->index();
            $table->foreignId('contrato_id')->nullable()->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ingresos_egresos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('contrato_id');
            $table->dropColumn('excluido_historico');
        });
        Schema::dropIfExists('ajustes');
    }
};
