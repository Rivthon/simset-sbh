<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const DEFAULT_KONDISI_ASET = 'baik';

    private const VALID_KONDISI_ASET = [
        'baik',
        'sedang',
        'rusak',
        'hilang',
    ];

    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            if (! Schema::hasColumn('assets', 'kondisi_aset')) {
                $table->string('kondisi_aset', 20)->default(self::DEFAULT_KONDISI_ASET)->after('status_aset');
            }
        });

        DB::table('assets')
            ->select(['id', 'current_condition', 'status_aset', 'condition'])
            ->orderBy('id')
            ->chunkById(200, function ($assets): void {
                foreach ($assets as $asset) {
                    DB::table('assets')
                        ->where('id', $asset->id)
                        ->update([
                            'kondisi_aset' => $this->mapKondisiAset(
                                $asset->current_condition,
                                $asset->status_aset,
                                $asset->condition,
                            ),
                        ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            if (Schema::hasColumn('assets', 'kondisi_aset')) {
                $table->dropColumn('kondisi_aset');
            }
        });
    }

    private function mapKondisiAset(?string $currentCondition, ?string $statusAset, ?string $condition): string
    {
        return $this->normalizeKondisiAset($currentCondition)
            ?? $this->normalizeStatusAset($statusAset)
            ?? $this->normalizeKondisiAset($condition)
            ?? self::DEFAULT_KONDISI_ASET;
    }

    private function normalizeStatusAset(?string $value): ?string
    {
        $normalized = $this->normalizeRawValue($value);

        return match ($normalized) {
            'rusak' => 'rusak',
            'hilang' => 'hilang',
            default => null,
        };
    }

    private function normalizeKondisiAset(?string $value): ?string
    {
        $normalized = $this->normalizeRawValue($value);

        if (in_array($normalized, self::VALID_KONDISI_ASET, true)) {
            return $normalized;
        }

        $mapped = match ($normalized) {
            'good' => 'baik',
            'minor_damage' => 'sedang',
            'major_damage', 'damaged', 'damage' => 'rusak',
            'lost', 'missing' => 'hilang',
            'inactive', 'disabled' => 'rusak',
            default => null,
        };

        if ($mapped !== null) {
            return $mapped;
        }

        foreach (self::VALID_KONDISI_ASET as $kondisi) {
            if (str_contains($normalized, $kondisi)) {
                return $kondisi;
            }
        }

        return null;
    }

    private function normalizeRawValue(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = strtolower(trim($value));

        return $normalized === '' ? null : $normalized;
    }
};
