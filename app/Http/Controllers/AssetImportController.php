<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Container;
use App\Models\Location;
use App\Models\Unit;
use App\Support\AssetClassifier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AssetImportController extends Controller
{
    private const HEADERS = [
        'kode_aset_lama',
        'nama_aset',
        'kategori',
        'lokasi',
        'penyimpanan',
        'jumlah',
        'jumlah_baik',
        'jumlah_sedang',
        'jumlah_rusak',
        'jumlah_hilang',
        'satuan',
        'cara_identifikasi_aset',
        'keterangan',
    ];

    public function create(Request $request): View
    {
        return view('assets.import', $this->referenceData($request));
    }

    public function template(Request $request): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Import Aset');
        $sheet->fromArray(self::HEADERS, null, 'A1');
        $sheet->fromArray([
            '02.1.24.BarAbu3.01',
            'Barbel Abu-Abu 3 Kg',
            'Alat Ukur',
            'Antropometri',
            'Box/Rak Antropometri',
            1,
            1,
            0,
            0,
            0,
            'unit',
            'QR Individual',
            'Contoh aset Laboratorium Gizi',
        ], null, 'A2');
        $sheet->fromArray([
            '-',
            'Sendok Ukur',
            'Alat Praktik',
            'Dietetik dan Kuliner',
            'Box Alat Praktik',
            30,
            28,
            2,
            0,
            0,
            'pcs',
            'QR Kelompok',
            'Contoh kondisi campuran: 28 baik dan 2 sedang',
        ], null, 'A3');
        $sheet->fromArray([
            '-',
            'Gelas Ukur',
            'Alat Laboratorium',
            'Kimia',
            'Lemari Alat Kimia',
            10,
            7,
            2,
            1,
            0,
            'pcs',
            'QR Kelompok',
            'Total kondisi wajib sama dengan jumlah',
        ], null, 'A4');

        $sheet->getStyle('A1:M1')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A1:M1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('465FFF');
        $sheet->freezePane('A2');

        foreach (range('A', 'M') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $this->addValidation($sheet, 'L', '"QR Individual,QR Kelompok,individual,group"');

        $reference = $spreadsheet->createSheet();
        $reference->setTitle('Referensi');
        $reference->fromArray(['Jenis', 'Referensi', 'Keterangan'], null, 'A1');
        $row = 2;

        $referenceRows = [
            ['Unit', 'Laboran Gizi', 'Contoh template untuk Laboratorium Gizi'],
            ['Catatan', 'Data contoh boleh dihapus sebelum import', 'Gunakan nama sesuai master data sistem'],
            ['Kategori', 'Alat Ukur', 'Referensi kategori Laboratorium Gizi'],
            ['Kategori', 'Alat Praktik', 'Referensi kategori Laboratorium Gizi'],
            ['Kategori', 'Alat Laboratorium', 'Referensi kategori Laboratorium Gizi'],
            ['Kategori', 'Instrumen', 'Referensi kategori Laboratorium Gizi'],
            ['Kategori', 'Media Praktikum', 'Referensi kategori Laboratorium Gizi'],
            ['Kategori', 'Perabot', 'Referensi kategori Laboratorium Gizi'],
            ['Kategori', 'Lain-Lain', 'Referensi kategori Laboratorium Gizi'],
            ['Lokasi', 'Antropometri', 'Referensi lokasi Laboratorium Gizi'],
            ['Lokasi', 'Dietetik dan Kuliner', 'Referensi lokasi Laboratorium Gizi'],
            ['Lokasi', 'Pendidikan Gizi dan Konsultasi', 'Referensi lokasi Laboratorium Gizi'],
            ['Lokasi', 'Kimia', 'Referensi lokasi Laboratorium Gizi'],
            ['Lokasi', 'Teknologi Pangan', 'Referensi lokasi Laboratorium Gizi'],
            ['Penyimpanan', 'Box/Rak Antropometri', 'Contoh penyimpanan Laboratorium Gizi'],
            ['Penyimpanan', 'Box Alat Praktik', 'Contoh penyimpanan Laboratorium Gizi'],
            ['Penyimpanan', 'Lemari Alat Kimia', 'Contoh penyimpanan Laboratorium Gizi'],
            ['Penyimpanan', 'Rak Teknologi Pangan', 'Contoh penyimpanan Laboratorium Gizi'],
            ['Penyimpanan', 'Lemari Media Praktikum', 'Contoh penyimpanan Laboratorium Gizi'],
            ['cara_identifikasi_aset', 'QR Individual', 'Satu QR untuk satu aset'],
            ['cara_identifikasi_aset', 'QR Kelompok', 'Satu QR untuk beberapa aset sejenis'],
            ['jumlah_baik', 'Bilangan bulat minimal 0', 'Bagian aset dengan kondisi baik'],
            ['jumlah_sedang', 'Bilangan bulat minimal 0', 'Bagian aset dengan kondisi sedang'],
            ['jumlah_rusak', 'Bilangan bulat minimal 0', 'Bagian aset dengan kondisi rusak'],
            ['jumlah_hilang', 'Bilangan bulat minimal 0', 'Bagian aset dengan kondisi hilang'],
            ['Aturan kondisi', 'baik + sedang + rusak + hilang = jumlah', 'Total pembagian kondisi wajib sama dengan jumlah aset'],
        ];

        foreach ($referenceRows as $referenceRow) {
            $reference->fromArray($referenceRow, null, "A{$row}");
            $row++;
        }
        $reference->getStyle('A1:C1')->getFont()->setBold(true);
        foreach (range('A', 'C') as $column) {
            $reference->getColumnDimension($column)->setAutoSize(true);
        }

        return response()->streamDownload(function () use ($spreadsheet): void {
            (new Xlsx($spreadsheet))->save('php://output');
        }, 'template-import-aset.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'unit_id' => [$request->user()->isAdmin() ? 'required' : 'nullable', 'exists:units,id'],
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        $unit = $this->resolveImportUnit($request);

        $rows = $this->readRows($request->file('file')->getRealPath());

        if ($rows === []) {
            return back()->with('error', 'File Excel kosong atau header template tidak ditemukan.');
        }

        $preparedRows = [];
        $errors = [];
        $seenImportKeys = [];
        $seenLegacyCodes = [];

        foreach ($rows as $rowNumber => $row) {
            if (AssetClassifier::isConsumable([
                $row['nama_aset'] ?? '',
                $row['kategori'] ?? '',
                $row['keterangan'] ?? '',
            ])) {
                $errors[] = 'Baris '.$rowNumber.': Data terindikasi BHP dan tidak diimport.';
                continue;
            }

            $row = $this->normalizeLegacyRow($row);
            $row['metode_qr'] = $this->normalizeIdentificationType($row['metode_qr'] ?? null);
            $usesConditionCounts = $this->usesConditionCounts($row);

            $validator = Validator::make($row, [
                'nama_aset' => ['required', 'string', 'max:255'],
                'kategori' => ['required', 'string', 'max:255'],
                'lokasi' => ['nullable', 'string', 'max:255'],
                'penyimpanan' => ['nullable', 'string', 'max:255'],
                'jumlah' => ['required', 'integer', 'min:1'],
                'jumlah_baik' => ['nullable', 'integer', 'min:0'],
                'jumlah_sedang' => ['nullable', 'integer', 'min:0'],
                'jumlah_rusak' => ['nullable', 'integer', 'min:0'],
                'jumlah_hilang' => ['nullable', 'integer', 'min:0'],
                'satuan' => ['required', 'string', 'max:50'],
                'metode_qr' => ['required', 'in:individual,group'],
                'keterangan' => ['nullable', 'string'],
            ], [], [
                'nama_aset' => 'nama aset',
                'metode_qr' => 'cara identifikasi aset',
                'jumlah_baik' => 'jumlah baik',
                'jumlah_sedang' => 'jumlah sedang',
                'jumlah_rusak' => 'jumlah rusak',
                'jumlah_hilang' => 'jumlah hilang',
            ]);

            if ($validator->fails()) {
                $errors[] = $this->validationErrorMessage($rowNumber, $validator->errors()->first());
                continue;
            }

            if ($usesConditionCounts) {
                foreach (array_keys(Asset::KONDISI_ASET_LABELS) as $conditionKey) {
                    $row['jumlah_'.$conditionKey] = (int) ($row['jumlah_'.$conditionKey] ?? 0);
                }

                $conditionTotal = collect(['jumlah_baik', 'jumlah_sedang', 'jumlah_rusak', 'jumlah_hilang'])
                    ->sum(fn (string $key): int => (int) $row[$key]);

                if ($conditionTotal !== (int) $row['jumlah']) {
                    $errors[] = 'Baris '.$rowNumber.': Total jumlah baik, sedang, rusak, dan hilang harus sama dengan jumlah aset.';
                    continue;
                }
            } else {
                $condition = Asset::normalizeKondisiAset($row['kondisi_aset'] ?? null) ?? 'baik';

                foreach (array_keys(Asset::KONDISI_ASET_LABELS) as $conditionKey) {
                    $row['jumlah_'.$conditionKey] = $conditionKey === $condition ? (int) $row['jumlah'] : 0;
                }
            }

            $row['kondisi_aset'] = Asset::worstCondition([
                'baik' => (int) $row['jumlah_baik'],
                'sedang' => (int) $row['jumlah_sedang'],
                'rusak' => (int) $row['jumlah_rusak'],
                'hilang' => (int) $row['jumlah_hilang'],
            ]);

            try {
                $data = $this->prepareRow($request, $row, $unit);
                $legacyCode = $this->normalizeDuplicateValue($data['legacy_inventory_code']);

                if ($legacyCode !== null && isset($seenLegacyCodes[$legacyCode])) {
                    $errors[] = 'Baris '.$rowNumber.': Kode aset lama '.$data['legacy_inventory_code'].' duplikat dengan baris '.$seenLegacyCodes[$legacyCode].'.';
                    continue;
                }

                $importKey = $this->duplicateImportKey($data);

                if (isset($seenImportKeys[$importKey])) {
                    $errors[] = 'Baris '.$rowNumber.': Data aset duplikat dengan baris '.$seenImportKeys[$importKey].'.';
                    continue;
                }

                if ($this->matchingAssetQuery($data)->exists()) {
                    $errors[] = 'Baris '.$rowNumber.': Data aset yang sama sudah ada di database.';
                    continue;
                }

                $preparedRows[] = $data;
                $seenImportKeys[$importKey] = $rowNumber;

                if ($legacyCode !== null) {
                    $seenLegacyCodes[$legacyCode] = $rowNumber;
                }
            } catch (\InvalidArgumentException $exception) {
                $errors[] = 'Baris '.$rowNumber.': '.$exception->getMessage();
            }
        }

        if ($preparedRows === []) {
            return back()->withInput()->with('import_errors', $errors);
        }

        DB::transaction(function () use ($preparedRows, $request): void {
            foreach ($preparedRows as $data) {
                $unit = Unit::findOrFail($data['unit_id']);
                $category = Category::findOrFail($data['category_id']);
                $data['asset_code'] = Asset::generateAssetCode($unit, $category);
                $data['created_by'] = $request->user()->id;

                Asset::create($data);
            }
        });

        return back()
            ->with('success', count($preparedRows).' aset berhasil diimport dari Excel. '.count($errors).' baris ditolak.')
            ->with('import_errors', $errors);
    }

    private function readRows(string $path): array
    {
        $sheet = IOFactory::load($path)->getActiveSheet();
        $highestColumn = $sheet->getHighestColumn();
        $highestRow = $sheet->getHighestRow();
        $headers = [];

        foreach ($sheet->rangeToArray("A1:{$highestColumn}1", null, true, true, true)[1] ?? [] as $column => $value) {
            $headers[$column] = $this->normalizeHeader((string) $value);
        }

        if (array_filter($headers) === []) {
            return [];
        }

        $rows = [];
        for ($rowNumber = 2; $rowNumber <= $highestRow; $rowNumber++) {
            $values = $sheet->rangeToArray("A{$rowNumber}:{$highestColumn}{$rowNumber}", null, true, true, true)[$rowNumber] ?? [];

            if (collect($values)->every(fn ($value) => blank($value))) {
                continue;
            }

            $row = [];
            foreach ($headers as $column => $header) {
                if ($header === '') {
                    continue;
                }
                $row[$header] = is_string($values[$column] ?? null) ? trim($values[$column]) : ($values[$column] ?? null);
            }

            $row = array_merge(array_fill_keys(self::HEADERS, null), $row);
            $rows[$rowNumber] = $row;
        }

        return $rows;
    }

    private function prepareRow(Request $request, array $row, Unit $unit): array
    {
        if (! $request->user()->canAccessUnit($unit->id)) {
            throw new \InvalidArgumentException('Unit tidak bisa diakses oleh user ini.');
        }

        $category = $this->resolveCategory($row['kategori'], $unit);
        $container = $this->resolveContainer($row['penyimpanan'] ?? null, $unit);
        $location = $container?->location ?? $this->resolveLocation($row['lokasi'] ?? null, $unit);

        if (filled($row['kode_aset_lama'] ?? null)) {
            $duplicate = Asset::where('unit_id', $unit->id)
                ->where('legacy_inventory_code', $row['kode_aset_lama'])
                ->exists();

            if ($duplicate) {
                throw new \InvalidArgumentException("Kode aset lama {$row['kode_aset_lama']} sudah ada.");
            }
        }

        return [
            'unit_id' => $unit->id,
            'category_id' => $category->id,
            'location_id' => $location?->id,
            'container_id' => $container?->id,
            'identification_type' => $row['metode_qr'],
            'quantity' => (int) $row['jumlah'],
            'satuan' => $row['satuan'],
            'legacy_inventory_code' => $row['kode_aset_lama'] ?: null,
            'name' => $row['nama_aset'],
            'kondisi_aset' => $row['kondisi_aset'],
            'jumlah_baik' => (int) $row['jumlah_baik'],
            'jumlah_sedang' => (int) $row['jumlah_sedang'],
            'jumlah_rusak' => (int) $row['jumlah_rusak'],
            'jumlah_hilang' => (int) $row['jumlah_hilang'],
            'description' => $row['keterangan'] ?: null,
        ];
    }

    private function usesConditionCounts(array $row): bool
    {
        foreach (['jumlah_baik', 'jumlah_sedang', 'jumlah_rusak', 'jumlah_hilang'] as $key) {
            if (($row[$key] ?? null) !== null && ($row[$key] ?? '') !== '') {
                return true;
            }
        }

        return false;
    }

    private function duplicateImportKey(array $data): string
    {
        $columns = [
            'unit_id', 'category_id', 'location_id', 'container_id', 'identification_type',
            'quantity', 'satuan', 'legacy_inventory_code', 'name', 'kondisi_aset',
            'jumlah_baik', 'jumlah_sedang', 'jumlah_rusak', 'jumlah_hilang', 'description',
        ];

        return hash('sha256', json_encode(array_map(
            fn (string $column) => $this->normalizeDuplicateValue($data[$column] ?? null),
            $columns,
        ), JSON_THROW_ON_ERROR));
    }

    private function matchingAssetQuery(array $data): Builder
    {
        return Asset::query()
            ->where('unit_id', $data['unit_id'])
            ->where('category_id', $data['category_id'])
            ->where('location_id', $data['location_id'])
            ->where('container_id', $data['container_id'])
            ->where('identification_type', $data['identification_type'])
            ->where('quantity', $data['quantity'])
            ->where('satuan', $data['satuan'])
            ->where('legacy_inventory_code', $data['legacy_inventory_code'])
            ->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower(trim($data['name']))])
            ->where('kondisi_aset', $data['kondisi_aset'])
            ->where('jumlah_baik', $data['jumlah_baik'])
            ->where('jumlah_sedang', $data['jumlah_sedang'])
            ->where('jumlah_rusak', $data['jumlah_rusak'])
            ->where('jumlah_hilang', $data['jumlah_hilang'])
            ->where('description', $data['description']);
    }

    private function normalizeDuplicateValue(mixed $value): mixed
    {
        if (! is_string($value)) {
            return $value;
        }

        $value = preg_replace('/\s+/u', ' ', trim($value));

        return $value === '' ? null : mb_strtolower($value);
    }

    private function resolveImportUnit(Request $request): Unit
    {
        if ($request->user()->isUnitScopedManager()) {
            return Unit::findOrFail($request->user()->unit_id);
        }

        $unit = Unit::whereKey($request->integer('unit_id'))
            ->where('status', 'active')
            ->first();

        if (! $unit) {
            throw new \InvalidArgumentException('Unit import tidak ditemukan atau tidak aktif.');
        }

        if (! $request->user()->canAccessUnit($unit->id)) {
            throw new \InvalidArgumentException('Anda tidak memiliki akses ke unit ini.');
        }

        return $unit;
    }

    private function resolveCategory(string $name, Unit $unit): Category
    {
        $category = Category::where('name', $name)
            ->where('status', 'active')
            ->where(function (Builder $query) use ($unit): void {
                $query->whereNull('unit_id')->orWhere('unit_id', $unit->id);
            })
            ->first();

        if (! $category) {
            throw new \InvalidArgumentException('Kategori tidak ditemukan.');
        }

        return $category;
    }

    private function resolveLocation(?string $name, Unit $unit): ?Location
    {
        if (blank($name)) {
            return null;
        }

        $location = Location::query()
            ->where('name', $name)
            ->where('unit_id', $unit->id)
            ->where('status', 'active')
            ->first();

        if (! $location) {
            throw new \InvalidArgumentException('Lokasi tidak ditemukan.');
        }

        return $location;
    }

    private function resolveContainer(?string $name, Unit $unit): ?Container
    {
        if (blank($name)) {
            return null;
        }

        $container = Container::with('location')
            ->where(function (Builder $query) use ($name): void {
                $query->where('name', $name)->orWhere('code', $name);
            })
            ->where('unit_id', $unit->id)
            ->where('status', 'active')
            ->first();

        if (! $container) {
            throw new \InvalidArgumentException('Penyimpanan tidak ditemukan.');
        }

        return $container;
    }

    private function normalizeHeader(string $header): string
    {
        return Str::of($header)->lower()->trim()->replace([' ', '-'], '_')->replaceMatches('/[^a-z0-9_]/', '')->toString();
    }

    private function addValidation($sheet, string $column, string $formula): void
    {
        for ($row = 2; $row <= 500; $row++) {
            $validation = $sheet->getCell("{$column}{$row}")->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_STOP);
            $validation->setAllowBlank(false);
            $validation->setShowDropDown(true);
            $validation->setFormula1($formula);
        }
    }

    private function referenceData(Request $request): array
    {
        return [
            'units' => $this->availableUnits($request)->get(),
            'categories' => $this->availableCategories($request)->get(),
            'locations' => $this->availableLocations($request)->get(),
            'containers' => $this->availableContainers($request)->get(),
        ];
    }

    private function availableUnits(Request $request): Builder
    {
        return Unit::where('status', 'active')
            ->when($request->user()->isUnitScoped(), fn (Builder $query) => $query->whereKey($request->user()->unit_id))
            ->orderBy('name');
    }

    private function availableCategories(Request $request): Builder
    {
        return Category::with('unit')
            ->where('status', 'active')
            ->when($request->user()->isUnitScoped(), function (Builder $query) use ($request): void {
                $query->where(function (Builder $query) use ($request): void {
                    $query->whereNull('unit_id')->orWhere('unit_id', $request->user()->unit_id);
                });
            })
            ->orderBy('name');
    }

    private function availableLocations(Request $request): Builder
    {
        return Location::with('unit')
            ->where('status', 'active')
            ->when($request->user()->isUnitScoped(), fn (Builder $query) => $query->where('unit_id', $request->user()->unit_id))
            ->orderBy('name');
    }

    private function availableContainers(Request $request): Builder
    {
        return Container::with(['unit', 'location'])
            ->where('status', 'active')
            ->visibleFor($request->user())
            ->orderBy('name');
    }

    private function firstAvailableUnitReference(Request $request): string
    {
        return $this->availableUnits($request)->value('name') ?? 'NAMA_UNIT';
    }

    private function firstAvailableCategoryReference(Request $request): string
    {
        return $this->availableCategories($request)->value('name') ?? 'NAMA_KATEGORI';
    }

    private function firstAvailableLocationReference(Request $request): string
    {
        return $this->availableLocations($request)->value('name') ?? 'NAMA_LOKASI';
    }

    private function normalizeLegacyRow(array $row): array
    {
        return array_merge($row, [
            'kode_aset_lama' => $row['kode_aset_lama'] ?? $row['kode_inventaris_lama'] ?? null,
            'kategori' => $row['kategori'] ?? $row['nama_kategori'] ?? $row['kode_kategori'] ?? null,
            'lokasi' => $row['lokasi'] ?? $row['nama_lokasi'] ?? $row['kode_lokasi'] ?? null,
            'penyimpanan' => $row['penyimpanan'] ?? $row['kode_tempat_penyimpanan'] ?? null,
            'metode_qr' => $row['metode_qr'] ?? $row['cara_identifikasi_aset'] ?? $row['jenis_identifikasi'] ?? null,
            'keterangan' => $row['keterangan'] ?? $row['deskripsi'] ?? null,
        ]);
    }

    private function normalizeIdentificationType(?string $value): ?string
    {
        $normalized = Str::of((string) $value)->lower()->trim()->replace([' ', '-'], '_')->toString();

        return match ($normalized) {
            'qr_individual', 'individual', 'individu', 'ditempel_langsung_ke_aset' => 'individual',
            'qr_kelompok', 'kelompok', 'group', 'digabung_dalam_satu_kelompok_aset' => 'group',
            default => null,
        };
    }

    private function validationErrorMessage(int $rowNumber, string $message): string
    {
        $message = strtolower($message);

        if (str_contains($message, 'nama aset')) {
            return "Baris {$rowNumber}: Nama aset wajib diisi.";
        }

        if (str_contains($message, 'jumlah baik')
            || str_contains($message, 'jumlah sedang')
            || str_contains($message, 'jumlah rusak')
            || str_contains($message, 'jumlah hilang')) {
            return "Baris {$rowNumber}: Kolom pembagian kondisi harus berupa bilangan bulat minimal 0.";
        }

        if (str_contains($message, 'jumlah')) {
            return "Baris {$rowNumber}: Jumlah harus angka minimal 1.";
        }

        if (str_contains($message, 'metode qr')) {
            return "Baris {$rowNumber}: Cara identifikasi aset wajib diisi dengan nilai yang valid.";
        }

        if (str_contains($message, 'satuan')) {
            return "Baris {$rowNumber}: Satuan wajib diisi.";
        }

        return "Baris {$rowNumber}: {$message}";
    }
}
