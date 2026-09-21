<?php

namespace Database\Seeders;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CoreUserSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            'BID' => $this->updateOrCreateUnit('BID', 'Laboran Kebidanan'),
            'FAR' => $this->updateOrCreateUnit('FAR', 'Laboran Farmasi'),
            'GZI' => $this->updateOrCreateUnit('GZI', 'Laboran Gizi'),
        ];

        $this->updateOrCreateUser([
            'name' => 'Admin SIMASET SBH',
            'username' => 'admin',
            'email' => 'admin@simaset.test',
            'role' => 'admin',
            'unit_id' => null,
        ]);

        $this->updateOrCreateUser([
            'name' => 'Kaprodi',
            'username' => 'pimpinan',
            'email' => 'pimpinan@simaset.test',
            'role' => 'pimpinan',
            'unit_id' => null,
        ]);

        $this->updateOrCreateUser([
            'name' => 'Diah',
            'username' => 'diah',
            'email' => 'diah@simaset.test',
            'role' => 'pengelola',
            'unit_id' => $units['BID']->id,
        ]);

        $this->updateOrCreateUser([
            'name' => 'Melisa',
            'username' => 'melisa',
            'email' => 'melisa@simaset.test',
            'role' => 'pengelola',
            'unit_id' => $units['FAR']->id,
        ]);

        $this->updateOrCreateUser([
            'name' => 'Adlina',
            'username' => 'adlina',
            'email' => 'adlina@simaset.test',
            'role' => 'pengelola',
            'unit_id' => $units['GZI']->id,
        ]);
    }

    private function updateOrCreateUnit(string $code, string $name): Unit
    {
        $unit = Unit::where('code', $code)
            ->orWhere('name', $name)
            ->first();

        if (! $unit) {
            $unit = new Unit();
        }

        $unit->fill([
            'name' => $name,
            'code' => $code,
            'status' => 'active',
        ])->save();

        return $unit;
    }

    /**
     * Status disimpan sebagai "active" karena skema dan login aplikasi memakai enum active/inactive.
     * Label UI tetap menampilkan "Aktif".
     */
    private function updateOrCreateUser(array $data): User
    {
        $user = User::where('email', $data['email'])
            ->orWhere('username', $data['username'])
            ->first();

        if (! $user) {
            $user = new User();
        }

        $user->fill([
            'unit_id' => $data['unit_id'],
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make('password'),
            'role' => $data['role'],
            'status' => 'active',
        ])->save();

        return $user;
    }
}
