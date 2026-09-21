<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Container;
use App\Models\InventoryCheck;
use App\Models\InventoryCheckItem;
use App\Models\Location;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InventoryCheckController extends Controller
{
    private const CONDITION_IMPORT_HEADERS = [
        'kode_aset',
        'jumlah_aktual',
        'jumlah_baik',
        'jumlah_sedang',
        'jumlah_rusak',
        'jumlah_hilang',
    ];

    public function index(Request $request): View
    {
        $checks = InventoryCheck::with(['unit', 'creator'])
            ->visibleFor($request->user())
            ->orderByDesc('id')
            ->paginate(10);

        return view('inventory_checks.index', compact('checks'));
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()->hasRole(['admin', 'pengelola']), 403);

        return view('inventory_checks.create', [
            'units' => $this->labUnits($request),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->hasRole(['admin', 'pengelola']), 403);

        $data = $request->validate([
            'unit_id' => ['required', 'exists:units,id'],
            'semester' => ['required', 'in:Ganjil,Genap'],
            'tahun_akademik' => ['required', 'string', 'max:20'],
            'tanggal_pemeriksaan' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $unit = Unit::findOrFail($data['unit_id']);

        if ($request->user()->isPengelola()) {
            abort_unless((int) $request->user()->unit_id === (int) $unit->id, 403);
        }

        $data['tahun_akademik'] = trim($data['tahun_akademik']);

        $existingCheck = InventoryCheck::where('unit_id', $unit->id)
            ->where('semester', $data['semester'])
            ->where('tahun_akademik', $data['tahun_akademik'])
            ->first();

        if ($existingCheck) {
            return back()
                ->withInput()
                ->with('error', 'Sesi pemeriksaan untuk unit, tahun akademik, dan 
                semester ini sudah ada. Gunakan sesi yang sudah tersedia.');
        }

        $check = InventoryCheck::create([
            ...$data,
            'periode' => $data['tahun_akademik'],
            'created_by' => $request->user()->id,
            'check_code' => $this->generateCode(),
            'status' => 'ongoing',
        ]);

        $this->prepareCheckItems($check);

        return redirect()->route('inventory-checks.show', $check)->with('success', 
        'Sesi pemeriksaan inventaris berhasil dibuat.');
    }

    public function show(Request $request, InventoryCheck $inventoryCheck): View
    {
        $this->authorizeCheck($request, $inventoryCheck);
        if ($inventoryCheck->status !== 'completed') {
            $this->prepareCheckItems($inventoryCheck);
        }

        return view('inventory_checks.show', [
            'check' => $inventoryCheck->load(['unit', 'creator', 'items.asset.category', 
            'items.asset.location', 'items.asset.container']),
            'progress' => $this->progress($inventoryCheck),
            'locationProgress' => $this->locationProgress($inventoryCheck),
        ]);
    }

    public function update(Request $request, InventoryCheck $inventoryCheck): RedirectResponse
    {
        abort_unless($request->user()->hasRole(['admin', 'pengelola']), 403);
        $this->authorizeCheck($request, $inventoryCheck);
        abort_if($inventoryCheck->status === 'completed', 403, 'Pemeriksaan yang sudah 
        selesai tidak bisa diubah.');

        $data = $request->validate([
            'items' => ['array'],
            'items.*.jumlah_aktual' => ['required', 'integer', 'min:0'],
            'items.*.jumlah_baik' => ['required', 'integer', 'min:0'],
            'items.*.jumlah_sedang' => ['required', 'integer', 'min:0'],
            'items.*.jumlah_rusak' => ['required', 'integer', 'min:0'],
            'items.*.jumlah_hilang' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:draft,ongoing,completed'],
        ]);

        foreach ($data['items'] ?? [] as $itemId => $itemData) {
            $item = $inventoryCheck->items()->with('asset')->whereKey($itemId)->first();

            if (! $item) {
                continue;
            }

            $actualQuantity = (int) $itemData['jumlah_aktual'];
            $conditionTotal = (int) $itemData['jumlah_baik']
                + (int) $itemData['jumlah_sedang']
                + (int) $itemData['jumlah_rusak']
                + (int) $itemData['jumlah_hilang'];

            if ($conditionTotal !== $actualQuantity) {
                return back()->withInput()->with('error', 'Total jumlah kondisi harus sama 
                dengan jumlah aktual.');
            }

            $result = $this->checkResult((int) $item->jumlah_sistem, $actualQuantity, $conditionTotal);
            $this->kondisiAsetFromCounts(
                (int) $itemData['jumlah_baik'],
                (int) $itemData['jumlah_sedang'],
                (int) $itemData['jumlah_rusak'],
                (int) $itemData['jumlah_hilang'],
            );

            $itemData['jumlah_aktual'] = $actualQuantity;
            $itemData['jumlah_sistem'] = (int) $item->jumlah_sistem;
            $itemData['hasil_pemeriksaan'] = $result;

            $item->update($itemData);
        }

        if ($data['status'] === 'completed') {
            $this->prepareCheckItems($inventoryCheck);

            $uncheckedCount = $this->uncheckedItemCount($inventoryCheck);

            if ($uncheckedCount > 0) {
                return back()
                    ->withInput()
                    ->with('error', "Sesi belum bisa diselesaikan. Masih ada {$uncheckedCount} 
                    aset yang belum diperiksa.");
            }
        }

        $inventoryCheck->update(['status' => $data['status']]);

        if ($data['status'] === 'completed') {
            $this->syncLatestAssetCondition($inventoryCheck);
        }

        if ($request->boolean('redirect_to_scan')) {
            $routeParams = ['inventoryCheck' => $inventoryCheck];

            if ($request->filled('location_id')) {
                $routeParams['location_id'] = $request->input('location_id');
            }

            return redirect()
                ->route('inventory-checks.scan.form', $routeParams)
                ->with('success', 'Hasil pemeriksaan berhasil disimpan.');
        }

        return back()->with('success', 'Hasil pemeriksaan berhasil disimpan.');
    }

    public function scanForm(Request $request, InventoryCheck $inventoryCheck): View
    {
        abort_unless($request->user()->hasRole(['admin', 'pengelola']), 403);
        $this->authorizeCheck($request, $inventoryCheck);
        abort_if($inventoryCheck->status === 'completed', 403, 'Pemeriksaan yang sudah 
        selesai tidak bisa discan lagi.');
        $this->prepareCheckItems($inventoryCheck);
        $locationContext = $this->locationContext($request, $inventoryCheck);

        return view('inventory_checks.scan-entry', [
            'check' => $inventoryCheck->load('unit'),
            'progress' => $this->progress($inventoryCheck->load('items'), $locationContext),
            'locationContext' => $locationContext,
        ]);
    }

    public function importForm(Request $request, InventoryCheck $inventoryCheck): View
    {
        abort_unless($request->user()->hasRole(['admin', 'pengelola']), 403);
        $this->authorizeCheck($request, $inventoryCheck);
        abort_if($inventoryCheck->status === 'completed', 403, 'Pemeriksaan yang sudah 
        selesai tidak bisa diimport lagi.');
        $locationContext = $this->locationContext($request, $inventoryCheck);

        return view('inventory_checks.import', [
            'check' => $inventoryCheck->load('unit'),
            'locationContext' => $locationContext,
        ]);
    }

    public function conditionTemplate(Request $request, InventoryCheck $inventoryCheck): StreamedResponse
    {
        abort_unless($request->user()->hasRole(['admin', 'pengelola']), 403);
        $this->authorizeCheck($request, $inventoryCheck);
        $locationContext = $this->locationContext($request, $inventoryCheck);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Import Kondisi');
        $sheet->fromArray(self::CONDITION_IMPORT_HEADERS, null, 'A1');

        $sampleAsset = $this->assetQueryForContext($inventoryCheck, $locationContext)->orderBy('name')->first();
        $sheet->fromArray([
            $sampleAsset?->asset_code ?? 'AST-KODE-0001',
            $sampleAsset?->quantity ?? 1,
            $sampleAsset?->quantity ?? 1,
            0,
            0,
            0,
        ], null, 'A2');

        $sheet->getStyle('A1:F1')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A1:F1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('465FFF');
        $sheet->freezePane('A2');

        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return response()->streamDownload(function () use ($spreadsheet): void {
            (new Xlsx($spreadsheet))->save('php://output');
        }, 'template-import-kondisi-stock-opname.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function importConditions(Request $request, InventoryCheck $inventoryCheck): RedirectResponse
    {
        abort_unless($request->user()->hasRole(['admin', 'pengelola']), 403);
        $this->authorizeCheck($request, $inventoryCheck);
        abort_if($inventoryCheck->status === 'completed', 403, 'Pemeriksaan yang sudah 
        selesai tidak bisa diimport lagi.');
        $locationContext = $this->locationContext($request, $inventoryCheck);

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        $rows = $this->readConditionRows($request->file('file')->getRealPath());

        if ($rows === []) {
            return back()->with('error', 'File Excel kosong atau header template tidak ditemukan.');
        }

        $preparedRows = [];
        $errors = [];

        foreach ($rows as $rowNumber => $row) {
            $validator = Validator::make($row, [
                'kode_aset' => ['required', 'string', 'max:255'],
                'jumlah_aktual' => ['required', 'integer', 'min:0'],
                'jumlah_baik' => ['required', 'integer', 'min:0'],
                'jumlah_sedang' => ['required', 'integer', 'min:0'],
                'jumlah_rusak' => ['required', 'integer', 'min:0'],
                'jumlah_hilang' => ['required', 'integer', 'min:0'],
            ]);

            if ($validator->fails()) {
                $errors[] = 'Baris '.$rowNumber.': '.$validator->errors()->first();
                continue;
            }

            try {
                $preparedRows[] = $this->prepareConditionRow($inventoryCheck, $row, $locationContext);
            } catch (\InvalidArgumentException $exception) {
                $errors[] = 'Baris '.$rowNumber.': '.$exception->getMessage();
            }
        }

        if ($errors !== []) {
            return back()->withInput()->with('import_errors', $errors);
        }

        DB::transaction(function () use ($preparedRows, $inventoryCheck): void {
            foreach ($preparedRows as $row) {
                InventoryCheckItem::updateOrCreate(
                    [
                        'inventory_check_id' => $inventoryCheck->id,
                        'asset_id' => $row['asset_id'],
                    ],
                    $row['data'],
                );
            }
        });

        return redirect()
            ->route('inventory-checks.show', $inventoryCheck)
            ->with('success', count($preparedRows).' hasil pemeriksaan berhasil diimport.');
    }

    public function processScan(Request $request, InventoryCheck $inventoryCheck): RedirectResponse
    {
        abort_unless($request->user()->hasRole(['admin', 'pengelola']), 403);
        $this->authorizeCheck($request, $inventoryCheck);
        abort_if($inventoryCheck->status === 'completed', 403, 'Pemeriksaan yang sudah selesai tidak bisa discan lagi.');

        $data = $request->validate([
            'scan_code' => ['required', 'string', 'max:255'],
        ], [], [
            'scan_code' => 'kode QR atau kode aset',
        ]);

        $routeParams = [
            'inventoryCheck' => $inventoryCheck,
            'qrCode' => $this->normalizeScanCode($data['scan_code']),
        ];

        if ($request->filled('location_id')) {
            $routeParams['location_id'] = $request->input('location_id');
        }

        return redirect()->route('inventory-checks.scan', $routeParams);
    }

    public function scan(Request $request, InventoryCheck $inventoryCheck, string $qrCode): View|RedirectResponse
    {
        abort_unless($request->user()->hasRole(['admin', 'pengelola']), 403);
        $this->authorizeCheck($request, $inventoryCheck);
        abort_if($inventoryCheck->status === 'completed', 403, 'Pemeriksaan yang sudah selesai tidak bisa discan lagi.');
        $locationContext = $this->locationContext($request, $inventoryCheck);
        $scanFormRouteParams = $this->scanFormRouteParams($inventoryCheck, $locationContext);

        $code = $this->normalizeScanCode($qrCode);
        $asset = $this->findScannedAsset($code);

        if ($asset) {
            if ((int) $asset->unit_id !== (int) $inventoryCheck->unit_id) {
                return redirect()
                    ->route('inventory-checks.scan.form', $scanFormRouteParams)
                    ->with('error', 'Aset ini tidak termasuk dalam unit pemeriksaan.');
            }

            if (! $this->assetMatchesLocationContext($asset, $locationContext)) {
                return redirect()
                    ->route('inventory-checks.scan.form', $scanFormRouteParams)
                    ->with('error', 'Aset ini tidak termasuk dalam lokasi pemeriksaan.');
            }

            $item = $this->ensureCheckItem($inventoryCheck, $asset)->load(['asset.category', 'asset.location', 'asset.container', 'asset.latestCompletedInventoryCheckItem']);

            return view('inventory_checks.scan', [
                'check' => $inventoryCheck->load('unit'),
                'type' => 'asset',
                'asset' => $asset,
                'container' => null,
                'items' => collect([$item]),
                'locationContext' => $locationContext,
            ]);
        }

        $container = Container::with(['unit', 'location.unit', 'assets.category', 'assets.location', 'assets.container', 'assets.latestCompletedInventoryCheckItem'])
            ->where(function (Builder $query) use ($code): void {
                if (ctype_digit($code)) {
                    $query->orWhereKey((int) $code);
                }

                $query->orWhere('qr_code', $code)
                    ->orWhere('code', $code);
            })
            ->first();

        if (! $container) {
            return redirect()
                ->route('inventory-checks.scan.form', $scanFormRouteParams)
                ->with('error', 'Kode QR atau kode aset tidak ditemukan pada data aset atau penyimpanan.');
        }

        if ((int) ($container->unit_id ?: $container->location?->unit_id) !== (int) $inventoryCheck->unit_id) {
            return redirect()
                ->route('inventory-checks.scan.form', $scanFormRouteParams)
                ->with('error', 'Penyimpanan ini tidak termasuk dalam unit pemeriksaan.');
        }

        if (! $this->containerMatchesLocationContext($container, $locationContext)) {
            return redirect()
                ->route('inventory-checks.scan.form', $scanFormRouteParams)
                ->with('error', 'Penyimpanan ini tidak termasuk dalam lokasi pemeriksaan.');
        }

        $items = $container->assets
            ->filter(fn (Asset $asset): bool => $this->assetMatchesLocationContext($asset, $locationContext))
            ->map(fn (Asset $asset): InventoryCheckItem => $this->ensureCheckItem($inventoryCheck, $asset)->load(['asset.category', 'asset.location', 'asset.container', 'asset.latestCompletedInventoryCheckItem']))
            ->values();

        return view('inventory_checks.scan', [
            'check' => $inventoryCheck->load('unit'),
            'type' => 'container',
            'asset' => null,
            'container' => $container,
            'items' => $items,
            'locationContext' => $locationContext,
        ]);
    }

    private function readConditionRows(string $path): array
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

            $rows[$rowNumber] = array_merge(array_fill_keys(self::CONDITION_IMPORT_HEADERS, null), $row);
        }

        return $rows;
    }

    private function prepareConditionRow(InventoryCheck $check, array $row, array $locationContext): array
    {
        $asset = $this->findScannedAsset((string) $row['kode_aset']);

        if (! $asset) {
            throw new \InvalidArgumentException("Kode aset {$row['kode_aset']} tidak ditemukan.");
        }

        if ((int) $asset->unit_id !== (int) $check->unit_id) {
            throw new \InvalidArgumentException("Kode aset {$row['kode_aset']} bukan milik unit pemeriksaan.");
        }

        if (! $this->assetMatchesLocationContext($asset, $locationContext)) {
            throw new \InvalidArgumentException("Kode aset {$row['kode_aset']} bukan milik lokasi pemeriksaan.");
        }

        $systemQuantity = max(1, (int) $asset->quantity);
        $actualQuantity = (int) $row['jumlah_aktual'];
        $conditionTotal = (int) $row['jumlah_baik']
            + (int) $row['jumlah_sedang']
            + (int) $row['jumlah_rusak']
            + (int) $row['jumlah_hilang'];

        if ($conditionTotal !== $actualQuantity) {
            throw new \InvalidArgumentException('Total baik + sedang + rusak + hilang harus sama dengan jumlah aktual.');
        }

        $this->kondisiAsetFromCounts(
            (int) $row['jumlah_baik'],
            (int) $row['jumlah_sedang'],
            (int) $row['jumlah_rusak'],
            (int) $row['jumlah_hilang'],
        );

        $result = $this->checkResult($systemQuantity, $actualQuantity, $conditionTotal);

        return [
            'asset_id' => $asset->id,
            'data' => [
                'jumlah_sistem' => $systemQuantity,
                'jumlah_aktual' => $actualQuantity,
                'jumlah_baik' => (int) $row['jumlah_baik'],
                'jumlah_sedang' => (int) $row['jumlah_sedang'],
                'jumlah_rusak' => (int) $row['jumlah_rusak'],
                'jumlah_hilang' => (int) $row['jumlah_hilang'],
                'hasil_pemeriksaan' => $result,
            ],
        ];
    }

    private function normalizeHeader(string $header): string
    {
        return Str::of($header)->lower()->trim()->replace([' ', '-'], '_')->replaceMatches('/[^a-z0-9_]/', '')->toString();
    }

    private function labUnits(Request $request)
    {
        return Unit::query()
            ->when($request->user()->isPengelola(), fn (Builder $query) => $query->whereKey($request->user()->unit_id))
            ->orderBy('name')
            ->get();
    }

    private function authorizeCheck(Request $request, InventoryCheck $check): void
    {
        if ($this->isUnitScopedUser($request) && (int) $check->unit_id !== (int) $request->user()->unit_id) {
            abort(403);
        }
    }

    private function isUnitScopedUser(Request $request): bool
    {
        return $request->user()->isUnitScoped();
    }

    private function findScannedAsset(string $code): ?Asset
    {
        return Asset::with(['unit', 'category', 'location', 'container', 'latestCompletedInventoryCheckItem'])
            ->where(function (Builder $query) use ($code): void {
                if (ctype_digit($code)) {
                    $query->orWhereKey((int) $code);
                }

                $query->orWhere('asset_code', $code)
                    ->orWhere('legacy_inventory_code', $code)
                    ->orWhere('qr_code', $code);
            })
            ->first();
    }

    private function checkResult(int $systemQuantity, int $actualQuantity, int $conditionTotal): string
    {
        return $actualQuantity === $systemQuantity && $conditionTotal === $actualQuantity
            ? 'sesuai'
            : 'tidak_sesuai';
    }

    private function progress(InventoryCheck $check, ?array $locationContext = null): array
    {
        $items = $check->items()->with('asset')->get();
        $assetsQuery = $this->assetQueryForContext($check, $locationContext);
        $assetIds = (clone $assetsQuery)->pluck('id');
        $items = $items->filter(fn (InventoryCheckItem $item): bool => $assetIds->contains($item->asset_id));
        $totalAssets = $assetIds->count();
        $checked = $items->whereNotNull('jumlah_aktual')->count();

        return [
            'total' => $totalAssets,
            'checked' => $checked,
            'unchecked' => max($totalAssets - $checked, 0),
            'sesuai' => $items->where('hasil_pemeriksaan', 'sesuai')->count(),
            'tidak_sesuai' => $items->where('hasil_pemeriksaan', 'tidak_sesuai')->count(),
            'percent' => $totalAssets > 0 ? round(($checked / $totalAssets) * 100, 1) : 0,
        ];
    }

    private function uncheckedItemCount(InventoryCheck $check): int
    {
        return $check->items()
            ->whereNull('jumlah_aktual')
            ->count();
    }

    private function syncLatestAssetCondition(InventoryCheck $check): void
    {
        $check->items()->with('asset')->whereNotNull('jumlah_aktual')->get()->each(function (InventoryCheckItem $item): void {
            if (! $item->asset) {
                return;
            }

            $kondisiAset = $this->kondisiAsetFromCounts(
                (int) $item->jumlah_baik,
                (int) $item->jumlah_sedang,
                (int) $item->jumlah_rusak,
                (int) $item->jumlah_hilang,
            );
            $item->asset?->update([
                'kondisi_aset' => $kondisiAset,
                'jumlah_baik' => (int) $item->jumlah_baik,
                'jumlah_sedang' => (int) $item->jumlah_sedang,
                'jumlah_rusak' => (int) $item->jumlah_rusak,
                'jumlah_hilang' => (int) $item->jumlah_hilang,
            ]);
        });
    }

    private function prepareCheckItems(InventoryCheck $check): void
    {
        Asset::where('unit_id', $check->unit_id)
            ->orderBy('name')
            ->get()
            ->each(fn (Asset $asset): InventoryCheckItem => $this->ensureCheckItem($check, $asset));
    }

    private function locationContext(Request $request, InventoryCheck $check): array
    {
        $rawLocationId = $request->query('location_id', $request->input('location_id'));

        if ($rawLocationId === 'none') {
            return [
                'id' => 'none',
                'location_id' => null,
                'label' => 'Tanpa Lokasi',
                'filtered' => true,
                'without_location' => true,
                'location' => null,
            ];
        }

        if ($rawLocationId !== null && $rawLocationId !== '') {
            $location = Location::whereKey((int) $rawLocationId)
                ->where('unit_id', $check->unit_id)
                ->firstOrFail();

            return [
                'id' => (string) $location->id,
                'location_id' => (int) $location->id,
                'label' => $location->name,
                'filtered' => true,
                'without_location' => false,
                'location' => $location,
            ];
        }

        return [
            'id' => null,
            'location_id' => null,
            'label' => 'Semua Lokasi',
            'filtered' => false,
            'without_location' => false,
            'location' => null,
        ];
    }

    private function assetQueryForContext(InventoryCheck $check, ?array $locationContext = null): Builder
    {
        return Asset::where('unit_id', $check->unit_id)
            ->when(($locationContext['filtered'] ?? false) && ($locationContext['without_location'] ?? false), fn (Builder $query) => $query->whereNull('location_id'))
            ->when(($locationContext['filtered'] ?? false) && ! ($locationContext['without_location'] ?? false), fn (Builder $query) => $query->where('location_id', $locationContext['location_id']));
    }

    private function locationProgress(InventoryCheck $check): array
    {
        $assets = Asset::with('location')
            ->where('unit_id', $check->unit_id)
            ->orderBy('location_id')
            ->orderBy('name')
            ->get();

        $checkedAssetIds = $check->items()
            ->whereNotNull('jumlah_aktual')
            ->pluck('asset_id')
            ->all();

        return $assets
            ->groupBy(fn (Asset $asset): string => $asset->location_id ? (string) $asset->location_id : 'none')
            ->map(function ($assets, string $key) use ($checkedAssetIds): array {
                $total = $assets->count();
                $checked = $assets->whereIn('id', $checkedAssetIds)->count();
                $location = $assets->first()?->location;

                return [
                    'id' => $key,
                    'label' => $location?->name ?? 'Tanpa Lokasi',
                    'total' => $total,
                    'checked' => $checked,
                    'unchecked' => max($total - $checked, 0),
                    'percent' => $total > 0 ? round(($checked / $total) * 100, 1) : 0,
                ];
            })
            ->sortBy('label')
            ->values()
            ->all();
    }

    private function assetMatchesLocationContext(Asset $asset, array $locationContext): bool
    {
        if (! ($locationContext['filtered'] ?? false)) {
            return true;
        }

        if ($locationContext['without_location'] ?? false) {
            return $asset->location_id === null;
        }

        return (int) $asset->location_id === (int) $locationContext['location_id'];
    }

    private function containerMatchesLocationContext(Container $container, array $locationContext): bool
    {
        if (! ($locationContext['filtered'] ?? false)) {
            return true;
        }

        if ($locationContext['without_location'] ?? false) {
            return $container->location_id === null;
        }

        return (int) $container->location_id === (int) $locationContext['location_id'];
    }

    private function scanFormRouteParams(InventoryCheck $check, array $locationContext): array
    {
        $params = ['inventoryCheck' => $check];

        if ($locationContext['filtered']) {
            $params['location_id'] = $locationContext['id'];
        }

        return $params;
    }

    private function ensureCheckItem(InventoryCheck $check, Asset $asset): InventoryCheckItem
    {
        if ((int) $asset->unit_id !== (int) $check->unit_id) {
            throw ValidationException::withMessages([
                'scan_code' => 'Aset bukan milik unit/laboratorium sesi pemeriksaan.',
            ]);
        }

        $quantity = max(1, (int) $asset->quantity);

        return InventoryCheckItem::firstOrCreate(
            [
                'inventory_check_id' => $check->id,
                'asset_id' => $asset->id,
            ],
            [
                'jumlah_sistem' => $quantity,
                'hasil_pemeriksaan' => 'pending',
            ],
        );
    }

    private function kondisiAsetFromCounts(int $baik, int $sedang, int $rusak, int $hilang): string
    {
        return match (true) {
            $hilang > 0 => 'hilang',
            $rusak > 0 => 'rusak',
            $sedang > 0 => 'sedang',
            default => 'baik',
        };
    }

    private function normalizeScanCode(string $code): string
    {
        $code = rawurldecode(preg_replace('/[\r\n\t]+/', '', trim($code)));
        $path = parse_url($code, PHP_URL_PATH);

        if (is_string($path) && $path !== '') {
            $path = trim($path, '/');
            $segments = array_values(array_filter(explode('/', $path), fn (string $segment): bool => $segment !== ''));
            $last = end($segments);

            if ($last !== false && (
                str_contains($path, 'qr/assets/')
                || str_contains($path, 'qr/storages/')
                || str_contains($path, 'scan/')
                || str_contains($path, 'assets/')
                || str_contains($path, 'containers/')
                || str_contains($path, 'storages/')
            )) {
                return rawurldecode((string) $last);
            }
        }

        return $code;
    }

    private function generateCode(): string
    {
        do {
            $code = 'INV-'.now()->format('Ymd').'-'.Str::upper(Str::random(5));
        } while (InventoryCheck::where('check_code', $code)->exists());

        return $code;
    }
}
