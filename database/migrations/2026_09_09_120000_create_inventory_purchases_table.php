<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('inventory_items')->cascadeOnDelete();
            $table->unsignedInteger('quantity');
            $table->decimal('precio', 10, 2);
            $table->string('comprobante_path');
            $table->string('estado')->default('pendiente');
            $table->string('pais')->nullable();
            $table->string('notas')->nullable();
            $table->string('motivo_rechazo')->nullable();
            $table->foreignId('solicitado_por')->constrained('users')->restrictOnDelete();
            $table->foreignId('revisado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('revisado_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_purchases');
    }
};
