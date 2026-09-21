<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        $this->optimizeColumnDefinitions();
        $this->addRecommendedIndexes();
        $this->addRecommendedConstraints();
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        $this->addIndexIfMissing('categories', 'categories_unit_id_foreign', ['unit_id']);
        $this->addIndexIfMissing('locations', 'locations_unit_id_foreign', ['unit_id']);
        $this->addIndexIfMissing('containers', 'containers_unit_id_foreign', ['unit_id']);
        $this->addIndexIfMissing('assets', 'assets_unit_id_foreign', ['unit_id']);

        $this->dropIndexIfExists('categories', 'categories_unit_status_name_index');
        $this->dropIndexIfExists('locations', 'locations_unit_status_name_index');
        $this->dropIndexIfExists('containers', 'containers_unit_status_name_index');
        $this->dropIndexIfExists('assets', 'assets_unit_location_name_index');
        $this->dropIndexIfExists('assets', 'assets_unit_category_index');
        $this->dropIndexIfExists('assets', 'assets_unit_kondisi_index');
        $this->dropIndexIfExists('assets', 'assets_unit_legacy_inventory_code_index');
        $this->dropIndexIfExists('inventory_checks', 'inventory_checks_unit_status_index');
        $this->dropIndexIfExists('inventory_checks', 'inventory_checks_unit_semester_tahun_unique');
        $this->dropIndexIfExists('tool_replacement_requests', 'tool_replacements_code_nim_index');
        $this->dropIndexIfExists('locations', 'locations_unit_name_unique');
    }

    private function optimizeColumnDefinitions(): void
    {
        $this->statementIfColumnExists('users', 'name', 'ALTER TABLE users MODIFY name VARCHAR(100) NOT NULL');
        $this->statementIfColumnExists('users', 'username', 'ALTER TABLE users MODIFY username VARCHAR(50) NOT NULL');
        $this->statementIfColumnExists('users', 'email', 'ALTER TABLE users MODIFY email VARCHAR(100) NOT NULL');

        $this->statementIfColumnExists('units', 'name', 'ALTER TABLE units MODIFY name VARCHAR(100) NOT NULL');
        $this->statementIfColumnExists('units', 'code', 'ALTER TABLE units MODIFY code VARCHAR(20) NOT NULL');

        $this->statementIfColumnExists('categories', 'name', 'ALTER TABLE categories MODIFY name VARCHAR(100) NOT NULL');
        $this->statementIfColumnExists('locations', 'name', 'ALTER TABLE locations MODIFY name VARCHAR(100) NOT NULL');

        $this->statementIfColumnExists('containers', 'name', 'ALTER TABLE containers MODIFY name VARCHAR(100) NOT NULL');
        $this->statementIfColumnExists('containers', 'code', 'ALTER TABLE containers MODIFY code VARCHAR(20) NOT NULL');
        $this->statementIfColumnExists('containers', 'qr_code', 'ALTER TABLE containers MODIFY qr_code VARCHAR(150) NULL');

        $this->statementIfColumnExists('assets', 'asset_code', 'ALTER TABLE assets MODIFY asset_code VARCHAR(30) NOT NULL');
        $this->statementIfColumnExists('assets', 'legacy_inventory_code', 'ALTER TABLE assets MODIFY legacy_inventory_code VARCHAR(30) NULL');
        $this->statementIfColumnExists('assets', 'qr_code', 'ALTER TABLE assets MODIFY qr_code VARCHAR(150) NULL');
        $this->statementIfColumnExists('assets', 'name', 'ALTER TABLE assets MODIFY name VARCHAR(150) NOT NULL');
        $this->statementIfColumnExists('assets', 'quantity', 'ALTER TABLE assets MODIFY quantity SMALLINT UNSIGNED NOT NULL DEFAULT 1');
        $this->statementIfColumnExists('assets', 'satuan', "ALTER TABLE assets MODIFY satuan ENUM('unit','pcs','set','buah','lembar','pasang','box','lainnya') NOT NULL DEFAULT 'unit'");
        $this->statementIfColumnExists('assets', 'kondisi_aset', "ALTER TABLE assets MODIFY kondisi_aset ENUM('baik','sedang','rusak','hilang') NOT NULL DEFAULT 'baik'");

        $this->statementIfColumnExists('inventory_checks', 'check_code', 'ALTER TABLE inventory_checks MODIFY check_code VARCHAR(30) NOT NULL');
        $this->statementIfColumnExists('inventory_checks', 'periode', 'ALTER TABLE inventory_checks MODIFY periode CHAR(9) NULL');
        $this->statementIfColumnExists('inventory_checks', 'semester', "ALTER TABLE inventory_checks MODIFY semester ENUM('Ganjil','Genap') NOT NULL");
        $this->statementIfColumnExists('inventory_checks', 'tahun_akademik', 'ALTER TABLE inventory_checks MODIFY tahun_akademik CHAR(9) NOT NULL');
        $this->statementIfColumnExists('inventory_checks', 'tanggal_pemeriksaan', 'ALTER TABLE inventory_checks MODIFY tanggal_pemeriksaan DATE NOT NULL');

        foreach (['jumlah_sistem', 'jumlah_aktual', 'jumlah_baik', 'jumlah_sedang', 'jumlah_rusak', 'jumlah_hilang'] as $column) {
            $nullable = $column === 'jumlah_aktual' ? 'NULL' : 'NOT NULL DEFAULT 0';
            $default = $column === 'jumlah_sistem' ? 'NOT NULL DEFAULT 1' : $nullable;

            $this->statementIfColumnExists(
                'inventory_check_items',
                $column,
                "ALTER TABLE inventory_check_items MODIFY {$column} SMALLINT UNSIGNED {$default}",
            );
        }

        $this->statementIfColumnExists('inventory_check_items', 'hasil_pemeriksaan', "ALTER TABLE inventory_check_items MODIFY hasil_pemeriksaan ENUM('pending','sesuai','tidak_sesuai') NOT NULL DEFAULT 'pending'");

        $this->statementIfColumnExists('tool_replacement_requests', 'replacement_code', 'ALTER TABLE tool_replacement_requests MODIFY replacement_code VARCHAR(30) NULL');
        $this->statementIfColumnExists('tool_replacement_requests', 'student_name', 'ALTER TABLE tool_replacement_requests MODIFY student_name VARCHAR(100) NOT NULL');
        $this->statementIfColumnExists('tool_replacement_requests', 'student_nim', 'ALTER TABLE tool_replacement_requests MODIFY student_nim VARCHAR(30) NULL');
        $this->statementIfColumnExists('tool_replacement_requests', 'student_semester', 'ALTER TABLE tool_replacement_requests MODIFY student_semester TINYINT UNSIGNED NULL');
        $this->statementIfColumnExists('tool_replacement_requests', 'prodi_kelas', 'ALTER TABLE tool_replacement_requests MODIFY prodi_kelas VARCHAR(150) NULL');
        $this->statementIfColumnExists('tool_replacement_requests', 'practicum_name', 'ALTER TABLE tool_replacement_requests MODIFY practicum_name VARCHAR(150) NOT NULL');
        $this->statementIfColumnExists('tool_replacement_requests', 'replacement_quantity', 'ALTER TABLE tool_replacement_requests MODIFY replacement_quantity SMALLINT UNSIGNED NOT NULL');
        $this->statementIfColumnExists('tool_replacement_requests', 'whatsapp_number', 'ALTER TABLE tool_replacement_requests MODIFY whatsapp_number VARCHAR(15) NULL');
        $this->statementIfColumnExists('tool_replacement_requests', 'damage_photo', 'ALTER TABLE tool_replacement_requests MODIFY damage_photo VARCHAR(150) NULL');
    }

    private function addRecommendedIndexes(): void
    {
        $this->addIndexIfMissing('categories', 'categories_unit_status_name_index', ['unit_id', 'status', 'name']);
        $this->addIndexIfMissing('locations', 'locations_unit_status_name_index', ['unit_id', 'status', 'name']);
        $this->addIndexIfMissing('containers', 'containers_unit_status_name_index', ['unit_id', 'status', 'name']);
        $this->addIndexIfMissing('assets', 'assets_unit_location_name_index', ['unit_id', 'location_id', 'name']);
        $this->addIndexIfMissing('assets', 'assets_unit_category_index', ['unit_id', 'category_id']);
        $this->addIndexIfMissing('assets', 'assets_unit_kondisi_index', ['unit_id', 'kondisi_aset']);
        $this->addIndexIfMissing('assets', 'assets_unit_legacy_inventory_code_index', ['unit_id', 'legacy_inventory_code']);
        $this->addIndexIfMissing('inventory_checks', 'inventory_checks_unit_status_index', ['unit_id', 'status']);
        $this->addIndexIfMissing('tool_replacement_requests', 'tool_replacements_code_nim_index', ['replacement_code', 'student_nim']);
    }

    private function addRecommendedConstraints(): void
    {
        $this->addUniqueIfMissing('locations', 'locations_unit_name_unique', ['unit_id', 'name']);
        $this->addUniqueIfMissing('inventory_checks', 'inventory_checks_unit_semester_tahun_unique', ['unit_id', 'semester', 'tahun_akademik']);
    }

    private function statementIfColumnExists(string $table, string $column, string $statement): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
            return;
        }

        DB::statement($statement);
    }

    private function addIndexIfMissing(string $table, string $index, array $columns): void
    {
        if (! Schema::hasTable($table) || $this->indexExists($table, $index)) {
            return;
        }

        DB::statement(sprintf(
            'ALTER TABLE %s ADD INDEX %s (%s)',
            $table,
            $index,
            implode(', ', $columns),
        ));
    }

    private function addUniqueIfMissing(string $table, string $index, array $columns): void
    {
        if (! Schema::hasTable($table) || $this->indexExists($table, $index)) {
            return;
        }

        DB::statement(sprintf(
            'ALTER TABLE %s ADD UNIQUE %s (%s)',
            $table,
            $index,
            implode(', ', $columns),
        ));
    }

    private function dropIndexIfExists(string $table, string $index): void
    {
        if (! Schema::hasTable($table) || ! $this->indexExists($table, $index)) {
            return;
        }

        DB::statement(sprintf('ALTER TABLE %s DROP INDEX %s', $table, $index));
    }

    private function indexExists(string $table, string $index): bool
    {
        return DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', $table)
            ->where('INDEX_NAME', $index)
            ->exists();
    }
};
