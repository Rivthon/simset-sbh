<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $unitMap = [
        'LABKEB' => ['code' => 'BID', 'name' => 'Laboran Kebidanan'],
        'LABFAR' => ['code' => 'FAR', 'name' => 'Laboran Farmasi'],
        'LABGIZ' => ['code' => 'GZI', 'name' => 'Laboran Gizi'],
    ];

    public function up(): void
    {
        if (! Schema::hasTable('units')) {
            return;
        }

        DB::transaction(function (): void {
            foreach ($this->unitMap as $oldCode => $target) {
                $targetId = DB::table('units')->where('code', $target['code'])->value('id');

                if (! $targetId) {
                    $data = [
                        'code' => $target['code'],
                        'name' => $target['name'],
                        'status' => 'active',
                    ];

                    if (Schema::hasColumn('units', 'description')) {
                        $data['description'] = $target['name'];
                    }

                    if (Schema::hasColumn('units', 'created_at')) {
                        $data['created_at'] = now();
                    }

                    if (Schema::hasColumn('units', 'updated_at')) {
                        $data['updated_at'] = now();
                    }

                    $targetId = DB::table('units')->insertGetId($data);
                }

                $oldId = DB::table('units')->where('code', $oldCode)->value('id');

                if (! $oldId || (int) $oldId === (int) $targetId) {
                    continue;
                }

                $this->moveUnitReferences((int) $oldId, (int) $targetId);

                DB::table('units')->where('id', $oldId)->delete();
            }
        });
    }

    public function down(): void
    {
        DB::transaction(function (): void {
            foreach ($this->unitMap as $oldCode => $target) {
                if (DB::table('units')->where('code', $oldCode)->exists()) {
                    continue;
                }

                $data = [
                    'code' => $oldCode,
                    'name' => str_replace('Laboratorium', 'Laboran', $target['name']),
                    'status' => 'inactive',
                ];

                if (Schema::hasColumn('units', 'description')) {
                    $data['description'] = 'Unit lama sebelum digabung ke '.$target['code'];
                }

                if (Schema::hasColumn('units', 'created_at')) {
                    $data['created_at'] = now();
                }

                if (Schema::hasColumn('units', 'updated_at')) {
                    $data['updated_at'] = now();
                }

                DB::table('units')->insert($data);
            }
        });
    }

    private function moveUnitReferences(int $oldId, int $targetId): void
    {
        foreach ([
            'users',
            'locations',
            'assets',
            'inventory_checks',
            'categories',
        ] as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'unit_id')) {
                continue;
            }

            $data = ['unit_id' => $targetId];

            if (Schema::hasColumn($table, 'updated_at')) {
                $data['updated_at'] = now();
            }

            DB::table($table)
                ->where('unit_id', $oldId)
                ->update($data);
        }
    }
};
