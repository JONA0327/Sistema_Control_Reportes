<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Cortes de viaje recibidos de Merlo Web. trip_id (viaje en Merlo) es la
    // llave de idempotencia: reenviar un corte actualiza, no duplica.
    public function up(): void
    {
        Schema::create('merlo_cortes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('trip_id')->unique();
            $table->date('fecha')->index();
            $table->string('ruta');
            $table->unsignedInteger('total_seats');
            $table->unsignedInteger('sold_seats');
            $table->unsignedInteger('late_sold_seats');
            $table->unsignedInteger('apartado_seats');
            $table->unsignedInteger('free_seats');
            $table->decimal('sold_amount', 12, 2);
            $table->decimal('late_amount', 12, 2);
            $table->decimal('abonos_amount', 12, 2);
            $table->decimal('total_cost', 12, 2);
            $table->foreignId('contrato_id')->constrained()->restrictOnDelete(); // solo contratos tipo compras
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('merlo_cortes');
    }
};
