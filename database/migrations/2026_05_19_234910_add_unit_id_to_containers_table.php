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
        Schema::table('containers', function (Blueprint $table) {
            if (! Schema::hasColumn('containers', 'unit_id')) {
                $table->foreignId('unit_id')->nullable()->after('id')->constrained('units')->nullOnDelete();
            }
        });

        if (Schema::hasColumn('containers', 'unit_id') && Schema::hasTable('locations')) {
            DB::table('containers')
                ->join('locations', 'containers.location_id', '=', 'locations.id')
                ->whereNull('containers.unit_id')
                ->update(['containers.unit_id' => DB::raw('locations.unit_id')]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('containers', function (Blueprint $table) {
            if (Schema::hasColumn('containers', 'unit_id')) {
                $table->dropConstrainedForeignId('unit_id');
            }
        });
    }
};
