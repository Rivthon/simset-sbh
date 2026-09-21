<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $indexName = 'inventory_checks_unit_date_unique';

    public function up(): void
    {
        if (! Schema::hasTable('inventory_checks')) {
            return;
        }

        $this->mergeDuplicateChecks();

        Schema::table('inventory_checks', function (Blueprint $table): void {
            $table->unique(['unit_id', 'tanggal_pemeriksaan'], $this->indexName);
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('inventory_checks')) {
            return;
        }

        Schema::table('inventory_checks', function (Blueprint $table): void {
            $table->dropUnique($this->indexName);
        });
    }

    private function mergeDuplicateChecks(): void
    {
        $duplicateGroups = DB::table('inventory_checks')
            ->select('unit_id', 'tanggal_pemeriksaan', DB::raw('COUNT(*) as total'))
            ->groupBy('unit_id', 'tanggal_pemeriksaan')
            ->having('total', '>', 1)
            ->get();

        foreach ($duplicateGroups as $group) {
            $checks = DB::table('inventory_checks')
                ->leftJoin('inventory_check_items', 'inventory_checks.id', '=', 'inventory_check_items.inventory_check_id')
                ->select(
                    'inventory_checks.id',
                    DB::raw('COUNT(inventory_check_items.id) as item_total'),
                    DB::raw('SUM(CASE WHEN inventory_check_items.jumlah_aktual IS NOT NULL THEN 1 ELSE 0 END) as checked_total')
                )
                ->where('inventory_checks.unit_id', $group->unit_id)
                ->where('inventory_checks.tanggal_pemeriksaan', $group->tanggal_pemeriksaan)
                ->groupBy('inventory_checks.id')
                ->orderByDesc('checked_total')
                ->orderByDesc('item_total')
                ->orderByDesc('inventory_checks.id')
                ->get();

            $keepId = (int) $checks->first()->id;

            foreach ($checks->pluck('id')->map(fn ($id): int => (int) $id)->filter(fn (int $id): bool => $id !== $keepId) as $deleteId) {
                $items = DB::table('inventory_check_items')
                    ->where('inventory_check_id', $deleteId)
                    ->get();

                foreach ($items as $item) {
                    $targetExists = DB::table('inventory_check_items')
                        ->where('inventory_check_id', $keepId)
                        ->where('asset_id', $item->asset_id)
                        ->exists();

                    if ($targetExists) {
                        DB::table('inventory_check_items')->where('id', $item->id)->delete();
                        continue;
                    }

                    DB::table('inventory_check_items')
                        ->where('id', $item->id)
                        ->update(['inventory_check_id' => $keepId]);
                }

                DB::table('inventory_checks')->where('id', $deleteId)->delete();
            }
        }
    }
};
