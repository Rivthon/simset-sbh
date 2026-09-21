<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('units', function (Blueprint $table): void {
            if (! Schema::hasColumn('units', 'code')) {
                $table->string('code')->nullable()->unique()->after('name');
            }
        });

        $this->dropIndexIfExists('categories', 'categories_code_unique');
        $this->dropIndexIfExists('locations', 'locations_code_unique');
        $this->dropColumnsIfExist('categories', ['code']);
        $this->dropColumnsIfExist('locations', ['code']);

        $this->normalizeCoreUnits();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table): void {
            if (! Schema::hasColumn('categories', 'code')) {
                $table->string('code')->nullable()->unique()->after('unit_id');
            }
        });

        Schema::table('locations', function (Blueprint $table): void {
            if (! Schema::hasColumn('locations', 'code')) {
                $table->string('code')->nullable()->unique()->after('name');
            }
        });
    }

    private function normalizeCoreUnits(): void
    {
        $units = [
            'BID' => 'Laboran Kebidanan',
            'FAR' => 'Laboran Farmasi',
            'GZI' => 'Laboran Gizi',
        ];

        foreach ($units as $code => $name) {
            $unit = DB::table('units')
                ->where('code', $code)
                ->orWhere('name', $name)
                ->orWhere('name', str_replace('Laboran', 'Laboratorium', $name))
                ->first();

            if ($unit) {
                DB::table('units')->where('id', $unit->id)->update([
                    'name' => $name,
                    'code' => $code,
                    'status' => 'active',
                ]);

                continue;
            }

            DB::table('units')->insert([
                'name' => $name,
                'code' => $code,
                'status' => 'active',
            ]);
        }
    }

    private function dropColumnsIfExist(string $tableName, array $columns): void
    {
        $existingColumns = array_values(array_filter(
            $columns,
            fn (string $column): bool => Schema::hasColumn($tableName, $column),
        ));

        if ($existingColumns === []) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) use ($existingColumns): void {
            $table->dropColumn($existingColumns);
        });
    }

    private function dropIndexIfExists(string $tableName, string $indexName): void
    {
        $exists = DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', $tableName)
            ->where('INDEX_NAME', $indexName)
            ->exists();

        if (! $exists) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) use ($indexName): void {
            $table->dropUnique($indexName);
        });
    }
};
