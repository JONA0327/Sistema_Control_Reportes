<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Gastos de liquidación capturados por administración en lugar del operador.
    public function up(): void
    {
        Schema::table('liquidacion_gastos', function (Blueprint $table) {
            $table->foreignId('capturado_por')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('liquidacion_gastos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('capturado_por');
        });
    }
};
