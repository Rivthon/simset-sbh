<?php

namespace Tests\Feature;

use App\Models\Asset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\Feature\Concerns\CreatesSimasetFixtures;
use Tests\TestCase;

class AssetImportControllerTest extends TestCase
{
    use CreatesSimasetFixtures;
    use RefreshDatabase;

    public function test_asset_import_supports_mixed_condition_counts(): void
    {
        $unit = $this->unit();
        $this->category($unit);
        $this->location($unit);
        $path = $this->makeImportFile([
            'kode_aset_lama', 'nama_aset', 'kategori', 'lokasi', 'penyimpanan', 'jumlah',
            'jumlah_baik', 'jumlah_sedang', 'jumlah_rusak', 'jumlah_hilang',
            'satuan', 'cara_identifikasi_aset', 'keterangan',
        ], [
            '-', 'Timbangan Import Campuran', 'Alat Ukur', 'Ruang Praktikum', null, 8,
            6, 2, 0, 0, 'pcs', 'QR Kelompok', 'Aset hasil import',
        ]);

        try {
            $this->actingAs($this->user('admin'))
                ->post(route('assets.import.store'), [
                    'unit_id' => $unit->id,
                    'file' => new UploadedFile($path, 'import-aset.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true),
                ])
                ->assertSessionHas('success');
        } finally {
            @unlink($path);
        }

        $asset = Asset::where('name', 'Timbangan Import Campuran')->firstOrFail();

        $this->assertSame(8, $asset->quantity);
        $this->assertSame(['baik' => 6, 'sedang' => 2, 'rusak' => 0, 'hilang' => 0], $asset->conditionCounts());
        $this->assertSame('sedang', $asset->kondisi_aset);
    }

    public function test_asset_import_rejects_condition_total_that_differs_from_quantity(): void
    {
        $unit = $this->unit();
        $this->category($unit);
        $this->location($unit);
        $path = $this->makeImportFile([
            'nama_aset', 'kategori', 'lokasi', 'jumlah', 'jumlah_baik', 'jumlah_sedang',
            'jumlah_rusak', 'jumlah_hilang', 'satuan', 'cara_identifikasi_aset',
        ], [
            'Timbangan Import Tidak Valid', 'Alat Ukur', 'Ruang Praktikum', 8, 5, 2, 0, 0, 'pcs', 'QR Kelompok',
        ]);

        try {
            $this->actingAs($this->user('admin'))
                ->post(route('assets.import.store'), [
                    'unit_id' => $unit->id,
                    'file' => new UploadedFile($path, 'import-aset.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true),
                ])
                ->assertSessionHas('import_errors');
        } finally {
            @unlink($path);
        }

        $this->assertDatabaseMissing('assets', ['name' => 'Timbangan Import Tidak Valid']);
    }

    public function test_legacy_asset_import_allocates_all_quantity_to_single_condition(): void
    {
        $unit = $this->unit();
        $this->category($unit);
        $this->location($unit);
        $path = $this->makeImportFile([
            'nama_aset', 'kategori', 'lokasi', 'jumlah', 'satuan', 'cara_identifikasi_aset', 'kondisi_aset',
        ], [
            'Timbangan Import Lama', 'Alat Ukur', 'Ruang Praktikum', 4, 'pcs', 'QR Kelompok', 'rusak',
        ]);

        try {
            $this->actingAs($this->user('admin'))
                ->post(route('assets.import.store'), [
                    'unit_id' => $unit->id,
                    'file' => new UploadedFile($path, 'import-aset-lama.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true),
                ])
                ->assertSessionHas('success');
        } finally {
            @unlink($path);
        }

        $asset = Asset::where('name', 'Timbangan Import Lama')->firstOrFail();

        $this->assertSame(['baik' => 0, 'sedang' => 0, 'rusak' => 4, 'hilang' => 0], $asset->conditionCounts());
    }

    public function test_asset_import_rejects_duplicate_rows_in_the_same_file(): void
    {
        $unit = $this->unit();
        $this->category($unit);
        $this->location($unit);
        $headers = [
            'nama_aset', 'kategori', 'lokasi', 'jumlah', 'jumlah_baik', 'jumlah_sedang',
            'jumlah_rusak', 'jumlah_hilang', 'satuan', 'cara_identifikasi_aset',
        ];
        $row = ['Aset Duplikat', 'Alat Ukur', 'Ruang Praktikum', 2, 2, 0, 0, 0, 'pcs', 'QR Kelompok'];
        $path = $this->makeImportFile($headers, [$row, $row]);

        try {
            $this->actingAs($this->user('admin'))
                ->post(route('assets.import.store'), [
                    'unit_id' => $unit->id,
                    'file' => new UploadedFile($path, 'duplikat.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true),
                ])
                ->assertSessionHas('import_errors');
        } finally {
            @unlink($path);
        }

        $this->assertSame(1, Asset::where('name', 'Aset Duplikat')->count());
    }

    public function test_asset_import_rejects_a_row_that_already_exists_in_database(): void
    {
        $unit = $this->unit();
        $category = $this->category($unit);
        $location = $this->location($unit);
        $this->asset($unit, [
            'category' => $category,
            'location' => $location,
            'name' => 'Aset Sudah Ada',
        ]);
        $path = $this->makeImportFile([
            'nama_aset', 'kategori', 'lokasi', 'jumlah', 'jumlah_baik', 'jumlah_sedang',
            'jumlah_rusak', 'jumlah_hilang', 'satuan', 'cara_identifikasi_aset',
        ], [[
            'Aset Sudah Ada', 'Alat Ukur', 'Ruang Praktikum', 1, 1, 0, 0, 0, 'unit', 'QR Individual',
        ]]);

        try {
            $this->actingAs($this->user('admin'))
                ->post(route('assets.import.store'), [
                    'unit_id' => $unit->id,
                    'file' => new UploadedFile($path, 'sudah-ada.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true),
                ])
                ->assertSessionHas('import_errors');
        } finally {
            @unlink($path);
        }

        $this->assertSame(1, Asset::where('name', 'Aset Sudah Ada')->count());
    }

    private function makeImportFile(array $headers, array $row): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($headers, null, 'A1');
        $rows = isset($row[0]) && is_array($row[0]) ? $row : [$row];
        $sheet->fromArray($rows, null, 'A2');
        $path = tempnam(sys_get_temp_dir(), 'simaset-import-').'.xlsx';
        (new Xlsx($spreadsheet))->save($path);

        return $path;
    }
}
