<?php

namespace Tests\Feature\Concerns;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Container;
use App\Models\InventoryCheck;
use App\Models\InventoryCheckItem;
use App\Models\Location;
use App\Models\ToolReplacementRequest;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

trait CreatesSimasetFixtures
{
    protected function signIn(string $role = 'admin', ?Unit $unit = null): User
    {
        $user = $this->user($role, $unit);
        $this->actingAs($user);

        return $user;
    }

    protected function user(string $role = 'admin', ?Unit $unit = null, array $overrides = []): User
    {
        $sequence = (int) DB::table('users')->count() + 1;

        $attributes = array_merge([
            'unit_id' => $unit?->id,
            'name' => ucfirst($role).' SIMASET',
            'username' => $role.$sequence,
            'email' => $role.$sequence.'@simaset.test',
            'password' => Hash::make('password'),
            'role' => $role,
            'status' => 'active',
        ], $overrides);

        $existing = User::query()
            ->when($attributes['email'] ?? null, fn ($query, string $email) => $query->orWhere('email', $email))
            ->when($attributes['username'] ?? null, fn ($query, string $username) => $query->orWhere('username', $username))
            ->first();

        if ($existing) {
            $existing->fill($attributes)->save();

            return $existing;
        }

        $id = DB::table('users')->insertGetId($attributes);

        return User::findOrFail($id);
    }

    protected function unit(string $code = 'GZI', string $name = 'Laboran Gizi'): Unit
    {
        return Unit::firstOrCreate([
            'code' => $code,
        ], [
            'name' => $name,
            'status' => 'active',
        ]);
    }

    protected function category(Unit $unit, string $name = 'Alat Ukur'): Category
    {
        return Category::firstOrCreate([
            'unit_id' => $unit->id,
            'name' => $name,
        ], [
            'status' => 'active',
        ]);
    }

    protected function location(Unit $unit, string $name = 'Ruang Praktikum'): Location
    {
        return Location::firstOrCreate([
            'unit_id' => $unit->id,
            'name' => $name,
        ], [
            'status' => 'active',
        ]);
    }

    protected function container(Unit $unit, ?Location $location = null, string $name = 'Lemari Praktikum'): Container
    {
        $location ??= $this->location($unit);

        return Container::create([
            'unit_id' => $unit->id,
            'location_id' => $location->id,
            'name' => $name,
            'code' => Container::generateCode($location),
            'status' => 'active',
        ]);
    }

    protected function asset(Unit $unit, array $overrides = []): Asset
    {
        $category = $overrides['category'] ?? $this->category($unit);
        $location = $overrides['location'] ?? $this->location($unit);
        $container = $overrides['container'] ?? null;

        unset($overrides['category'], $overrides['location'], $overrides['container']);

        return Asset::create(array_merge([
            'unit_id' => $unit->id,
            'category_id' => $category->id,
            'location_id' => $location->id,
            'container_id' => $container?->id,
            'identification_type' => 'individual',
            'quantity' => 1,
            'satuan' => 'unit',
            'asset_code' => Asset::generateAssetCode($unit, $category),
            'legacy_inventory_code' => null,
            'name' => 'Timbangan Digital',
            'kondisi_aset' => 'baik',
            'description' => null,
        ], $overrides));
    }

    protected function assetPayload(Unit $unit, Category $category, Location $location, array $overrides = []): array
    {
        return array_merge([
            'unit_id' => $unit->id,
            'category_id' => $category->id,
            'location_id' => $location->id,
            'container_id' => null,
            'identification_type' => 'individual',
            'quantity' => 1,
            'satuan_choice' => 'unit',
            'satuan_custom' => null,
            'legacy_inventory_code' => null,
            'name' => 'Timbangan Digital',
            'kondisi_aset' => 'baik',
            'jumlah_baik' => 1,
            'jumlah_sedang' => 0,
            'jumlah_rusak' => 0,
            'jumlah_hilang' => 0,
            'description' => null,
        ], $overrides);
    }

    protected function inventoryCheck(Unit $unit, User $user, array $overrides = []): InventoryCheck
    {
        return InventoryCheck::create(array_merge([
            'unit_id' => $unit->id,
            'created_by' => $user->id,
            'check_code' => 'INV-20260703-TEST'.str_pad((string) (InventoryCheck::count() + 1), 2, '0', STR_PAD_LEFT),
            'periode' => '2026/2027',
            'semester' => 'Ganjil',
            'tahun_akademik' => '2026/2027',
            'tanggal_pemeriksaan' => '2026-07-03',
            'status' => 'ongoing',
            'notes' => null,
        ], $overrides));
    }

    protected function checkedItem(InventoryCheck $check, Asset $asset, array $overrides = []): InventoryCheckItem
    {
        return InventoryCheckItem::create(array_merge([
            'inventory_check_id' => $check->id,
            'asset_id' => $asset->id,
            'jumlah_sistem' => $asset->quantity,
            'jumlah_aktual' => $asset->quantity,
            'jumlah_baik' => $asset->quantity,
            'jumlah_sedang' => 0,
            'jumlah_rusak' => 0,
            'jumlah_hilang' => 0,
            'hasil_pemeriksaan' => 'sesuai',
        ], $overrides));
    }

    protected function replacement(Asset $asset, array $overrides = []): ToolReplacementRequest
    {
        return ToolReplacementRequest::create(array_merge([
            'asset_id' => $asset->id,
            'unit_id' => $asset->unit_id,
            'student_name' => 'Budi Santoso',
            'student_nim' => '23123456',
            'student_semester' => '4',
            'prodi_kelas' => 'D3 Gizi A',
            'practicum_name' => 'Praktikum Pengukuran',
            'incident_date' => '2026-07-03',
            'replacement_quantity' => 1,
            'damage_description' => 'Kaca alat retak saat praktikum.',
            'whatsapp_number' => '6281234567890',
            'status' => 'menunggu_verifikasi',
        ], $overrides));
    }
}
