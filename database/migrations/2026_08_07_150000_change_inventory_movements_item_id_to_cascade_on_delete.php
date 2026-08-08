<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->dropForeign(['item_id']);
        });

        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->foreign('item_id')->references('id')->on('inventory_items')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->dropForeign(['item_id']);
        });

        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->foreign('item_id')->references('id')->on('inventory_items')->restrictOnDelete();
        });
    }
};
