<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->backfillFinalColumns();

        $this->dropForeignIfExists('assets', 'submitted_by');
        $this->dropForeignIfExists('assets', 'approved_by');
        $this->dropForeignIfExists('inventory_checks', 'checked_by');

        $this->dropColumnsIfExist('assets', [
            'barcode_code',
            'brand',
            'model',
            'serial_number',
            'purchase_date',
            'purchase_price',
            'condition',
            'current_condition',
            'status',
            'status_aset',
            'approval_status',
            'submitted_by',
            'approved_by',
            'approved_at',
            'rejected_reason',
        ]);

        $this->dropColumnsIfExist('inventory_checks', [
            'checked_by',
            'academic_year',
            'check_date',
        ]);

        $this->dropColumnsIfExist('inventory_check_items', [
            'system_quantity',
            'actual_quantity',
            'physical_status',
            'condition',
            'check_result',
            'checked_at',
        ]);
    }

    public function down(): void
    {
        if (Schema::hasTable('assets')) {
            Schema::table('assets', function (Blueprint $table): void {
                if (! Schema::hasColumn('assets', 'barcode_code')) {
                    $table->string('barcode_code')->unique()->nullable()->after('qr_code');
                }
                if (! Schema::hasColumn('assets', 'brand')) {
                    $table->string('brand')->nullable()->after('name');
                }
                if (! Schema::hasColumn('assets', 'model')) {
                    $table->string('model')->nullable()->after('brand');
                }
                if (! Schema::hasColumn('assets', 'serial_number')) {
                    $table->string('serial_number')->nullable()->after('model');
                }
                if (! Schema::hasColumn('assets', 'purchase_date')) {
                    $table->date('purchase_date')->nullable()->after('serial_number');
                }
                if (! Schema::hasColumn('assets', 'purchase_price')) {
                    $table->decimal('purchase_price', 15, 2)->nullable()->after('purchase_date');
                }
                if (! Schema::hasColumn('assets', 'condition')) {
                    $table->string('condition')->default('good')->after('purchase_price');
                }
                if (! Schema::hasColumn('assets', 'current_condition')) {
                    $table->string('current_condition')->nullable()->after('condition');
                }
                if (! Schema::hasColumn('assets', 'status')) {
                    $table->string('status')->default('active')->after('current_condition');
                }
                if (! Schema::hasColumn('assets', 'status_aset')) {
                    $table->string('status_aset')->nullable()->after('status');
                }
                if (! Schema::hasColumn('assets', 'approval_status')) {
                    $table->string('approval_status')->default('approved')->after('photo');
                }
                if (! Schema::hasColumn('assets', 'submitted_by')) {
                    $table->foreignId('submitted_by')->nullable()->after('approval_status')->constrained('users')->nullOnDelete();
                }
                if (! Schema::hasColumn('assets', 'approved_by')) {
                    $table->foreignId('approved_by')->nullable()->after('submitted_by')->constrained('users')->nullOnDelete();
                }
                if (! Schema::hasColumn('assets', 'approved_at')) {
                    $table->timestamp('approved_at')->nullable()->after('approved_by');
                }
                if (! Schema::hasColumn('assets', 'rejected_reason')) {
                    $table->text('rejected_reason')->nullable()->after('approved_at');
                }
            });
        }

        if (Schema::hasTable('inventory_checks')) {
            Schema::table('inventory_checks', function (Blueprint $table): void {
                if (! Schema::hasColumn('inventory_checks', 'checked_by')) {
                    $table->foreignId('checked_by')->nullable()->after('unit_id')->constrained('users')->nullOnDelete();
                }
                if (! Schema::hasColumn('inventory_checks', 'academic_year')) {
                    $table->string('academic_year')->nullable()->after('semester');
                }
                if (! Schema::hasColumn('inventory_checks', 'check_date')) {
                    $table->date('check_date')->nullable()->after('academic_year');
                }
            });
        }

        if (Schema::hasTable('inventory_check_items')) {
            Schema::table('inventory_check_items', function (Blueprint $table): void {
                if (! Schema::hasColumn('inventory_check_items', 'system_quantity')) {
                    $table->unsignedInteger('system_quantity')->default(1)->after('asset_id');
                }
                if (! Schema::hasColumn('inventory_check_items', 'actual_quantity')) {
                    $table->unsignedInteger('actual_quantity')->nullable()->after('system_quantity');
                }
                if (! Schema::hasColumn('inventory_check_items', 'physical_status')) {
                    $table->string('physical_status')->default('available')->after('actual_quantity');
                }
                if (! Schema::hasColumn('inventory_check_items', 'condition')) {
                    $table->string('condition')->default('good')->after('physical_status');
                }
                if (! Schema::hasColumn('inventory_check_items', 'check_result')) {
                    $table->string('check_result')->default('pending')->after('condition');
                }
                if (! Schema::hasColumn('inventory_check_items', 'checked_at')) {
                    $table->timestamp('checked_at')->nullable()->after('check_result');
                }
            });
        }
    }

    private function backfillFinalColumns(): void
    {
        if (Schema::hasTable('assets') && Schema::hasColumn('assets', 'kondisi_aset')) {
            DB::table('assets')
                ->whereNull('kondisi_aset')
                ->orWhere('kondisi_aset', '')
                ->update(['kondisi_aset' => 'baik']);
        }

        if (Schema::hasTable('inventory_checks')) {
            if (Schema::hasColumn('inventory_checks', 'created_by') && Schema::hasColumn('inventory_checks', 'checked_by')) {
                DB::table('inventory_checks')->whereNull('created_by')->update(['created_by' => DB::raw('checked_by')]);
            }
            if (Schema::hasColumn('inventory_checks', 'tahun_akademik') && Schema::hasColumn('inventory_checks', 'academic_year')) {
                DB::table('inventory_checks')->whereNull('tahun_akademik')->update(['tahun_akademik' => DB::raw('academic_year')]);
            }
            if (Schema::hasColumn('inventory_checks', 'tanggal_pemeriksaan') && Schema::hasColumn('inventory_checks', 'check_date')) {
                DB::table('inventory_checks')->whereNull('tanggal_pemeriksaan')->update(['tanggal_pemeriksaan' => DB::raw('check_date')]);
            }
        }

        if (Schema::hasTable('inventory_check_items')) {
            if (Schema::hasColumn('inventory_check_items', 'jumlah_sistem') && Schema::hasColumn('inventory_check_items', 'system_quantity')) {
                DB::table('inventory_check_items')->whereNull('jumlah_sistem')->update(['jumlah_sistem' => DB::raw('system_quantity')]);
            }
            if (Schema::hasColumn('inventory_check_items', 'jumlah_aktual') && Schema::hasColumn('inventory_check_items', 'actual_quantity')) {
                DB::table('inventory_check_items')->whereNull('jumlah_aktual')->whereNotNull('actual_quantity')->update(['jumlah_aktual' => DB::raw('actual_quantity')]);
            }
            if (Schema::hasColumn('inventory_check_items', 'kondisi_fisik') && Schema::hasColumn('inventory_check_items', 'physical_status')) {
                DB::table('inventory_check_items')->whereNull('kondisi_fisik')->update(['kondisi_fisik' => DB::raw('physical_status')]);
            }
            if (Schema::hasColumn('inventory_check_items', 'hasil_pemeriksaan') && Schema::hasColumn('inventory_check_items', 'check_result')) {
                DB::table('inventory_check_items')->whereNull('hasil_pemeriksaan')->update(['hasil_pemeriksaan' => DB::raw('check_result')]);
            }
        }
    }

    private function dropColumnsIfExist(string $table, array $columns): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        $existing = array_values(array_filter($columns, fn (string $column): bool => Schema::hasColumn($table, $column)));

        if ($existing === []) {
            return;
        }

        Schema::table($table, fn (Blueprint $blueprint) => $blueprint->dropColumn($existing));
    }

    private function dropForeignIfExists(string $table, string $column): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
            return;
        }

        $database = DB::getDatabaseName();
        $foreignKey = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', $database)
            ->where('TABLE_NAME', $table)
            ->where('COLUMN_NAME', $column)
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->value('CONSTRAINT_NAME');

        if (! $foreignKey) {
            return;
        }

        Schema::table($table, fn (Blueprint $blueprint) => $blueprint->dropForeign($foreignKey));
    }
};
