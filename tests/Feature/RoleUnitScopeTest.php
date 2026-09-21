<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\CreatesSimasetFixtures;
use Tests\TestCase;

class RoleUnitScopeTest extends TestCase
{
    use CreatesSimasetFixtures;
    use RefreshDatabase;

    public function test_manager_only_accesses_own_unit(): void
    {
        $this->withoutVite();

        $ownUnit = $this->unit('GZI', 'Laboran Gizi');
        $otherUnit = $this->unit('FAR', 'Laboran Farmasi');
        $ownAsset = $this->asset($ownUnit, ['name' => 'Aset Unit Sendiri']);
        $otherAsset = $this->asset($otherUnit, ['name' => 'Aset Unit Lain']);

        $this->actingAs($this->user('pengelola', $ownUnit))
            ->get(route('assets.index'))
            ->assertOk()
            ->assertSee($ownAsset->name)
            ->assertDontSee($otherAsset->name);
    }

    public function test_manager_cannot_open_asset_from_another_unit(): void
    {
        $this->withoutVite();

        $ownUnit = $this->unit('GZI', 'Laboran Gizi');
        $otherAsset = $this->asset($this->unit('FAR', 'Laboran Farmasi'));

        $this->actingAs($this->user('pengelola', $ownUnit))
            ->get(route('assets.show', $otherAsset))
            ->assertForbidden();
    }

    public function test_leader_only_accesses_reports(): void
    {
        $this->withoutVite();

        $leader = $this->user('pimpinan');

        $this->actingAs($leader)
            ->get(route('reports.index'))
            ->assertOk();

        $this->actingAs($leader)
            ->get(route('assets.create'))
            ->assertForbidden();
    }

    public function test_student_public_user_cannot_access_admin_pages(): void
    {
        $this->withoutVite();

        $this->get(route('units.index'))
            ->assertRedirect(route('login'));
    }
}
