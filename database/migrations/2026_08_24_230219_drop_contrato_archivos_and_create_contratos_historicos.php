<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('contrato_archivos');

        Schema::create('contratos_historicos', function (Blueprint $table) {
            $table->id();
            $table->string('folio');
            $table->string('cliente_nombre');
            $table->string('destino')->nullable();
            $table->date('fecha_salida')->nullable();
            $table->date('fecha_regreso')->nullable();
            $table->decimal('precio_viaje', 10, 2)->nullable();
            $table->decimal('anticipo', 10, 2)->nullable();
            $table->string('archivo_path');
            $table->string('nombre_original');
            $table->string('descripcion')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contratos_historicos');

        Schema::create('contrato_archivos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrato_id')->nullable()->constrained()->nullOnDelete();
            $table->string('cliente_nombre')->nullable();
            $table->string('archivo_path');
            $table->string('nombre_original');
            $table->string('descripcion')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }
};
