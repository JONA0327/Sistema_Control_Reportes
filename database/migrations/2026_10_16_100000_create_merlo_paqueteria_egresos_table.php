<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Egresos del corte diario de paquetería (concepto y cantidad). merlo_id es la
    // llave de idempotencia: reenviar el corte actualiza, no duplica.
    public function up(): void
    {
        Schema::create('merlo_paqueteria_egresos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('merlo_id')->unique();
            $table->date('fecha')->index();
            $table->string('concepto');
            $table->decimal('monto', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('merlo_paqueteria_egresos');
    }
};
