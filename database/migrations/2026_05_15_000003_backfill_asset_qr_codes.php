<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('assets')
            ->whereNull('qr_code')
            ->orWhereColumn('qr_code', 'asset_code')
            ->get()
            ->each(function ($asset): void {
                DB::table('assets')
                    ->where('id', $asset->id)
                    ->update(['qr_code' => $this->generateAssetQrCode()]);
            });
    }

    public function down(): void
    {
        //
    }

    private function generateAssetQrCode(): string
    {
        do {
            $code = 'SIMASET-AST-'.Str::upper(Str::random(10));
        } while (DB::table('assets')->where('qr_code', $code)->exists());

        return $code;
    }
};
