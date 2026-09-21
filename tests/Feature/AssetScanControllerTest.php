<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\CreatesSimasetFixtures;
use Tests\TestCase;

class AssetScanControllerTest extends TestCase
{
    use CreatesSimasetFixtures;
    use RefreshDatabase;

    public function test_valid_qr_code_displays_asset_detail(): void
    {
        $this->withoutVite();

        $asset = $this->asset($this->unit(), [
            'name' => 'Mikroskop Binokuler',
            'qr_code' => 'SIMASET-AST-VALID001',
        ]);

        $this->get(route('qr.assets.show', $asset->qr_code))
            ->assertOk()
            ->assertViewIs('assets.scan')
            ->assertViewHas('asset', fn ($scannedAsset): bool => $scannedAsset->is($asset))
            ->assertSee('Mikroskop Binokuler');
    }

    public function test_invalid_qr_code_displays_not_found_page(): void
    {
        $this->withoutVite();

        $this->get(route('qr.assets.show', 'SIMASET-AST-TIDAKADA'))
            ->assertOk()
            ->assertViewIs('assets.scan-not-found')
            ->assertSee('SIMASET-AST-TIDAKADA');
    }

    public function test_asset_detail_matches_scanned_qr_code(): void
    {
        $this->withoutVite();

        $unit = $this->unit();
        $firstAsset = $this->asset($unit, ['name' => 'Aset Pertama', 'qr_code' => 'SIMASET-AST-FIRST']);
        $secondAsset = $this->asset($unit, ['name' => 'Aset Kedua', 'qr_code' => 'SIMASET-AST-SECOND']);

        $this->get(route('qr.assets.show', $secondAsset->qr_code))
            ->assertOk()
            ->assertViewHas('asset', fn ($asset): bool => $asset->is($secondAsset))
            ->assertSee('Aset Kedua')
            ->assertDontSee($firstAsset->asset_code);
    }
}
