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
        Schema::table('ingresos_egresos', function (Blueprint $table) {
            // Turismo / Compras / Americano: separado de `categoria` (que
            // describe el tipo de movimiento: Anticipo, Combustible, Gastos
            // de operador, etc). Permite ver cuánto entró y salió por tipo
            // de viaje aunque el movimiento sea un egreso (viáticos, diésel).
            // Null cuando el movimiento no está ligado a un contrato/viaje
            // clasificado (movimientos manuales sin viaje asociado).
            $table->string('clasificacion')->nullable()->after('categoria');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ingresos_egresos', function (Blueprint $table) {
            $table->dropColumn('clasificacion');
        });
    }
};
