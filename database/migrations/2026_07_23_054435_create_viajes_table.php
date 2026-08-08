<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('viajes', function (Blueprint $table) {
            $table->id();
            $table->string('no_contrato');
            $table->foreignId('bus_id')->constrained('buses')->restrictOnDelete();
            $table->foreignId('operador_id')->constrained('users')->restrictOnDelete();
            $table->string('origen');
            $table->string('destino');
            $table->text('recorridos');
            $table->decimal('km_inicial', 10, 2);
            $table->decimal('km_final', 10, 2)->nullable();
            $table->decimal('km_total', 10, 2)->nullable();
            $table->date('fecha_salida');
            $table->date('fecha_regreso');
            $table->decimal('costo_viaje', 10, 2);
            $table->decimal('gastos_entregados', 10, 2);
            $table->decimal('gasto_diesel_inicio', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('viajes');
    }
};
