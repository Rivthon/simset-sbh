<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('assets') || ! Schema::hasColumn('assets', 'kondisi_aset')) {
            return;
        }

        DB::table('assets')
            ->where('kondisi_aset', 'nonaktif')
            ->update(['kondisi_aset' => 'rusak']);

        DB::table('assets')
            ->whereNotIn('kondisi_aset', ['baik', 'sedang', 'rusak', 'hilang'])
            ->update(['kondisi_aset' => 'baik']);
    }

    public function down(): void
    {
        // No reliable way to infer which assets were previously nonaktif.
    }
};
