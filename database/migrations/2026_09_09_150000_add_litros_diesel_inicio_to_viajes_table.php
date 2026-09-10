<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('viajes', function (Blueprint $table) {
            $table->decimal('litros_diesel_inicio', 10, 2)->nullable()->after('gasto_diesel_inicio');
            $table->decimal('costo_litro_diesel_inicio', 10, 2)->nullable()->after('litros_diesel_inicio');
        });
    }

    public function down(): void
    {
        Schema::table('viajes', function (Blueprint $table) {
            $table->dropColumn(['litros_diesel_inicio', 'costo_litro_diesel_inicio']);
        });
    }
};
