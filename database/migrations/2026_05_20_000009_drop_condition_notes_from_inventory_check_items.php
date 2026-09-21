<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('inventory_check_items')) {
            return;
        }

        Schema::table('inventory_check_items', function (Blueprint $table): void {
            if (Schema::hasColumn('inventory_check_items', 'kondisi_fisik')) {
                $table->dropColumn('kondisi_fisik');
            }

            if (Schema::hasColumn('inventory_check_items', 'notes')) {
                $table->dropColumn('notes');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('inventory_check_items')) {
            return;
        }

        Schema::table('inventory_check_items', function (Blueprint $table): void {
            if (! Schema::hasColumn('inventory_check_items', 'kondisi_fisik')) {
                $table->string('kondisi_fisik')->nullable()->after('jumlah_hilang');
            }

            if (! Schema::hasColumn('inventory_check_items', 'notes')) {
                $table->text('notes')->nullable()->after('hasil_pemeriksaan');
            }
        });
    }
};
