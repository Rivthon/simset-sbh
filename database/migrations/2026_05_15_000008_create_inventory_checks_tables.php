<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('checked_by')->constrained('users')->cascadeOnDelete();
            $table->string('check_code')->unique();
            $table->string('semester');
            $table->string('academic_year');
            $table->date('check_date');
            $table->enum('status', ['draft', 'completed'])->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('inventory_check_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_check_id')->constrained()->cascadeOnDelete();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->enum('physical_status', ['available', 'missing', 'damaged'])->default('available');
            $table->enum('condition', ['good', 'minor_damage', 'major_damage'])->default('good');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['inventory_check_id', 'asset_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_check_items');
        Schema::dropIfExists('inventory_checks');
    }
};

