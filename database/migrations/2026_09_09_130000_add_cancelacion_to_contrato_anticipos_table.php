<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contrato_anticipos', function (Blueprint $table) {
            $table->timestamp('cancelado_at')->nullable()->after('notas');
            $table->string('motivo_cancelacion')->nullable()->after('cancelado_at');
            $table->foreignId('cancelado_por')->nullable()->after('motivo_cancelacion')
                ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('contrato_anticipos', function (Blueprint $table) {
            $table->dropForeign(['cancelado_por']);
            $table->dropColumn(['cancelado_at', 'motivo_cancelacion', 'cancelado_por']);
        });
    }
};
