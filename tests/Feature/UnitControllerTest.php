<?php

namespace Tests\Feature;

use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\CreatesSimasetFixtures;
use Tests\TestCase;

class UnitControllerTest extends TestCase
{
    use CreatesSimasetFixtures;
    use RefreshDatabase;

    public function test_admin_can_view_units_page(): void
    {
        $this->withoutVite();

        $this->unit('GZI', 'Laboran Gizi');

        $this->actingAs($this->user('admin'))
            ->get(route('units.index'))
            ->assertOk()
            ->assertViewIs('units.index')
            ->assertSee('Laboran Gizi');
    }

    public function test_admin_can_create_unit(): void
    {
        $this->withoutVite();

        $this->actingAs($this->user('admin'))
            ->post(route('units.store'), [
                'name' => 'Laboran Analis Kesehatan',
                'status' => 'active',
            ])
            ->assertRedirect(route('units.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('units', [
            'name' => 'Laboran Analis Kesehatan',
            'code' => Unit::where('name', 'Laboran Analis Kesehatan')->firstOrFail()->code,
            'status' => 'active',
        ]);
    }

    public function test_duplicate_unit_code_is_rejected_by_generating_unique_code(): void
    {
        $this->withoutVite();

        $admin = $this->user('admin');
        $initialCount = Unit::where('name', 'Laboran Gizi')->count();

        $this->actingAs($admin)->post(route('units.store'), [
            'name' => 'Laboran Gizi',
            'status' => 'active',
        ]);

        $this->actingAs($admin)->post(route('units.store'), [
            'name' => 'Laboran Gizi',
            'status' => 'active',
        ])->assertRedirect(route('units.index'));

        $codes = Unit::where('name', 'Laboran Gizi')->pluck('code')->all();

        $this->assertCount($initialCount + 2, $codes);
        $this->assertCount($initialCount + 2, array_unique($codes));
    }

    public function test_required_unit_fields_are_validated(): void
    {
        $this->withoutVite();

        $this->actingAs($this->user('admin'))
            ->post(route('units.store'), [])
            ->assertSessionHasErrors(['name', 'status']);
    }
}
