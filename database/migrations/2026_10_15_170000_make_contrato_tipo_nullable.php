<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // `tipo` se agregó con default 'turismo', así que los contratos anteriores
    // figuran como turismo sin serlo necesariamente. Los creados antes de que
    // existiera el campo (2026-10-14) pasan a "sin tipo" para clasificarlos a
    // mano al editarlos; los nuevos ya eligen su tipo al crearse.
    public function up(): void
    {
        Schema::table('contratos', function (Blueprint $table) {
            $table->string('tipo')->nullable()->default(null)->change();
        });

        DB::table('contratos')->where('created_at', '<', '2026-10-14')->update(['tipo' => null]);
    }

    public function down(): void
    {
        DB::table('contratos')->whereNull('tipo')->update(['tipo' => 'turismo']);

        Schema::table('contratos', function (Blueprint $table) {
            $table->string('tipo')->nullable(false)->default('turismo')->change();
        });
    }
};
