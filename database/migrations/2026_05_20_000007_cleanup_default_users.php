<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $finalUsers = [
        'admin@simaset.test' => [
            'name' => 'Admin SIMASET SBH',
            'username' => 'admin',
            'role' => 'admin',
            'unit_code' => null,
            'position' => 'Administrator Sistem',
            'access_scope' => 'all',
        ],
        'pimpinan@simaset.test' => [
            'name' => 'Pimpinan',
            'username' => 'pimpinan',
            'role' => 'pimpinan',
            'unit_code' => null,
            'position' => 'Pimpinan',
            'access_scope' => 'all',
        ],
        'diah@simaset.test' => [
            'name' => 'Diah',
            'username' => 'diah',
            'role' => 'pengelola',
            'unit_code' => 'BID',
            'position' => 'Laboran Kebidanan',
            'access_scope' => 'unit',
        ],
        'melisa@simaset.test' => [
            'name' => 'Melisa',
            'username' => 'melisa',
            'role' => 'pengelola',
            'unit_code' => 'FAR',
            'position' => 'Laboran Farmasi',
            'access_scope' => 'unit',
        ],
        'adlina@simaset.test' => [
            'name' => 'Adlina',
            'username' => 'adlina',
            'role' => 'pengelola',
            'unit_code' => 'GZI',
            'position' => 'Laboran Gizi',
            'access_scope' => 'unit',
        ],
    ];

    public function up(): void
    {
        DB::transaction(function (): void {
            $this->ensureFinalUsers();

            DB::table('users')
                ->whereNotIn('email', array_keys($this->finalUsers))
                ->delete();
        });
    }

    public function down(): void
    {
        // No-op: user dummy lama tidak direstore otomatis.
    }

    private function ensureFinalUsers(): void
    {
        foreach ($this->finalUsers as $email => $user) {
            $unitId = $user['unit_code']
                ? DB::table('units')->where('code', $user['unit_code'])->value('id')
                : null;

            $data = [
                'name' => $user['name'],
                'username' => $user['username'],
                'password' => Hash::make('password'),
                'role' => $user['role'],
                'unit_id' => $unitId,
                'status' => 'active',
            ];

            foreach (['position', 'access_scope'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $data[$column] = $user[$column];
                }
            }

            if (Schema::hasColumn('users', 'created_at')) {
                $data['created_at'] = now();
            }

            if (Schema::hasColumn('users', 'updated_at')) {
                $data['updated_at'] = now();
            }

            DB::table('users')->updateOrInsert(['email' => $email], $data);
        }
    }
};
