<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Corte diario de paquetería de Merlo Web. `fecha` es la llave de idempotencia:
    // reenviar el corte de un día actualiza su ingreso, no lo duplica.
    public function up(): void
    {
        Schema::create('merlo_cortes_paqueteria', function (Blueprint $table) {
            $table->id();
            $table->date('fecha')->unique();
            $table->unsignedInteger('payments_count');
            $table->decimal('total_amount', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('merlo_cortes_paqueteria');
    }
};
