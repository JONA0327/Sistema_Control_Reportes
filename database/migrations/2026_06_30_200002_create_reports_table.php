<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bus_id')->constrained('buses')->restrictOnDelete();
            $table->binary('audio')->nullable();
            $table->text('transcription')->nullable();
            $table->string('titulo');
            $table->string('description')->nullable();
            $table->string('status');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('notified_to_agency_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
