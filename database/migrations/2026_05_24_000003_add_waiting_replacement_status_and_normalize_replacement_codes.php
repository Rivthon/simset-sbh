<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('tool_replacement_requests')) {
            return;
        }

        DB::statement("ALTER TABLE tool_replacement_requests MODIFY status ENUM('menunggu_verifikasi', 'menunggu_penggantian', 'sudah_diganti', 'ditolak') NOT NULL DEFAULT 'menunggu_verifikasi'");

        DB::table('tool_replacement_requests')
            ->leftJoin('units', 'tool_replacement_requests.unit_id', '=', 'units.id')
            ->orderBy('id')
            ->get([
                'tool_replacement_requests.id',
                'tool_replacement_requests.unit_id',
                'tool_replacement_requests.incident_date',
                'tool_replacement_requests.replacement_code',
                'units.code as unit_code',
            ])
            ->groupBy(fn (object $row): string => $this->prefix($row))
            ->each(function ($rows, string $prefix): void {
                $counter = 1;

                foreach ($rows as $row) {
                    $expected = $prefix.str_pad((string) $counter, 4, '0', STR_PAD_LEFT);
                    $counter++;

                    if ($row->replacement_code === $expected) {
                        continue;
                    }

                    DB::table('tool_replacement_requests')
                        ->where('id', $row->id)
                        ->update(['replacement_code' => $expected]);
                }
            });
    }

    public function down(): void
    {
        if (! Schema::hasTable('tool_replacement_requests')) {
            return;
        }

        DB::table('tool_replacement_requests')
            ->where('status', 'menunggu_penggantian')
            ->update(['status' => 'menunggu_verifikasi']);

        DB::statement("ALTER TABLE tool_replacement_requests MODIFY status ENUM('menunggu_verifikasi', 'sudah_diganti', 'ditolak') NOT NULL DEFAULT 'menunggu_verifikasi'");
    }

    private function prefix(object $row): string
    {
        $unitCode = preg_replace('/[^A-Z0-9]/', '', strtoupper($row->unit_code ?: 'U'.$row->unit_id));
        $date = $this->dateParts($row->incident_date);

        return 'PGA-'.$unitCode.'-'.$date['month'].'-'.$date['year'].'-';
    }

    private function dateParts(mixed $value): array
    {
        if ($value instanceof DateTimeInterface) {
            return [
                'month' => $value->format('m'),
                'year' => $value->format('Y'),
            ];
        }

        if (is_string($value) && preg_match('/^(\d{4})-(\d{2})/', $value, $matches)) {
            return [
                'month' => $matches[2],
                'year' => $matches[1],
            ];
        }

        return [
            'month' => now()->format('m'),
            'year' => now()->format('Y'),
        ];
    }
};
