<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\CreatesSimasetFixtures;
use Tests\TestCase;

class ContainerAssetRemovalTest extends TestCase
{
    use CreatesSimasetFixtures;
    use RefreshDatabase;

    public function test_admin_can_remove_asset_from_container_without_changing_location(): void
    {
        $unit = $this->unit();
        $location = $this->location($unit);
        $container = $this->container($unit, $location);
        $asset = $this->asset($unit, compact('location', 'container'));

        $this->actingAs($this->user('admin'))
            ->delete(route('containers.assets.destroy', [$container, $asset]))
            ->assertRedirect(route('containers.show', $container))
            ->assertSessionHas('success');

        $asset->refresh();

        $this->assertNull($asset->container_id);
        $this->assertSame($location->id, $asset->location_id);
        $this->assertDatabaseHas('assets', ['id' => $asset->id]);
    }

    public function test_manager_cannot_remove_asset_from_another_unit_container(): void
    {
        $ownUnit = $this->unit('OWN', 'Unit Sendiri');
        $otherUnit = $this->unit('OTH', 'Unit Lain');
        $location = $this->location($otherUnit);
        $container = $this->container($otherUnit, $location);
        $asset = $this->asset($otherUnit, compact('location', 'container'));

        $this->actingAs($this->user('pengelola', $ownUnit))
            ->delete(route('containers.assets.destroy', [$container, $asset]))
            ->assertForbidden();

        $this->assertSame($container->id, $asset->fresh()->container_id);
    }

    public function test_asset_must_belong_to_container_being_modified(): void
    {
        $unit = $this->unit();
        $location = $this->location($unit);
        $container = $this->container($unit, $location, 'Lemari Pertama');
        $otherContainer = $this->container($unit, $location, 'Lemari Kedua');
        $asset = $this->asset($unit, ['location' => $location, 'container' => $otherContainer]);

        $this->actingAs($this->user('admin'))
            ->delete(route('containers.assets.destroy', [$container, $asset]))
            ->assertStatus(422);

        $this->assertSame($otherContainer->id, $asset->fresh()->container_id);
    }

    public function test_leader_does_not_see_remove_asset_action(): void
    {
        $unit = $this->unit();
        $location = $this->location($unit);
        $container = $this->container($unit, $location);
        $this->asset($unit, compact('location', 'container'));

        $this->actingAs($this->user('pimpinan'))
            ->get(route('containers.show', $container))
            ->assertOk()
            ->assertDontSee('Keluarkan');
    }
}
