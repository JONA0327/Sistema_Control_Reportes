<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contrato_archivos', function (Blueprint $table) {
            $table->dropForeign(['contrato_id']);
            $table->foreignId('contrato_id')->nullable()->change();
            $table->foreign('contrato_id')->references('id')->on('contratos')->nullOnDelete();

            $table->string('cliente_nombre')->nullable()->after('contrato_id');
            $table->string('descripcion')->nullable()->after('nombre_original');
        });
    }

    public function down(): void
    {
        Schema::table('contrato_archivos', function (Blueprint $table) {
            $table->dropColumn(['cliente_nombre', 'descripcion']);
            $table->dropForeign(['contrato_id']);
            $table->foreignId('contrato_id')->nullable(false)->change();
            $table->foreign('contrato_id')->references('id')->on('contratos')->cascadeOnDelete();
        });
    }
};
