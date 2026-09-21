<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'unit_id')) {
                $table->foreignId('unit_id')->nullable()->after('id')->constrained('units')->nullOnDelete();
            }

            if (! Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['admin', 'pengelola', 'pimpinan'])->default('pengelola')->after('password');
            }

            if (! Schema::hasColumn('users', 'status')) {
                $table->enum('status', ['active', 'inactive'])->default('active')->after('access_scope');
            }
        });
    }

    public function down(): void
    {
        // Sengaja no-op: kolom users ini adalah bagian inti sistem dan mungkin berasal dari migration awal.
    }
};
