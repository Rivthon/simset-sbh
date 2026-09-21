<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\CreatesSimasetFixtures;
use Tests\TestCase;

class ReportControllerTest extends TestCase
{
    use CreatesSimasetFixtures;
    use RefreshDatabase;

    public function test_report_page_can_be_displayed(): void
    {
        $this->withoutVite();

        $this->asset($this->unit(), ['name' => 'Mikroskop Rusak', 'kondisi_aset' => 'rusak']);

        $this->actingAs($this->user('admin'))
            ->get(route('reports.index'))
            ->assertOk()
            ->assertViewIs('reports.index')
            ->assertViewHasAll(['totalAssets', 'byCondition', 'reportAssets']);
    }

    public function test_report_filter_works_correctly(): void
    {
        $this->withoutVite();

        $gizi = $this->unit('GZI', 'Laboran Gizi');
        $farmasi = $this->unit('FAR', 'Laboran Farmasi');
        $giziAsset = $this->asset($gizi, ['name' => 'Timbangan Rusak', 'kondisi_aset' => 'rusak']);
        $farmasiAsset = $this->asset($farmasi, ['name' => 'Mikroskop Rusak', 'kondisi_aset' => 'rusak']);

        $this->actingAs($this->user('admin'))
            ->get(route('reports.index', [
                'unit_id' => $gizi->id,
                'kondisi_aset' => 'rusak',
                'report_scope' => 'all',
            ]))
            ->assertOk()
            ->assertSee($giziAsset->name)
            ->assertDontSee($farmasiAsset->name)
            ->assertViewHas('totalAssets', 1);
    }

    public function test_report_export_works_correctly(): void
    {
        $this->withoutVite();

        $unit = $this->unit();
        $this->asset($unit, ['name' => 'Timbangan Export', 'kondisi_aset' => 'rusak']);

        $response = $this->actingAs($this->user('admin'))
            ->get(route('assets.export', [
                'unit_id' => $unit->id,
                'kondisi_aset' => 'rusak',
            ]));

        $response->assertOk();
        $this->assertSame(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            $response->headers->get('content-type')
        );
        $this->assertStringContainsString('attachment;', $response->headers->get('content-disposition'));
    }
}
