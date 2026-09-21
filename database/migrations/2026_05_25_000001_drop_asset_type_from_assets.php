<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('assets') || ! Schema::hasColumn('assets', 'asset_type')) {
            return;
        }

        Schema::table('assets', function (Blueprint $table): void {
            $table->dropColumn('asset_type');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('assets') || Schema::hasColumn('assets', 'asset_type')) {
            return;
        }

        Schema::table('assets', function (Blueprint $table): void {
            $table->string('asset_type', 50)->default('peralatan')->after('identification_type');
        });
    }
};
