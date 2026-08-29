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
        Schema::table('viajes', function (Blueprint $table) {
            $table->foreignId('contrato_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });

        Schema::table('viajes', function (Blueprint $table) {
            $table->unsignedBigInteger('bus_id')->nullable()->change();
            $table->unsignedBigInteger('operador_id')->nullable()->change();
            $table->string('origen')->nullable()->change();
            $table->string('destino')->nullable()->change();
            $table->text('recorridos')->nullable()->change();
            $table->decimal('gastos_entregados', 10, 2)->nullable()->change();
            $table->decimal('gasto_diesel_inicio', 10, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('viajes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('contrato_id');
        });

        Schema::table('viajes', function (Blueprint $table) {
            $table->unsignedBigInteger('bus_id')->nullable(false)->change();
            $table->unsignedBigInteger('operador_id')->nullable(false)->change();
            $table->string('origen')->nullable(false)->change();
            $table->string('destino')->nullable(false)->change();
            $table->text('recorridos')->nullable(false)->change();
            $table->decimal('gastos_entregados', 10, 2)->nullable(false)->change();
            $table->decimal('gasto_diesel_inicio', 10, 2)->nullable(false)->change();
        });
    }
};
