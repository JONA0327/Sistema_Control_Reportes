<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Cierre de viaje (solo contratos de compras): hasta cerrarlo, sus egresos
    // e ingresos automáticos no entran a ingresos_egresos.
    public function up(): void
    {
        Schema::table('viajes', function (Blueprint $table) {
            $table->timestamp('cerrado_at')->nullable();
            $table->foreignId('cerrado_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('viajes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cerrado_by');
            $table->dropColumn('cerrado_at');
        });
    }
};
