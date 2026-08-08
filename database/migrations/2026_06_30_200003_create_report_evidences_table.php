<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_evidences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->cascadeOnDelete();
            $table->binary('evidence_blob')->nullable();
            $table->string('evidence_type');
            $table->string('description')->nullable();
            $table->timestamp('uploaded_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_evidences');
    }
};
