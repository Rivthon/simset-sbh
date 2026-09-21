<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('units') || ! Schema::hasColumn('units', 'description')) {
            return;
        }

        Schema::table('units', function (Blueprint $table): void {
            $table->dropColumn('description');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('units') || Schema::hasColumn('units', 'description')) {
            return;
        }

        Schema::table('units', function (Blueprint $table): void {
            $table->text('description')->nullable()->after('code');
        });
    }
};
