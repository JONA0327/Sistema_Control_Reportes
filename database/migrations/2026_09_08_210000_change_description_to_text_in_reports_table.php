<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE reports MODIFY description TEXT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE reports MODIFY description VARCHAR(255) NULL');
    }
};
