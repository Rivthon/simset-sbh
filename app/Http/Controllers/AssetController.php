<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetQuantityAddition;
use App\Models\Category;
use App\Models\Container;
use App\Models\Location;
use App\Models\Unit;
use App\Support\AssetClassifier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AssetController extends Controller
{
    public function index(Request $request): View
    {
        $assets = $this->filteredAssetQuery($request)
            ->with(['unit', 'category', 'location', 'container', 'latestCompletedInventoryCheckItem'])
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('assets.index', array_merge(compact('assets'), $this->filterData($request)));
    }

    public function export(Request $request): StreamedResponse
    {
        $assets = $this->filteredAssetQuery($request)
            ->with(['unit', 'category', 'location', 'container', 'latestCompletedInventoryCheckItem'])
            ->orderBy('name')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Aset');

        $headers = [
            'No',
            'Kode Aset Sistem',
            'Kode Aset Lama',
            'Nama Aset',
            'Unit/Laboratorium',
            'Kategori',
            'Lokasi',
            'Penyimpanan',
            'Jumlah',
            'Satuan',
            'Cara Identifikasi Aset',
            'Kondisi Aset',
            'Keterangan',
            'Tanggal Dibuat',
            'Tanggal Diperbarui',
        ];

        $sheet->fromArray($headers, null, 'A1');

        $rowNumber = 2;
        foreach ($assets as $index => $asset) {
            $sheet->fromArray([
                $index + 1,
                $asset->asset_code,
                $asset->legacy_inventory_code ?: '-',
                $asset->name,
                $asset->unit?->name ?? '-',
                $asset->category?->name ?? '-',
                $asset->location?->name ?? '-',
                $asset->container?->name ?? '-',
                $this->exportQuantity($asset, $request),
                $asset->satuan ?: 'unit',
                $asset->identificationLabel(),
                $asset->kondisi_aset_label,
                $asset->description ?: '-',
                $this->formatDateForExport($asset->getAttribute('created_at')),
                $this->formatDateForExport($asset->getAttribute('updated_at')),
            ], null, "A{$rowNumber}");
            $rowNumber++;
        }

        $sheet->getStyle('A1:O1')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A1:O1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('465FFF');
        $sheet->freezePane('A2');

        foreach (range('A', 'O') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $unitName = null;
        if ($request->filled('unit_id') && $this->canFilterUnit($request)) {
            $unitName = Unit::find($request->integer('unit_id'))?->name;
        } elseif ($request->user()->isUnitScoped()) {
            $unitName = $request->user()->unit?->name;
        }

        $filename = $unitName
            ? 'data-aset-'.$this->filenameSlug($unitName).'-'.now()->format('Ymd').'.xlsx'
            : 'data-aset-simaset-'.now()->format('Ymd').'.xlsx';

        return response()->streamDownload(function () use ($spreadsheet): void {
            (new Xlsx($spreadsheet))->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()->hasRole(['admin', 'pengelola']), 403);

        return view('assets.form', $this->formData($request, new Asset()));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->hasRole(['admin', 'pengelola']), 403);

        $data = $this->validated($request);

        if ($this->isUnitScopedManager($request)) {
            $data['unit_id'] = $request->user()->unit_id;
        }

        $this->authorizeCategoryId($request, (int) $data['category_id']);
        $category = Category::findOrFail($data['category_id']);
        $this->rejectConsumableAsset($request, $data);

        if (filled($data['container_id'] ?? null)) {
            $container = Container::with('location')->findOrFail($data['container_id']);
            if (! $request->user()->canAccessUnit($container->location->unit_id)) {
                abort(403);
            }
            $data['location_id'] = $container->location_id;
            $data['unit_id'] = $container->location->unit_id;
        }

        $this->authorizeLocationId($request, (int) $data['location_id'], (int) $data['unit_id']);

        $data['asset_code'] = Asset::generateAssetCode(Unit::findOrFail($data['unit_id']), $category);
        $data['created_by'] = $request->user()->id;

        $asset = Asset::create($data);

        if ($request->filled('redirect_container_id')) {
            $redirectContainer = Container::find($request->integer('redirect_container_id'));

            if ($redirectContainer && (int) $redirectContainer->id === (int) $asset->container_id) {
                return redirect()
                    ->route('containers.show', $redirectContainer)
                    ->with('success', 'Aset berhasil ditambahkan ke penyimpanan.');
            }
        }

        return redirect()->route('assets.index')->with('success', 'Aset berhasil ditambahkan.');
    }

    public function show(Request $request, Asset $asset): View
    {
        $this->authorizeAsset($request, $asset);

        return view('assets.show', [
            'asset' => $asset->load(['unit', 'category', 'location', 'container', 'creator', 
            'inventoryCheckItems.inventoryCheck', 'latestCompletedInventoryCheckItem', 'quantityAdditions.creator']),
        ]);
    }

    public function edit(Request $request, Asset $asset): View
    {
        abort_unless($request->user()->hasRole(['admin', 'pengelola']), 403);
        $this->authorizeAsset($request, $asset);

        return view('assets.form', $this->formData($request, $asset));
    }

    public function update(Request $request, Asset $asset): RedirectResponse
    {
        abort_unless($request->user()->hasRole(['admin', 'pengelola']), 403);
        $this->authorizeAsset($request, $asset);

        $data = $this->validated($request, $asset);

        if ($this->isUnitScopedManager($request)) {
            $data['unit_id'] = $request->user()->unit_id;
        }

        $this->authorizeCategoryId($request, (int) $data['category_id']);
        $category = Category::findOrFail($data['category_id']);
        $this->rejectConsumableAsset($request, $data);

        if (filled($data['container_id'] ?? null)) {
            $container = Container::with('location')->findOrFail($data['container_id']);
            if (! $request->user()->canAccessUnit($container->location->unit_id)) {
                abort(403);
            }
            $data['location_id'] = $container->location_id;
            $data['unit_id'] = $container->location->unit_id;
        }

        $this->authorizeLocationId($request, (int) $data['location_id'], (int) $data['unit_id']);

        $asset->update($data);

        return redirect()->route('assets.index')->with('success', 'Aset berhasil diperbarui.');
    }

    public function addQuantity(Request $request, Asset $asset): RedirectResponse
    {
        abort_unless($request->user()->hasRole(['admin', 'pengelola']), 403);
        $this->authorizeAsset($request, $asset);

        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
            'jumlah_baik' => ['required', 'integer', 'min:0'],
            'jumlah_sedang' => ['required', 'integer', 'min:0'],
            'jumlah_rusak' => ['required', 'integer', 'min:0'],
            'jumlah_hilang' => ['required', 'integer', 'min:0'],
            'addition_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $conditionTotal = collect(['jumlah_baik', 'jumlah_sedang', 'jumlah_rusak', 'jumlah_hilang'])
            ->sum(fn (string $key): int => (int) $data[$key]);

        if ($conditionTotal !== (int) $data['quantity']) {
            throw ValidationException::withMessages([
                'quantity' => 'Total kondisi tambahan harus sama dengan kuantitas yang ditambahkan.',
            ]);
        }

        DB::transaction(function () use ($asset, $data, $request): void {
            $lockedAsset = Asset::query()->lockForUpdate()->findOrFail($asset->id);
            $counts = $lockedAsset->conditionCounts();

            foreach (array_keys(Asset::KONDISI_ASET_LABELS) as $condition) {
                $counts[$condition] += (int) $data['jumlah_'.$condition];
            }

            $lockedAsset->update([
                'quantity' => (int) $lockedAsset->quantity + (int) $data['quantity'],
                'jumlah_baik' => $counts['baik'],
                'jumlah_sedang' => $counts['sedang'],
                'jumlah_rusak' => $counts['rusak'],
                'jumlah_hilang' => $counts['hilang'],
                'kondisi_aset' => Asset::worstCondition($counts),
            ]);

            AssetQuantityAddition::create(array_merge($data, [
                'asset_id' => $lockedAsset->id,
                'created_by' => $request->user()->id,
            ]));
        });

        return back()->with('success', 'Kuantitas aset berhasil ditambahkan.');
    }

    public function destroy(Request $request, Asset $asset): RedirectResponse
    {
        abort_unless($request->user()->hasRole(['admin', 'pengelola']), 403);
        $this->authorizeAssetDeletion($request, $asset);

        if (! $request->user()->isAdmin() && $asset->hasTransactionHistory()) {
            return back()->with('error', 'Data aset tidak dapat dihapus karena sudah memiliki 
            riwayat transaksi.');
        }

        $asset->delete();

        return redirect()->route('assets.index')->with('success', 'Data aset berhasil dihapus permanen.');
    }

    private function formData(Request $request, Asset $asset): array
    {
        $unitScoped = $this->isUnitScopedManager($request);
        $sourceContainer = null;

        if (! $asset->exists && $request->filled('container_id')) {
            $sourceContainer = Container::with('location')->findOrFail($request->integer('container_id'));

            abort_unless($request->user()->canAccessUnit($sourceContainer->unit_id ?: 
            $sourceContainer->location?->unit_id), 403);

            $asset->unit_id = $sourceContainer->unit_id ?: $sourceContainer->location?->unit_id;
            $asset->location_id = $sourceContainer->location_id;
            $asset->container_id = $sourceContainer->id;
        }

        return [
            'asset' => $asset,
            'conditionLocked' => $asset->exists && $asset->completedInventoryCheckItems()->exists(),
            'sourceContainer' => $sourceContainer,
            'units' => Unit::where('status', 'active')
                ->when($unitScoped, fn (Builder $query) => $query->whereKey($request->user()->unit_id))
                ->orderBy('name')
                ->get(),
            'categories' => $this->categoryOptions($request),
            'locations' => Location::with('unit')
                ->where('status', 'active')
                ->when($unitScoped, fn (Builder $query) => $query->where('unit_id', $request->user()->unit_id))
                ->orderBy('name')
                ->get(),
            'containers' => Container::with(['unit', 'location'])
                ->where('status', 'active')
                ->when($unitScoped, fn (Builder $query) => $query->where('unit_id', $request->user()->unit_id))
                ->orderBy('name')
                ->get(),
        ];
    }

    private function validated(Request $request, ?Asset $asset = null): array
    {
        $satuanOptions = array_keys(Asset::SATUAN_OPTIONS);

        $data = $request->validate(
            [
                'unit_id' => ['required', 'exists:units,id'],
                'category_id' => ['required', 'exists:categories,id'],
                'location_id' => ['required', 'exists:locations,id'],
                'container_id' => ['nullable', 'exists:containers,id'],
                'identification_type' => ['required', 'in:individual,group'],
                'quantity' => ['required', 'integer', 'min:1'],
                'satuan_choice' => ['required', Rule::in($satuanOptions)],
                'satuan_custom' => ['nullable', 'required_if:satuan_choice,lainnya', 'string', 'max:50'],
                'asset_code' => ['nullable', 'string', 'max:100', Rule::unique('assets')->ignore($asset)],
                'legacy_inventory_code' => ['nullable', 'string', 'max:100'],
                'name' => ['required', 'string', 'max:255'],
                'jumlah_baik' => ['required', 'integer', 'min:0'],
                'jumlah_sedang' => ['required', 'integer', 'min:0'],
                'jumlah_rusak' => ['required', 'integer', 'min:0'],
                'jumlah_hilang' => ['required', 'integer', 'min:0'],
                'description' => ['nullable', 'string'],
            ],
            [
                'satuan_choice.required' => 'Satuan wajib diisi.',
                'satuan_choice.in' => 'Pilihan satuan tidak valid.',
                'satuan_custom.required_if' => 'Satuan manual wajib diisi saat memilih lainnya.',
            ]
        );

        $data['satuan'] = $data['satuan_choice'] === 'lainnya'
            ? trim((string) $data['satuan_custom'])
            : $data['satuan_choice'];

        unset($data['satuan_choice'], $data['satuan_custom']);

        $conditionLocked = $asset?->exists && $asset->completedInventoryCheckItems()->exists();

        if ($conditionLocked) {
            $current = $asset->conditionCounts();
            $changed = (int) $data['quantity'] !== (int) $asset->quantity;

            foreach (array_keys(Asset::KONDISI_ASET_LABELS) as $condition) {
                $changed = $changed || (int) $data['jumlah_'.$condition] !== (int) $current[$condition];
            }

            if ($changed) {
                throw ValidationException::withMessages([
                    'quantity' => 'Jumlah dan kondisi aset yang sudah di-stock-opname tidak dapat diedit langsung. Gunakan Tambah Kuantitas pada detail aset.',
                ]);
            }
        } else {
            $conditionTotal = collect(['jumlah_baik', 'jumlah_sedang', 'jumlah_rusak', 'jumlah_hilang'])
                ->sum(fn (string $key): int => (int) $data[$key]);

            if ($conditionTotal !== (int) $data['quantity']) {
                throw ValidationException::withMessages([
                    'quantity' => 'Total Baik, Sedang, Rusak, dan Hilang harus sama dengan jumlah aset.',
                ]);
            }
        }

        $data['kondisi_aset'] = Asset::worstCondition([
            'baik' => $data['jumlah_baik'],
            'sedang' => $data['jumlah_sedang'],
            'rusak' => $data['jumlah_rusak'],
            'hilang' => $data['jumlah_hilang'],
        ]);

        return $data;
    }

    private function rejectConsumableAsset(Request $request, array $data): void
    {
        if (! AssetClassifier::isConsumable([
            $data['name'] ?? '',
            $data['description'] ?? '',
        ])) {
            return;
        }

        throw ValidationException::withMessages([
            'name' => 'BHP/Bahan Habis Pakai tidak boleh dimasukkan ke data aset utama.',
        ]);
    }

    private function assetQuery(Request $request): Builder
    {
        return Asset::query()->visibleFor($request->user());
    }

    private function filteredAssetQuery(Request $request): Builder
    {
        return $this->assetQuery($request)
            ->when($request->filled('keyword'), function (Builder $query) use ($request): void {
                $keyword = $request->string('keyword');
                $query->where(function (Builder $query) use ($keyword): void {
                    $query->where('name', 'like', "%{$keyword}%")
                        ->orWhere('asset_code', 'like', "%{$keyword}%")
                        ->orWhere('legacy_inventory_code', 'like', "%{$keyword}%")
                        ->orWhere('qr_code', 'like', "%{$keyword}%");
                });
            })
            ->when($request->filled('unit_id') && $this->canFilterUnit($request), fn (Builder $query) 
            => $query->where('unit_id', $request->integer('unit_id')))
            ->when($request->filled('category_id'), fn (Builder $query) 
            => $query->where('category_id', $request->integer('category_id')))
            ->when($request->filled('location_id'), fn (Builder $query) 
            => $query->where('location_id', $request->integer('location_id')))
            ->when($request->filled('container_id'), fn (Builder $query) 
            => $query->where('container_id', $request->integer('container_id')))
            ->when($request->filled('kondisi_aset'), fn (Builder $query) 
            => $query->whereConditionHas((string) $request->string('kondisi_aset')))
            ->when(! $request->filled('kondisi_aset') && $request->input('report_scope') 
            === 'attention', fn (Builder $query) => $query->whereAnyConditionHas(['sedang', 'rusak', 'hilang']))
            ->when($request->filled('identification_type'), fn (Builder $query) 
            => $query->where('identification_type', $request->string('identification_type')));
    }

    private function authorizeAsset(Request $request, Asset $asset): void
    {
        if (! $request->user()->canAccessUnit($asset->unit_id)) {
            abort(403);
        }
    }

    private function authorizeAssetDeletion(Request $request, Asset $asset): void
    {
        if ($request->user()->isAdmin()) {
            return;
        }

        if ($request->user()->canDeleteAssetInUnit($asset->unit_id)) {
            return;
        }

        abort(403);
    }

    private function isUnitScopedManager(Request $request): bool
    {
        return $request->user()->isUnitScopedManager();
    }

    private function isUnitScopedUser(Request $request): bool
    {
        return $request->user()->isUnitScoped();
    }

    private function canFilterUnit(Request $request): bool
    {
        return $request->user()->canFilterUnits();
    }

    private function filenameSlug(string $value): string
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $value), '-'));

        return $slug !== '' ? $slug : 'unit';
    }

    private function exportQuantity(Asset $asset, Request $request): int
    {
        $counts = $asset->conditionCounts();
        $condition = Asset::normalizeKondisiAset((string) $request->input('kondisi_aset'));

        if ($condition !== null) {
            return (int) ($counts[$condition] ?? 0);
        }

        if ($request->input('report_scope') === 'attention') {
            return (int) ($counts['sedang'] ?? 0)
                + (int) ($counts['rusak'] ?? 0)
                + (int) ($counts['hilang'] ?? 0);
        }

        return (int) ($asset->quantity ?? 1);
    }

    private function formatDateForExport(mixed $value): string
    {
        if (blank($value)) {
            return '-';
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('d/m/Y H:i');
        }

        return Carbon::parse($value)->format('d/m/Y H:i');
    }

    private function filterData(Request $request): array
    {
        $unitScoped = $this->isUnitScopedUser($request);

        return [
            'units' => Unit::where('status', 'active')
                ->when($unitScoped, fn (Builder $query) => $query->whereKey($request->user()->unit_id))
                ->orderBy('name')
                ->get(),
            'categories' => $this->categoryOptions($request),
            'locations' => Location::with('unit')
                ->where('status', 'active')
                ->when($unitScoped, fn (Builder $query) => $query->where('unit_id', $request->user()->unit_id))
                ->orderBy('name')
                ->get(),
            'containers' => Container::with(['unit', 'location'])
                ->where('status', 'active')
                ->when($unitScoped, fn (Builder $query) => $query->where('unit_id', $request->user()->unit_id))
                ->orderBy('name')
                ->get(),
        ];
    }

    private function categoryOptions(Request $request)
    {
        return Category::with('unit')
            ->where('status', 'active')
            ->when($this->isUnitScopedUser($request), function (Builder $query) use ($request): void {
                $query->where(function (Builder $query) use ($request): void {
                    $query->whereNull('unit_id')
                        ->orWhere('unit_id', $request->user()->unit_id);
                });
            })
            ->orderBy('name')
            ->get();
    }

    private function authorizeCategoryId(Request $request, int $categoryId): void
    {
        if (! $this->isUnitScopedUser($request)) {
            return;
        }

        $allowed = Category::whereKey($categoryId)
            ->where(function (Builder $query) use ($request): void {
                $query->whereNull('unit_id')
                    ->orWhere('unit_id', $request->user()->unit_id);
            })
            ->exists();

        abort_unless($allowed, 403);
    }

    private function authorizeLocationId(Request $request, int $locationId, int $unitId): void
    {
        $location = Location::findOrFail($locationId);

        abort_unless($location->unit_id === $unitId, 403);
        abort_unless($request->user()->canAccessUnit($location->unit_id), 403);
    }
}
