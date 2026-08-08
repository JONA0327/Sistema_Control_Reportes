<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('liquidacion_gastos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('liquidacion_id')->constrained('liquidaciones')->cascadeOnDelete();
            $table->string('tipo');
            $table->string('concepto')->nullable();
            $table->decimal('monto', 10, 2);
            $table->string('evidencia_path')->nullable();
            $table->string('estado')->default('pendiente');
            $table->string('motivo_rechazo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('liquidacion_gastos');
    }
};
