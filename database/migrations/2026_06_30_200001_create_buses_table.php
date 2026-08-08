<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buses', function (Blueprint $table) {
            $table->id();
            $table->integer('num_bus');
            $table->foreignId('operator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buses');
    }
};
