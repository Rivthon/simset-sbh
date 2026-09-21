<?php

namespace Tests\Feature;

use App\Models\Asset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\CreatesSimasetFixtures;
use Tests\TestCase;

class AssetControllerTest extends TestCase
{
    use CreatesSimasetFixtures;
    use RefreshDatabase;

    public function test_admin_can_view_assets_page(): void
    {
        $this->withoutVite();

        $unit = $this->unit();
        $asset = $this->asset($unit, [
            'name' => 'Timbangan Digital',
            'description' => 'Keterangan aset pengujian',
        ]);

        $this->actingAs($this->user('admin'))
            ->get(route('assets.index'))
            ->assertOk()
            ->assertViewIs('assets.index')
            ->assertSee($asset->name)
            ->assertSee('Keterangan')
            ->assertSee('Keterangan aset pengujian')
            ->assertDontSee('<th class="px-4 py-3">Unit</th>', false);
    }

    public function test_admin_can_create_asset_with_generated_code_and_qr_code(): void
    {
        $this->withoutVite();

        [$unit, $category, $location] = $this->assetFormFixture(categoryName: 'Alat Ukur');

        $this->actingAs($this->user('admin'))
            ->post(route('assets.store'), $this->assetPayload($unit, $category, $location))
            ->assertRedirect(route('assets.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('assets', [
            'unit_id' => $unit->id,
            'category_id' => $category->id,
            'location_id' => $location->id,
            'name' => 'Timbangan Digital',
            'quantity' => 1,
        ]);

        $asset = Asset::where('name', 'Timbangan Digital')->firstOrFail();

        $this->assertSame('AST-GZI-AUK-0001', $asset->asset_code);
        $this->assertNotEmpty($asset->qr_code);
        $this->assertStringStartsWith('SIMASET-AST-', $asset->qr_code);
    }

    public function test_asset_validation_rejects_required_fields(): void
    {
        $this->withoutVite();

        $this->actingAs($this->user('admin'))
            ->post(route('assets.store'), [])
            ->assertSessionHasErrors(['unit_id', 'category_id', 'location_id', 'name', 'quantity']);
    }

    public function test_asset_can_be_updated(): void
    {
        $this->withoutVite();

        $unit = $this->unit();
        $category = $this->category($unit);
        $location = $this->location($unit);
        $asset = $this->asset($unit, compact('category', 'location'));

        $this->actingAs($this->user('admin'))
            ->put(route('assets.update', $asset), $this->assetPayload($unit, $category, $location, [
                'name' => 'Timbangan Digital Terverifikasi',
                'quantity' => 3,
                'jumlah_baik' => 3,
                'satuan_choice' => 'pcs',
            ]))
            ->assertRedirect(route('assets.index'))
            ->assertSessionHas('success');

        $asset->refresh();

        $this->assertSame('Timbangan Digital Terverifikasi', $asset->name);
        $this->assertSame(3, $asset->quantity);
        $this->assertSame('pcs', $asset->satuan);
    }

    public function test_asset_can_be_created_with_mixed_condition_counts(): void
    {
        $this->withoutVite();

        [$unit, $category, $location] = $this->assetFormFixture();

        $this->actingAs($this->user('admin'))
            ->post(route('assets.store'), $this->assetPayload($unit, $category, $location, [
                'quantity' => 8,
                'jumlah_baik' => 6,
                'jumlah_sedang' => 2,
            ]))
            ->assertRedirect(route('assets.index'));

        $asset = Asset::where('name', 'Timbangan Digital')->firstOrFail();

        $this->assertSame(['baik' => 6, 'sedang' => 2, 'rusak' => 0, 'hilang' => 0], $asset->conditionCounts());
        $this->assertSame('sedang', $asset->kondisi_aset);
        $this->assertSame('6 Baik, 2 Sedang', $asset->kondisi_aset_label);
    }

    public function test_condition_total_must_equal_asset_quantity(): void
    {
        $this->withoutVite();

        [$unit, $category, $location] = $this->assetFormFixture();

        $this->actingAs($this->user('admin'))
            ->post(route('assets.store'), $this->assetPayload($unit, $category, $location, [
                'quantity' => 8,
                'jumlah_baik' => 6,
                'jumlah_sedang' => 1,
            ]))
            ->assertSessionHasErrors('quantity');
    }

    public function test_quantity_can_be_added_without_changing_inventory_history(): void
    {
        $this->withoutVite();

        $unit = $this->unit();
        $asset = $this->asset($unit, ['quantity' => 8, 'jumlah_baik' => 6, 'jumlah_sedang' => 2]);
        $admin = $this->user('admin');
        $check = $this->inventoryCheck($unit, $admin, ['status' => 'completed']);
        $item = $this->checkedItem($check, $asset, [
            'jumlah_sistem' => 8,
            'jumlah_aktual' => 8,
            'jumlah_baik' => 6,
            'jumlah_sedang' => 2,
        ]);

        $this->actingAs($admin)
            ->post(route('assets.quantity-additions.store', $asset), [
                'quantity' => 3,
                'jumlah_baik' => 2,
                'jumlah_sedang' => 1,
                'jumlah_rusak' => 0,
                'jumlah_hilang' => 0,
                'addition_date' => '2026-08-27',
                'notes' => 'Pengadaan tambahan',
            ])
            ->assertRedirect();

        $asset->refresh();
        $item->refresh();

        $this->assertSame(11, $asset->quantity);
        $this->assertSame(8, $asset->jumlah_baik);
        $this->assertSame(3, $asset->jumlah_sedang);
        $this->assertSame(6, $item->jumlah_baik);
        $this->assertDatabaseHas('asset_quantity_additions', [
            'asset_id' => $asset->id,
            'quantity' => 3,
            'notes' => 'Pengadaan tambahan',
        ]);
    }

    public function test_asset_can_be_deleted(): void
    {
        $this->withoutVite();

        $asset = $this->asset($this->unit());

        $this->actingAs($this->user('admin'))
            ->delete(route('assets.destroy', $asset))
            ->assertRedirect(route('assets.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('assets', ['id' => $asset->id]);
    }

    private function assetFormFixture(string $categoryName = 'Alat Ukur'): array
    {
        $unit = $this->unit('GZI', 'Laboran Gizi');
        $category = $this->category($unit, $categoryName);
        $location = $this->location($unit);

        return [$unit, $category, $location];
    }
}
