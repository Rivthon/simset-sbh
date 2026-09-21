<?php

namespace Tests\Feature;

use App\Models\InventoryCheck;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\CreatesSimasetFixtures;
use Tests\TestCase;

class InventoryCheckControllerTest extends TestCase
{
    use CreatesSimasetFixtures;
    use RefreshDatabase;

    public function test_inventory_check_can_be_created(): void
    {
        $this->withoutVite();

        $unit = $this->unit();
        $asset = $this->asset($unit);
        $manager = $this->signIn('pengelola', $unit);

        $this->post(route('inventory-checks.store'), [
            'unit_id' => $unit->id,
            'semester' => 'Ganjil',
            'tahun_akademik' => '2026/2027',
            'tanggal_pemeriksaan' => '2026-07-03',
        ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $check = InventoryCheck::firstOrFail();

        $this->assertSame($manager->id, $check->created_by);
        $this->assertSame('ongoing', $check->status);
        $this->assertDatabaseHas('inventory_check_items', [
            'inventory_check_id' => $check->id,
            'asset_id' => $asset->id,
            'jumlah_sistem' => 1,
        ]);
    }

    public function test_duplicate_inventory_check_is_rejected(): void
    {
        $this->withoutVite();

        $unit = $this->unit();
        $manager = $this->signIn('pengelola', $unit);

        $this->inventoryCheck($unit, $manager, [
            'semester' => 'Genap',
            'tahun_akademik' => '2026/2027',
            'tanggal_pemeriksaan' => '2026-07-03',
        ]);

        $this->post(route('inventory-checks.store'), [
            'unit_id' => $unit->id,
            'semester' => 'Genap',
            'tahun_akademik' => '2026/2027',
            'tanggal_pemeriksaan' => '2026-07-10',
        ])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertSame(1, InventoryCheck::where('unit_id', $unit->id)->count());
    }

    public function test_asset_can_only_be_checked_once(): void
    {
        $this->withoutVite();

        $unit = $this->unit();
        $asset = $this->asset($unit, ['asset_code' => 'AST-GZI-AUK-0099']);
        $manager = $this->signIn('pengelola', $unit);
        $check = $this->inventoryCheck($unit, $manager);

        $this->get(route('inventory-checks.scan', [$check, $asset->asset_code]))->assertOk();
        $this->get(route('inventory-checks.scan', [$check, $asset->asset_code]))->assertOk();

        $this->assertSame(1, $check->items()->where('asset_id', $asset->id)->count());
    }

    public function test_inventory_check_progress_is_calculated_correctly(): void
    {
        $this->withoutVite();

        $unit = $this->unit();
        $checked = $this->asset($unit, ['name' => 'Aset Sudah Dicek']);
        $this->asset($unit, ['name' => 'Aset Belum Dicek']);
        $manager = $this->signIn('pengelola', $unit);
        $check = $this->inventoryCheck($unit, $manager);
        $this->checkedItem($check, $checked);

        $this->get(route('inventory-checks.show', $check))
            ->assertOk()
            ->assertViewHas('progress', fn (array $progress): bool => $progress['total'] === 2
                && $progress['checked'] === 1
                && $progress['unchecked'] === 1
                && $progress['percent'] === 50.0);
    }

    public function test_inventory_check_can_be_completed(): void
    {
        $this->withoutVite();

        $unit = $this->unit();
        $asset = $this->asset($unit);
        $manager = $this->signIn('pengelola', $unit);
        $check = $this->inventoryCheck($unit, $manager);
        $this->checkedItem($check, $asset);

        $this->patch(route('inventory-checks.update', $check), [
            'status' => 'completed',
        ])->assertSessionHas('success');

        $this->assertSame('completed', $check->fresh()->status);
    }

    public function test_asset_condition_is_updated_from_completed_inventory_check(): void
    {
        $this->withoutVite();

        $unit = $this->unit();
        $asset = $this->asset($unit, ['quantity' => 2, 'kondisi_aset' => 'baik']);
        $manager = $this->signIn('pengelola', $unit);
        $check = $this->inventoryCheck($unit, $manager);
        $this->checkedItem($check, $asset, [
            'jumlah_aktual' => 2,
            'jumlah_baik' => 1,
            'jumlah_sedang' => 0,
            'jumlah_rusak' => 1,
            'jumlah_hilang' => 0,
            'hasil_pemeriksaan' => 'sesuai',
        ]);

        $this->patch(route('inventory-checks.update', $check), [
            'status' => 'completed',
        ])->assertSessionHas('success');

        $this->assertSame('rusak', $asset->fresh()->kondisi_aset);
    }
}
