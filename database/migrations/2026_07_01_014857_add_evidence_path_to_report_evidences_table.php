<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('report_evidences', function (Blueprint $table) {
            $table->string('evidence_path')->nullable()->after('evidence_blob');
        });
    }

    public function down(): void
    {
        Schema::table('report_evidences', function (Blueprint $table) {
            $table->dropColumn('evidence_path');
        });
    }
};
