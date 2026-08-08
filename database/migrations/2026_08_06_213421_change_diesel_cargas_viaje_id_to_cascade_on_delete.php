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
        Schema::table('diesel_cargas', function (Blueprint $table) {
            $table->dropForeign(['viaje_id']);
            $table->foreign('viaje_id')->references('id')->on('viajes')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diesel_cargas', function (Blueprint $table) {
            $table->dropForeign(['viaje_id']);
            $table->foreign('viaje_id')->references('id')->on('viajes')->restrictOnDelete();
        });
    }
};
