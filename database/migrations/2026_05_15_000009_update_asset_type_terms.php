<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('assets') || ! Schema::hasColumn('assets', 'asset_type')) {
            return;
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE assets MODIFY asset_type ENUM('alat', 'bahan', 'peralatan', 'perlengkapan') NOT NULL DEFAULT 'peralatan'");
        }

        DB::table('assets')->where('asset_type', 'alat')->update(['asset_type' => 'peralatan']);
        DB::table('assets')->where('asset_type', 'bahan')->update(['asset_type' => 'perlengkapan']);

        DB::table('assets')
            ->where('asset_code', 'like', 'AST-%-ALT-%')
            ->update(['asset_code' => DB::raw("REPLACE(asset_code, '-ALT-', '-PRT-')")]);

        DB::table('assets')
            ->where('asset_code', 'like', 'AST-%-BHN-%')
            ->update(['asset_code' => DB::raw("REPLACE(asset_code, '-BHN-', '-PLK-')")]);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE assets MODIFY asset_type ENUM('peralatan', 'perlengkapan') NOT NULL DEFAULT 'peralatan'");
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('assets') || ! Schema::hasColumn('assets', 'asset_type')) {
            return;
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE assets MODIFY asset_type ENUM('alat', 'bahan', 'peralatan', 'perlengkapan') NOT NULL DEFAULT 'alat'");
        }

        DB::table('assets')->where('asset_type', 'peralatan')->update(['asset_type' => 'alat']);
        DB::table('assets')->where('asset_type', 'perlengkapan')->update(['asset_type' => 'bahan']);

        DB::table('assets')
            ->where('asset_code', 'like', 'AST-%-PRT-%')
            ->update(['asset_code' => DB::raw("REPLACE(asset_code, '-PRT-', '-ALT-')")]);

        DB::table('assets')
            ->where('asset_code', 'like', 'AST-%-PLK-%')
            ->update(['asset_code' => DB::raw("REPLACE(asset_code, '-PLK-', '-BHN-')")]);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE assets MODIFY asset_type ENUM('alat', 'bahan') NOT NULL DEFAULT 'alat'");
        }
    }
};
