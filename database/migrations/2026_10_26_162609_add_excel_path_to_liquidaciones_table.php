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
        Schema::table('liquidaciones', function (Blueprint $table) {
            // Hoja de liquidación en Excel que entrega el operador en físico/
            // digital cuando no captura la liquidación en el sistema. Se
            // importa para llenar km, diésel y gastos, y queda guardada aquí
            // como respaldo de lo que se usó para cerrar la liquidación.
            $table->string('excel_path')->nullable()->after('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('liquidaciones', function (Blueprint $table) {
            $table->dropColumn('excel_path');
        });
    }
};
