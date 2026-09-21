<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('assets') && Schema::hasColumn('assets', 'location_id')) {
            DB::statement('ALTER TABLE assets MODIFY location_id BIGINT UNSIGNED NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('assets') || ! Schema::hasColumn('assets', 'location_id')) {
            return;
        }

        if (DB::table('assets')->whereNull('location_id')->exists()) {
            return;
        }

        DB::statement('ALTER TABLE assets MODIFY location_id BIGINT UNSIGNED NOT NULL');
    }
};
