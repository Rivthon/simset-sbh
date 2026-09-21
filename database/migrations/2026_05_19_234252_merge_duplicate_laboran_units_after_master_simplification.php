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
        if (! Schema::hasTable('units')) {
            return;
        }

        $pairs = [
            'Laboran Kebidanan' => 'Laboratorium Kebidanan',
            'Laboran Farmasi' => 'Laboratorium Farmasi',
            'Laboran Gizi' => 'Laboratorium Gizi',
        ];

        foreach ($pairs as $oldName => $newName) {
            $targetId = DB::table('units')->where('name', $newName)->value('id');
            $oldId = DB::table('units')->where('name', $oldName)->value('id');

            if (! $targetId || ! $oldId || $targetId === $oldId) {
                continue;
            }

            foreach (['users', 'assets', 'categories', 'locations', 'inventory_checks'] as $tableName) {
                if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'unit_id')) {
                    DB::table($tableName)->where('unit_id', $oldId)->update(['unit_id' => $targetId]);
                }
            }

            DB::table('units')->where('id', $oldId)->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
