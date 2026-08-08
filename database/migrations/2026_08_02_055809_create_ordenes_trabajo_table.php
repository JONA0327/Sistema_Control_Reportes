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
        Schema::create('ordenes_trabajo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->unique()->constrained('reports')->cascadeOnDelete();
            $table->foreignId('mecanico_id')->constrained('users')->restrictOnDelete();
            $table->timestamp('recibido_at');
            $table->string('tipo_atencion');
            $table->string('falla_confirmada')->nullable();
            $table->json('subsistemas')->nullable();
            $table->string('codigo_falla')->nullable();
            $table->text('diagnostico')->nullable();
            $table->string('proveedor_externo')->nullable();
            $table->string('folio_proveedor')->nullable();
            $table->json('motivo_externo')->nullable();
            $table->timestamp('fecha_promesa_entrega')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordenes_trabajo');
    }
};
