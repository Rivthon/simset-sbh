<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (! Schema::hasColumn('categories', 'unit_id')) {
                $table->foreignId('unit_id')->nullable()->after('id')->constrained()->nullOnDelete();
            }
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE assets MODIFY `condition` ENUM('good','minor_damage','major_damage','lost') NOT NULL DEFAULT 'good'");
            DB::statement("ALTER TABLE inventory_check_items MODIFY `condition` ENUM('good','minor_damage','major_damage','lost') NOT NULL DEFAULT 'good'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::table('assets')->where('condition', 'lost')->update(['condition' => 'major_damage']);
            DB::table('inventory_check_items')->where('condition', 'lost')->update(['condition' => 'major_damage']);
            DB::statement("ALTER TABLE assets MODIFY `condition` ENUM('good','minor_damage','major_damage') NOT NULL DEFAULT 'good'");
            DB::statement("ALTER TABLE inventory_check_items MODIFY `condition` ENUM('good','minor_damage','major_damage') NOT NULL DEFAULT 'good'");
        }

        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'unit_id')) {
                $table->dropConstrainedForeignId('unit_id');
            }
        });
    }
};
