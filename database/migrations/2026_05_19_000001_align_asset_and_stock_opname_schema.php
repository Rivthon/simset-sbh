<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            if (! Schema::hasColumn('assets', 'satuan')) {
                $table->string('satuan', 50)->default('unit')->after('quantity');
            }

            if (! Schema::hasColumn('assets', 'current_condition')) {
                $table->string('current_condition')->nullable()->after('condition');
            }

            if (! Schema::hasColumn('assets', 'status_aset')) {
                $table->string('status_aset')->nullable()->after('status');
            }
        });

        DB::table('assets')
            ->whereNull('current_condition')
            ->update(['current_condition' => DB::raw('`condition`')]);

        DB::table('assets')
            ->whereNull('status_aset')
            ->update(['status_aset' => DB::raw('`status`')]);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE inventory_checks MODIFY `status` ENUM('draft','ongoing','completed') NOT NULL DEFAULT 'draft'");
        }

        Schema::table('inventory_checks', function (Blueprint $table) {
            if (! Schema::hasColumn('inventory_checks', 'periode')) {
                $table->string('periode')->nullable()->after('check_code');
            }

            if (! Schema::hasColumn('inventory_checks', 'tahun_akademik')) {
                $table->string('tahun_akademik')->nullable()->after('academic_year');
            }

            if (! Schema::hasColumn('inventory_checks', 'tanggal_pemeriksaan')) {
                $table->date('tanggal_pemeriksaan')->nullable()->after('check_date');
            }

            if (! Schema::hasColumn('inventory_checks', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('checked_by')->constrained('users')->nullOnDelete();
            }
        });

        DB::table('inventory_checks')
            ->whereNull('periode')
            ->orderBy('id')
            ->get(['id', 'semester', 'academic_year'])
            ->each(function (object $check): void {
                DB::table('inventory_checks')
                    ->where('id', $check->id)
                    ->update(['periode' => trim(($check->semester ?? '').' '.($check->academic_year ?? ''))]);
            });

        DB::table('inventory_checks')
            ->whereNull('tahun_akademik')
            ->update(['tahun_akademik' => DB::raw('academic_year')]);

        DB::table('inventory_checks')
            ->whereNull('tanggal_pemeriksaan')
            ->update(['tanggal_pemeriksaan' => DB::raw('check_date')]);

        DB::table('inventory_checks')
            ->whereNull('created_by')
            ->update(['created_by' => DB::raw('checked_by')]);

        Schema::table('inventory_check_items', function (Blueprint $table) {
            if (! Schema::hasColumn('inventory_check_items', 'jumlah_sistem')) {
                $table->unsignedInteger('jumlah_sistem')->default(1)->after('system_quantity');
            }

            if (! Schema::hasColumn('inventory_check_items', 'jumlah_aktual')) {
                $table->unsignedInteger('jumlah_aktual')->nullable()->after('actual_quantity');
            }

            if (! Schema::hasColumn('inventory_check_items', 'jumlah_baik')) {
                $table->unsignedInteger('jumlah_baik')->default(0)->after('jumlah_aktual');
            }

            if (! Schema::hasColumn('inventory_check_items', 'jumlah_sedang')) {
                $table->unsignedInteger('jumlah_sedang')->default(0)->after('jumlah_baik');
            }

            if (! Schema::hasColumn('inventory_check_items', 'jumlah_rusak')) {
                $table->unsignedInteger('jumlah_rusak')->default(0)->after('jumlah_sedang');
            }

            if (! Schema::hasColumn('inventory_check_items', 'jumlah_hilang')) {
                $table->unsignedInteger('jumlah_hilang')->default(0)->after('jumlah_rusak');
            }

            if (! Schema::hasColumn('inventory_check_items', 'hasil_pemeriksaan')) {
                $table->string('hasil_pemeriksaan')->default('pending')->after('check_result');
            }

            if (! Schema::hasColumn('inventory_check_items', 'kondisi_fisik')) {
                $table->string('kondisi_fisik')->nullable()->after('condition');
            }
        });

        DB::table('inventory_check_items')
            ->where('jumlah_sistem', 1)
            ->update(['jumlah_sistem' => DB::raw('system_quantity')]);

        DB::table('inventory_check_items')
            ->whereNull('jumlah_aktual')
            ->whereNotNull('actual_quantity')
            ->update(['jumlah_aktual' => DB::raw('actual_quantity')]);

        DB::table('inventory_check_items')
            ->whereNull('kondisi_fisik')
            ->update(['kondisi_fisik' => DB::raw('physical_status')]);

        DB::table('inventory_check_items')
            ->where('hasil_pemeriksaan', 'pending')
            ->update(['hasil_pemeriksaan' => DB::raw('check_result')]);

        Schema::table('asset_maintenances', function (Blueprint $table) {
            if (! Schema::hasColumn('asset_maintenances', 'unit_id')) {
                $table->foreignId('unit_id')->nullable()->after('asset_id')->constrained('units')->nullOnDelete();
            }

            if (! Schema::hasColumn('asset_maintenances', 'status')) {
                $table->string('status')->default('done')->after('notes');
            }
        });

        Schema::table('asset_disposals', function (Blueprint $table) {
            if (! Schema::hasColumn('asset_disposals', 'unit_id')) {
                $table->foreignId('unit_id')->nullable()->after('asset_id')->constrained('units')->nullOnDelete();
            }
        });

        DB::table('asset_maintenances')
            ->whereNull('unit_id')
            ->orderBy('id')
            ->get(['id', 'asset_id'])
            ->each(function (object $maintenance): void {
                DB::table('asset_maintenances')
                    ->where('id', $maintenance->id)
                    ->update([
                        'unit_id' => DB::table('assets')->where('id', $maintenance->asset_id)->value('unit_id'),
                    ]);
            });

        DB::table('asset_disposals')
            ->whereNull('unit_id')
            ->orderBy('id')
            ->get(['id', 'asset_id'])
            ->each(function (object $disposal): void {
                DB::table('asset_disposals')
                    ->where('id', $disposal->id)
                    ->update([
                        'unit_id' => DB::table('assets')->where('id', $disposal->asset_id)->value('unit_id'),
                    ]);
            });
    }

    public function down(): void
    {
        Schema::table('asset_disposals', function (Blueprint $table) {
            if (Schema::hasColumn('asset_disposals', 'unit_id')) {
                $table->dropConstrainedForeignId('unit_id');
            }
        });

        Schema::table('asset_maintenances', function (Blueprint $table) {
            if (Schema::hasColumn('asset_maintenances', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('asset_maintenances', 'unit_id')) {
                $table->dropConstrainedForeignId('unit_id');
            }
        });

        Schema::table('inventory_check_items', function (Blueprint $table) {
            foreach (['kondisi_fisik', 'hasil_pemeriksaan', 'jumlah_hilang', 'jumlah_rusak', 'jumlah_sedang', 'jumlah_baik', 'jumlah_aktual', 'jumlah_sistem'] as $column) {
                if (Schema::hasColumn('inventory_check_items', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('inventory_checks', function (Blueprint $table) {
            if (Schema::hasColumn('inventory_checks', 'created_by')) {
                $table->dropConstrainedForeignId('created_by');
            }

            foreach (['tanggal_pemeriksaan', 'tahun_akademik', 'periode'] as $column) {
                if (Schema::hasColumn('inventory_checks', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        if (DB::getDriverName() === 'mysql') {
            DB::table('inventory_checks')->where('status', 'ongoing')->update(['status' => 'draft']);
            DB::statement("ALTER TABLE inventory_checks MODIFY `status` ENUM('draft','completed') NOT NULL DEFAULT 'draft'");
        }

        Schema::table('assets', function (Blueprint $table) {
            foreach (['status_aset', 'current_condition', 'satuan'] as $column) {
                if (Schema::hasColumn('assets', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
