<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('tool_replacement_requests') || ! Schema::hasColumn('tool_replacement_requests', 'replacement_code')) {
            return;
        }

        DB::table('tool_replacement_requests')
            ->orderBy('id')
            ->update(['replacement_code' => DB::raw("CONCAT('TMP-', id)")]);

        DB::table('tool_replacement_requests')
            ->leftJoin('units', 'tool_replacement_requests.unit_id', '=', 'units.id')
            ->orderBy('tool_replacement_requests.id')
            ->get([
                'tool_replacement_requests.id',
                'tool_replacement_requests.unit_id',
                'tool_replacement_requests.incident_date',
                'units.code as unit_code',
            ])
            ->groupBy(fn (object $row): string => $this->prefix($row))
            ->each(function ($rows, string $prefix): void {
                $counter = 1;

                foreach ($rows as $row) {
                    DB::table('tool_replacement_requests')
                        ->where('id', $row->id)
                        ->update([
                            'replacement_code' => $prefix.str_pad((string) $counter, 3, '0', STR_PAD_LEFT),
                        ]);

                    $counter++;
                }
            });
    }

    public function down(): void
    {
        // Kode penggantian lama tidak direstorasi agar tracking yang sudah diberikan tidak berubah lagi.
    }

    private function prefix(object $row): string
    {
        $rawUnitCode = $row->unit_code ?: ($row->unit_id ? 'U'.$row->unit_id : 'UNIT');
        $unitCode = preg_replace('/[^A-Z0-9]/', '', strtoupper($rawUnitCode)) ?: 'UNIT';
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
