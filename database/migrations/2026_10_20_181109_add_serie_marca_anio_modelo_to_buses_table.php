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
        Schema::table('buses', function (Blueprint $table) {
            $table->string('serie')->nullable()->after('placa');
            $table->string('marca')->nullable()->after('serie');
            $table->unsignedSmallInteger('anio')->nullable()->after('marca');
            $table->string('modelo')->nullable()->after('anio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('buses', function (Blueprint $table) {
            $table->dropColumn(['serie', 'marca', 'anio', 'modelo']);
        });
    }
};
