<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE assets MODIFY identification_type ENUM('individual','group','container') NOT NULL DEFAULT 'individual'");
        }

        Schema::table('assets', function (Blueprint $table) {
            if (! Schema::hasColumn('assets', 'quantity')) {
                $table->unsignedInteger('quantity')->default(1)->after('identification_type');
            }

            if (! Schema::hasColumn('assets', 'legacy_inventory_code')) {
                $table->string('legacy_inventory_code')->nullable()->after('asset_code');
            }

            if (! Schema::hasColumn('assets', 'barcode_code')) {
                $table->string('barcode_code')->unique()->nullable()->after('qr_code');
            }
        });

        Schema::table('inventory_check_items', function (Blueprint $table) {
            if (! Schema::hasColumn('inventory_check_items', 'system_quantity')) {
                $table->unsignedInteger('system_quantity')->default(1)->after('asset_id');
            }

            if (! Schema::hasColumn('inventory_check_items', 'actual_quantity')) {
                $table->unsignedInteger('actual_quantity')->nullable()->after('system_quantity');
            }

            if (! Schema::hasColumn('inventory_check_items', 'check_result')) {
                $table->string('check_result')->default('pending')->after('condition');
            }

            if (! Schema::hasColumn('inventory_check_items', 'checked_at')) {
                $table->timestamp('checked_at')->nullable()->after('check_result');
            }
        });

        DB::table('assets')->whereNull('quantity')->update(['quantity' => 1]);

        DB::table('inventory_check_items')
            ->join('assets', 'inventory_check_items.asset_id', '=', 'assets.id')
            ->update(['inventory_check_items.system_quantity' => DB::raw('assets.quantity')]);
    }

    public function down(): void
    {
        Schema::table('inventory_check_items', function (Blueprint $table) {
            foreach (['checked_at', 'check_result', 'actual_quantity', 'system_quantity'] as $column) {
                if (Schema::hasColumn('inventory_check_items', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('assets', function (Blueprint $table) {
            foreach (['barcode_code', 'legacy_inventory_code', 'quantity'] as $column) {
                if (Schema::hasColumn('assets', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        if (DB::getDriverName() === 'mysql') {
            DB::table('assets')->where('identification_type', 'group')->update(['identification_type' => 'individual']);
            DB::statement("ALTER TABLE assets MODIFY identification_type ENUM('individual','container') NOT NULL DEFAULT 'individual'");
        }
    }
};
