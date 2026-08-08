<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->foreignId('responsable_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            $table->timestamp('devuelto_at')->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->dropConstrainedForeignId('responsable_id');
            $table->dropColumn('devuelto_at');
        });
    }
};
