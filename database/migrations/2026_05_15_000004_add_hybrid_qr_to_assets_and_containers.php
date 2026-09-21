<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('containers', function (Blueprint $table) {
            $table->string('qr_code')->unique()->nullable()->after('code');
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->enum('identification_type', ['individual', 'container'])
                ->default('individual')
                ->after('container_id');
        });

        DB::table('containers')
            ->whereNull('qr_code')
            ->orderBy('id')
            ->get()
            ->each(function ($container): void {
                DB::table('containers')
                    ->where('id', $container->id)
                    ->update(['qr_code' => $this->generateContainerQrCode()]);
            });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('identification_type');
        });

        Schema::table('containers', function (Blueprint $table) {
            $table->dropUnique(['qr_code']);
            $table->dropColumn('qr_code');
        });
    }

    private function generateContainerQrCode(): string
    {
        do {
            $code = 'SIMASET-CTR-'.Str::upper(Str::random(10));
        } while (
            DB::table('containers')->where('qr_code', $code)->exists()
            || DB::table('assets')->where('qr_code', $code)->exists()
        );

        return $code;
    }
};
