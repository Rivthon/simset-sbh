<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tool_replacement_requests', function (Blueprint $table): void {
            $table->id();
            $table->string('replacement_code')->nullable()->unique();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $table->string('student_name');
            $table->string('student_nim')->nullable();
            $table->string('student_semester')->nullable();
            $table->string('prodi_kelas')->nullable();
            $table->string('practicum_name');
            $table->date('incident_date');
            $table->unsignedInteger('replacement_quantity');
            $table->text('damage_description')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->string('damage_photo')->nullable();
            $table->enum('status', ['menunggu_verifikasi', 'menunggu_penggantian', 'sudah_diganti', 'ditolak'])->default('menunggu_verifikasi');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->text('laboran_note')->nullable();
            $table->timestamps();

            $table->index(['unit_id', 'status']);
            $table->index('incident_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tool_replacement_requests');
    }
};
