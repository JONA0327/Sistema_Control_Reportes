<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contratos', function (Blueprint $table) {
            // turismo | compras | americano — clasifica el contrato para
            // separar ingresos/egresos: turismo y compras caen en México
            // (categoria los distingue), americano cae en USA.
            $table->string('tipo')->default('turismo')->after('cliente_nombre');
        });
    }

    public function down(): void
    {
        Schema::table('contratos', function (Blueprint $table) {
            $table->dropColumn('tipo');
        });
    }
};
