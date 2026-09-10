<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->string('frecuencia')->nullable()->after('urgencia');
            $table->json('condiciones')->nullable()->after('frecuencia');
            $table->json('sintomas')->nullable()->after('condiciones');
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn(['frecuencia', 'condiciones', 'sintomas']);
        });
    }
};
