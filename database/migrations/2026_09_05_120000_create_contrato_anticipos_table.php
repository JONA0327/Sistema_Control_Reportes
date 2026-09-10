<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Anticipos formalizados del contrato. Cada anticipo tiene su propio
     * folio (ANT-{contrato.folio}-{seq}), archivo de evidencia opcional
     * y comprobante PDF descargable. Un contrato puede tener varios
     * anticipos (anticipo inicial + complemento, por ejemplo); la columna
     * legacy `contratos.anticipo` se conserva intacta para los contratos
     * que ya existen antes de este cambio.
     */
    public function up(): void
    {
        Schema::create('contrato_anticipos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrato_id')->constrained()->cascadeOnDelete();
            $table->string('folio')->unique(); // ANT-CT-00042-001
            $table->decimal('monto', 10, 2);
            $table->date('fecha_anticipo');
            $table->string('metodo_pago')->nullable();
            $table->string('evidencia_path')->nullable();
            $table->string('evidencia_nombre_original')->nullable();
            $table->text('notas')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->index(['contrato_id', 'fecha_anticipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contrato_anticipos');
    }
};
