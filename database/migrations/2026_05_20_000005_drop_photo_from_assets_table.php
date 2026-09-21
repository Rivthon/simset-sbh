<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('assets') || ! Schema::hasColumn('assets', 'photo')) {
            return;
        }

        Schema::table('assets', function (Blueprint $table): void {
            $table->dropColumn('photo');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('assets') || Schema::hasColumn('assets', 'photo')) {
            return;
        }

        Schema::table('assets', function (Blueprint $table): void {
            $table->string('photo')->nullable()->after('description');
        });
    }
};
