<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos', function (Blueprint $table) {
            $table->id();
            $table->string('modulo'); // anticipos, inventario, reportes, contratos
            $table->string('accion'); // creado, editado, eliminado, cancelado, estado_cambiado...
            $table->string('descripcion');
            $table->text('motivo')->nullable(); // razón, cuando aplica (cancelación, cambio, etc.)
            $table->nullableMorphs('movible'); // referencia opcional al registro afectado
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['modulo', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos');
    }
};
