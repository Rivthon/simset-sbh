<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('assets')) {
            return;
        }

        DB::table('assets')
            ->where(function ($query): void {
                $query->whereNull('identification_type')
                    ->orWhereNotIn('identification_type', ['individual', 'group']);
            })
            ->update(['identification_type' => 'group']);

        DB::table('assets')
            ->where(function ($query): void {
                $query->whereNull('qr_code')->orWhere('qr_code', '');
            })
            ->orderBy('id')
            ->get(['id'])
            ->each(function (object $asset): void {
                DB::table('assets')
                    ->where('id', $asset->id)
                    ->update(['qr_code' => $this->generateAssetQrCode()]);
            });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE assets MODIFY identification_type ENUM('individual','group') NOT NULL DEFAULT 'individual'");
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('assets')) {
            return;
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE assets MODIFY identification_type ENUM('individual','group','container') NOT NULL DEFAULT 'individual'");
        }
    }

    private function generateAssetQrCode(): string
    {
        do {
            $code = 'SIMASET-AST-'.Str::upper(Str::random(10));
        } while (
            DB::table('assets')->where('qr_code', $code)->exists()
            || (Schema::hasTable('containers') && DB::table('containers')->where('qr_code', $code)->exists())
        );

        return $code;
    }
};
