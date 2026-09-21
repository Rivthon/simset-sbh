<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'damage_reports',
        'asset_maintenances',
        'asset_mutations',
        'asset_disposals',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::drop($table);
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('asset_maintenances')) {
            Schema::create('asset_maintenances', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
                $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
                $table->string('type')->nullable();
                $table->date('maintenance_date')->nullable();
                $table->decimal('cost', 14, 2)->default(0);
                $table->string('status')->default('completed');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('asset_mutations')) {
            Schema::create('asset_mutations', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
                $table->foreignId('from_unit_id')->nullable()->constrained('units')->nullOnDelete();
                $table->foreignId('to_unit_id')->nullable()->constrained('units')->nullOnDelete();
                $table->foreignId('from_location_id')->nullable()->constrained('locations')->nullOnDelete();
                $table->foreignId('to_location_id')->nullable()->constrained('locations')->nullOnDelete();
                $table->foreignId('mutated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->date('mutation_date')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('asset_disposals')) {
            Schema::create('asset_disposals', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('asset_id')->unique()->constrained()->cascadeOnDelete();
                $table->foreignId('disposed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
                $table->date('disposal_date')->nullable();
                $table->string('method')->nullable();
                $table->text('reason')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('damage_reports')) {
            Schema::create('damage_reports', function (Blueprint $table): void {
                $table->id();
                $table->string('report_code')->unique();
                $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
                $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
                $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
                $table->date('damage_date')->nullable();
                $table->text('damage_description')->nullable();
                $table->string('priority')->default('medium');
                $table->string('status')->default('pending');
                $table->string('damage_photo')->nullable();
                $table->text('admin_note')->nullable();
                $table->timestamp('handled_at')->nullable();
                $table->timestamps();
            });
        }
    }

    // Foreign key ada di dalam tabel yang di-drop, sehingga tidak perlu dilepas manual.
};
