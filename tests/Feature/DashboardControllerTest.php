<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\InventoryCheck;
use App\Models\ToolReplacementRequest;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\CreatesSimasetFixtures;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use CreatesSimasetFixtures;
    use RefreshDatabase;

    public function test_dashboard_shows_correct_statistics(): void
    {
        $this->withoutVite();

        $unit = $this->unit();
        $admin = $this->signIn('admin');
        $this->asset($unit, ['quantity' => 2, 'name' => 'Timbangan Digital']);
        $this->asset($unit, ['quantity' => 1, 'name' => 'Mikroskop', 'kondisi_aset' => 'rusak']);
        $this->inventoryCheck($unit, $admin);
        $this->replacement($this->asset($unit, ['name' => 'Pipet Ukur']));

        $response = $this->get(route('dashboard'))
            ->assertOk()
            ->assertViewIs('dashboard.admin')
            ->assertViewHas('stats')
            ->assertSee('xl:ml-[280px]', false)
            ->assertDontSee('sidebar-expanded', false)
            ->assertDontSee('$store.sidebar.toggle()', false)
            ->assertDontSee('x-show="open"', false);

        $stats = $response->viewData('stats');

        $this->assertSame(Asset::count(), $stats['assets']);
        $this->assertSame((int) Asset::sum('quantity'), (int) $stats['asset_quantity']);
        $this->assertSame(Unit::count(), $stats['units']);
        $this->assertSame(User::count(), $stats['users']);
        $this->assertSame(InventoryCheck::count(), $stats['inventory_checks']);
        $this->assertSame(ToolReplacementRequest::where('status', 'menunggu_verifikasi')->count(), $stats['tool_replacements_pending']);
    }

    public function test_dashboard_shows_data_based_on_role(): void
    {
        $this->withoutVite();

        $ownUnit = $this->unit('TST', 'Laboran Test');
        $otherUnit = $this->unit('OTH', 'Laboran Lain');
        $this->asset($ownUnit, ['name' => 'Aset Unit Sendiri']);
        $this->asset($otherUnit, ['name' => 'Aset Unit Lain']);
        $manager = $this->signIn('pengelola', $ownUnit);

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertViewIs('dashboard.pengelola')
            ->assertViewHas('stats', fn (array $stats): bool => $stats['assets'] === 1 && $stats['units'] === 1)
            ->assertViewHas('assetByLocation', fn ($locations): bool => $locations->pluck('location.unit_id')->unique()->all() === [$manager->unit_id]);
    }
}
