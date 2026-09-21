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
        $this->dropIndexIfExists('units', 'units_code_unique');
        $this->dropIndexIfExists('categories', 'categories_code_unique');
        $this->dropIndexIfExists('locations', 'locations_code_unique');

        $this->dropColumnsIfExist('units', ['code']);
        $this->dropColumnsIfExist('categories', ['code', 'description']);
        $this->dropColumnsIfExist('locations', ['code', 'description']);
        $this->dropColumnsIfExist('containers', ['description']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            if (! Schema::hasColumn('units', 'code')) {
                $table->string('code')->nullable()->unique()->after('name');
            }
        });

        Schema::table('categories', function (Blueprint $table) {
            if (! Schema::hasColumn('categories', 'code')) {
                $table->string('code')->nullable()->unique()->after('unit_id');
            }

            if (! Schema::hasColumn('categories', 'description')) {
                $table->text('description')->nullable()->after('code');
            }
        });

        Schema::table('locations', function (Blueprint $table) {
            if (! Schema::hasColumn('locations', 'code')) {
                $table->string('code')->nullable()->unique()->after('name');
            }

            if (! Schema::hasColumn('locations', 'description')) {
                $table->text('description')->nullable()->after('code');
            }
        });

        Schema::table('containers', function (Blueprint $table) {
            if (! Schema::hasColumn('containers', 'description')) {
                $table->text('description')->nullable()->after('qr_code');
            }
        });
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
        $databaseName = DB::getDatabaseName();
        $exists = DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', $databaseName)
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
