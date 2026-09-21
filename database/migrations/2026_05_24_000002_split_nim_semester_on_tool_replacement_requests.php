<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tool_replacement_requests', function (Blueprint $table): void {
            if (! Schema::hasColumn('tool_replacement_requests', 'replacement_code')) {
                $table->string('replacement_code')->nullable()->unique()->after('id');
            }

            if (! Schema::hasColumn('tool_replacement_requests', 'student_nim')) {
                $table->string('student_nim')->nullable()->after('student_name');
            }

            if (! Schema::hasColumn('tool_replacement_requests', 'student_semester')) {
                $table->string('student_semester')->nullable()->after('student_nim');
            }
        });

        $hasLegacyNimSemester = Schema::hasColumn('tool_replacement_requests', 'nim_semester');

        DB::table('tool_replacement_requests')
            ->whereNull('replacement_code')
            ->orderBy('id')
            ->get($hasLegacyNimSemester ? ['id', 'nim_semester'] : ['id'])
            ->each(function (object $row) use ($hasLegacyNimSemester): void {
                $legacyValue = $hasLegacyNimSemester ? $row->nim_semester : null;

                DB::table('tool_replacement_requests')
                    ->where('id', $row->id)
                    ->update([
                        'replacement_code' => 'PGA-'.now()->format('Y').'-'.str_pad((string) $row->id, 4, '0', STR_PAD_LEFT),
                        'student_nim' => $this->legacyNim($legacyValue),
                        'student_semester' => $this->legacySemester($legacyValue),
                    ]);
            });
    }

    public function down(): void
    {
        Schema::table('tool_replacement_requests', function (Blueprint $table): void {
            if (Schema::hasColumn('tool_replacement_requests', 'student_semester')) {
                $table->dropColumn('student_semester');
            }

            if (Schema::hasColumn('tool_replacement_requests', 'student_nim')) {
                $table->dropColumn('student_nim');
            }

            if (Schema::hasColumn('tool_replacement_requests', 'replacement_code')) {
                $table->dropColumn('replacement_code');
            }
        });
    }

    private function legacyNim(?string $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        $parts = preg_split('/\s*(?:\/|-)\s*/', trim($value), 2);

        return trim($parts[0] ?? $value) ?: null;
    }

    private function legacySemester(?string $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        $parts = preg_split('/\s*(?:\/|-)\s*/', trim($value), 2);

        return isset($parts[1]) && trim($parts[1]) !== '' ? trim($parts[1]) : null;
    }
};
