<?php

namespace Tests\Feature;

use App\Models\ToolReplacementRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\CreatesSimasetFixtures;
use Tests\TestCase;

class ToolReplacementRequestTest extends TestCase
{
    use CreatesSimasetFixtures;
    use RefreshDatabase;

    public function test_student_can_submit_replacement_request_with_generated_code(): void
    {
        $this->withoutVite();

        $asset = $this->asset($this->unit('GZI', 'Laboran Gizi'), ['quantity' => 2]);

        $this->post(route('tool-replacements.public.store', $asset), $this->replacementPayload([
            'incident_date' => '2026-07-03',
        ]))
            ->assertRedirect();

        $this->assertDatabaseHas('tool_replacement_requests', [
            'asset_id' => $asset->id,
            'student_name' => 'Budi Santoso',
            'student_nim' => '23123456',
            'replacement_quantity' => 1,
            'status' => 'menunggu_verifikasi',
        ]);

        $request = ToolReplacementRequest::firstOrFail();

        $this->assertSame('PGA-GZI-07-2026-001', $request->replacement_code);
    }

    public function test_request_can_be_verified(): void
    {
        $this->withoutVite();

        $unit = $this->unit();
        $asset = $this->asset($unit);
        $request = $this->replacement($asset);
        $manager = $this->signIn('pengelola', $unit);

        $this->patch(route('tool-replacements.status', $request), [
            'status' => 'menunggu_penggantian',
            'laboran_note' => 'Data sudah diverifikasi.',
        ])->assertSessionHas('success');

        $request->refresh();

        $this->assertSame('menunggu_penggantian', $request->status);
        $this->assertSame($manager->id, $request->verified_by);
        $this->assertNotNull($request->verified_at);
    }

    public function test_request_can_be_approved(): void
    {
        $this->withoutVite();

        $unit = $this->unit();
        $request = $this->replacement($this->asset($unit), ['status' => 'menunggu_penggantian']);
        $this->signIn('pengelola', $unit);

        $this->patch(route('tool-replacements.status', $request), [
            'status' => 'sudah_diganti',
        ])->assertSessionHas('success');

        $request->refresh();

        $this->assertSame('sudah_diganti', $request->status);
        $this->assertNotNull($request->received_at);
    }

    public function test_request_can_be_rejected(): void
    {
        $this->withoutVite();

        $unit = $this->unit();
        $request = $this->replacement($this->asset($unit));
        $this->signIn('pengelola', $unit);

        $this->patch(route('tool-replacements.status', $request), [
            'status' => 'ditolak',
            'rejection_reason' => 'Data praktikum tidak lengkap.',
        ])->assertSessionHas('success');

        $request->refresh();

        $this->assertSame('ditolak', $request->status);
        $this->assertSame('Data praktikum tidak lengkap.', $request->rejection_reason);
        $this->assertNull($request->received_at);
    }

    private function replacementPayload(array $overrides = []): array
    {
        return array_merge([
            'student_name' => 'Budi Santoso',
            'student_nim' => '23123456',
            'student_semester' => '4',
            'prodi_kelas' => 'D3 Gizi A',
            'practicum_name' => 'Praktikum Pengukuran',
            'incident_date' => '2026-07-03',
            'replacement_quantity' => 1,
            'damage_description' => 'Alat rusak saat praktikum.',
            'whatsapp_number' => '6281234567890',
        ], $overrides);
    }
}
