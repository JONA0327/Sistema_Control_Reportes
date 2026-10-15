<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // El correo principal ahora se comparte por camión (varios
            // operadores, mismo correo), así que deja de ser único. El
            // login pasa a ser por username. El correo secundario es
            // personal de cada operador y es donde llega su código de
            // recuperación de contraseña, para que no lo vea el compañero
            // que comparte el correo principal.
            $table->dropUnique(['email']);
            $table->string('correo_secundario')->nullable()->unique()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['correo_secundario']);
            $table->dropColumn('correo_secundario');
            $table->unique('email');
        });
    }
};
