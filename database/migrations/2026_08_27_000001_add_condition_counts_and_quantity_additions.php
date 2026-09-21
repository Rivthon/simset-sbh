<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table): void {
            $table->unsignedInteger('jumlah_baik')->default(0)->after('kondisi_aset');
            $table->unsignedInteger('jumlah_sedang')->default(0)->after('jumlah_baik');
            $table->unsignedInteger('jumlah_rusak')->default(0)->after('jumlah_sedang');
            $table->unsignedInteger('jumlah_hilang')->default(0)->after('jumlah_rusak');
        });

        Schema::create('asset_quantity_additions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('addition_date');
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('jumlah_baik')->default(0);
            $table->unsignedInteger('jumlah_sedang')->default(0);
            $table->unsignedInteger('jumlah_rusak')->default(0);
            $table->unsignedInteger('jumlah_hilang')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        DB::table('assets')->orderBy('id')->each(function (object $asset): void {
            $latest = DB::table('inventory_check_items')
                ->where('asset_id', $asset->id)
                ->whereNotNull('jumlah_aktual')
                ->orderByDesc('id')
                ->first(['jumlah_baik', 'jumlah_sedang', 'jumlah_rusak', 'jumlah_hilang']);

            $counts = [
                'jumlah_baik' => (int) ($latest->jumlah_baik ?? 0),
                'jumlah_sedang' => (int) ($latest->jumlah_sedang ?? 0),
                'jumlah_rusak' => (int) ($latest->jumlah_rusak ?? 0),
                'jumlah_hilang' => (int) ($latest->jumlah_hilang ?? 0),
            ];

            if (array_sum($counts) === 0) {
                $condition = in_array($asset->kondisi_aset, ['baik', 'sedang', 'rusak', 'hilang'], true)
                    ? $asset->kondisi_aset
                    : 'baik';
                $counts['jumlah_'.$condition] = max(1, (int) $asset->quantity);
            }

            DB::table('assets')->where('id', $asset->id)->update($counts);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_quantity_additions');

        Schema::table('assets', function (Blueprint $table): void {
            $table->dropColumn(['jumlah_baik', 'jumlah_sedang', 'jumlah_rusak', 'jumlah_hilang']);
        });
    }
};
