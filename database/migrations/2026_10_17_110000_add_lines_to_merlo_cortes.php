<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Corte de viaje de Merlo con otros conceptos: gastos e ingresos del día (renglones),
    // desglose de ventas y el efectivo capturado a mano.
    public function up(): void
    {
        Schema::table('merlo_cortes', function (Blueprint $table) {
            $table->decimal('efectivo', 12, 2)->nullable();
            $table->json('desglose')->nullable();
        });

        Schema::create('merlo_corte_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merlo_corte_id')->constrained('merlo_cortes')->cascadeOnDelete();
            $table->unsignedBigInteger('merlo_id')->unique(); // id del renglón en Merlo (idempotencia)
            $table->string('kind'); // gasto | ingreso
            $table->string('concepto');
            $table->decimal('cantidad', 8, 2)->default(1);
            $table->decimal('monto_unitario', 12, 2);
            $table->decimal('monto', 12, 2);
            $table->string('nota')->nullable();
            $table->boolean('cuenta')->default(true); // false = solo informativo
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('merlo_corte_lines');
        Schema::table('merlo_cortes', function (Blueprint $table) {
            $table->dropColumn(['efectivo', 'desglose']);
        });
    }
};
