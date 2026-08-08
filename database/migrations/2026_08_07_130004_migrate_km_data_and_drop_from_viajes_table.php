<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $viajes = DB::table('viajes')->select('id', 'km_inicial', 'km_final')->get();

        foreach ($viajes as $viaje) {
            $existing = DB::table('liquidaciones')->where('viaje_id', $viaje->id)->first();

            if ($existing) {
                DB::table('liquidaciones')->where('viaje_id', $viaje->id)->update([
                    'km_inicial' => $viaje->km_inicial,
                    'km_final'   => $viaje->km_final,
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('liquidaciones')->insert([
                    'viaje_id'   => $viaje->id,
                    'km_inicial' => $viaje->km_inicial,
                    'km_final'   => $viaje->km_final,
                    'estado'     => 'abierta',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        Schema::table('viajes', function (Blueprint $table) {
            $table->dropColumn(['km_inicial', 'km_final', 'km_total']);
        });
    }

    public function down(): void
    {
        Schema::table('viajes', function (Blueprint $table) {
            $table->decimal('km_inicial', 10, 2)->nullable()->after('recorridos');
            $table->decimal('km_final', 10, 2)->nullable()->after('km_inicial');
            $table->decimal('km_total', 10, 2)->nullable()->after('km_final');
        });
    }
};
