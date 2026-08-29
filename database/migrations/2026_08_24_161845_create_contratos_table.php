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
        Schema::create('contratos', function (Blueprint $table) {
            $table->id();
            $table->string('cliente_nombre');
            $table->string('cliente_telefono')->nullable();
            $table->string('cliente_domicilio')->nullable();
            $table->string('cliente_ciudad')->nullable();
            $table->foreignId('bus_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('num_plazas')->nullable();
            $table->date('fecha_salida');
            $table->string('hora_salida')->nullable();
            $table->date('fecha_regreso');
            $table->string('hora_regreso')->nullable();
            $table->string('salida_destino');
            $table->string('punto_partida_llegada')->nullable();
            $table->text('itinerario')->nullable();
            $table->boolean('incluye_estacionamiento')->default(true);
            $table->decimal('costo_viaje', 10, 2);
            $table->decimal('anticipo', 10, 2)->default(0);
            $table->text('notas')->nullable();
            $table->string('lugar_firma')->default('San Luis Potosí, S.L.P.');
            $table->date('fecha_firma');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contratos');
    }
};
