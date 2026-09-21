<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('inventory_checks')) {
            return;
        }

        DB::table('inventory_checks')
            ->whereNotNull('tahun_akademik')
            ->update(['periode' => DB::raw('tahun_akademik')]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('inventory_checks')) {
            return;
        }

        DB::table('inventory_checks')
            ->whereNotNull('tahun_akademik')
            ->update(['periode' => DB::raw("TRIM(CONCAT(COALESCE(semester, ''), ' ', COALESCE(tahun_akademik, '')))")]);
    }
};
