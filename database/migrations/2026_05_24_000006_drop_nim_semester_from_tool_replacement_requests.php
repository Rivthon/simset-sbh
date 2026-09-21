<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('tool_replacement_requests') || ! Schema::hasColumn('tool_replacement_requests', 'nim_semester')) {
            return;
        }

        Schema::table('tool_replacement_requests', function (Blueprint $table): void {
            $table->dropColumn('nim_semester');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('tool_replacement_requests') || Schema::hasColumn('tool_replacement_requests', 'nim_semester')) {
            return;
        }

        Schema::table('tool_replacement_requests', function (Blueprint $table): void {
            $table->string('nim_semester')->nullable()->after('student_semester');
        });
    }
};
