<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('diesel_cargas', function (Blueprint $table) {
            $table->decimal('costo_litro', 10, 2)->nullable()->after('litros');
        });
    }

    public function down(): void
    {
        Schema::table('diesel_cargas', function (Blueprint $table) {
            $table->dropColumn('costo_litro');
        });
    }
};
