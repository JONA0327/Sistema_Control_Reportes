<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('liquidaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('viaje_id')->unique()->constrained('viajes')->cascadeOnDelete();
            $table->decimal('km_inicial', 10, 2)->nullable();
            $table->decimal('km_final', 10, 2)->nullable();
            $table->string('estado')->default('abierta');
            $table->timestamp('cerrada_at')->nullable();
            $table->foreignId('cerrada_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('liquidaciones');
    }
};
