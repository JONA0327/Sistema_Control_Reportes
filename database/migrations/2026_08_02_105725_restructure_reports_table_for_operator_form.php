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
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn('titulo');
            $table->foreignId('user_id')->after('bus_id')->constrained('users')->restrictOnDelete();
            $table->unsignedInteger('km_actual')->after('user_id');
            $table->json('categorias')->after('km_actual');
            $table->string('urgencia')->after('categorias');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'km_actual', 'categorias', 'urgencia']);
            $table->string('titulo')->after('bus_id');
        });
    }
};
