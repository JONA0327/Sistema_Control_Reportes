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
        Schema::create('diesel_cargas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('viaje_id')->constrained('viajes')->restrictOnDelete();
            $table->string('tipo'); // inicial | extra
            $table->decimal('monto', 10, 2);
            $table->string('fuente')->nullable(); // gastos_entregados | transferencia
            $table->string('origen'); // operador | administracion
            $table->string('estado_solicitud')->default('pendiente'); // pendiente | aprobada | rechazada
            $table->string('estado_evidencia')->default('sin_evidencia'); // sin_evidencia | enviada | validada | rechazada
            $table->decimal('litros', 10, 2)->nullable();
            $table->string('ticket_path')->nullable();
            $table->string('foto_litros_path')->nullable();
            $table->string('motivo_rechazo')->nullable();
            $table->foreignId('requested_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diesel_cargas');
    }
};
