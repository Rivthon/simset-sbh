<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Container;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssetScanController extends Controller
{
    public function index(): View
    {
        return view('assets.scan-entry');
    }

    public function process(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'scan_code' => ['required', 'string', 'max:255'],
        ], [], [
            'scan_code' => 'kode QR',
        ]);

        $code = $this->normalizeScanCode($data['scan_code']);
        $result = $this->findScannedData($code);

        if (! $result) {
            return back()->withInput()->with('error', 'Kode tidak ditemukan.');
        }

        [$type, $model] = $result;
        $unitId = $type === 'asset' ? $model->unit_id : ($model->unit_id ?: $model->location?->unit_id);

        if (! $request->user()->canAccessUnit($unitId)) {
            return back()->withInput()->with('error', 'Anda tidak memiliki akses ke data ini.');
        }

        return $type === 'asset'
            ? redirect()->route('assets.show', $model)
            : redirect()->route('containers.show', $model);
    }

    public function show(Request $request, string $qrCode): View|RedirectResponse
    {
        $result = $this->findScannedData($this->normalizeScanCode($qrCode));

        if (! $result) {
            return view('assets.scan-not-found', ['qrCode' => $qrCode]);
        }

        [$type, $model] = $result;

        if ($type === 'asset') {
            $asset = $model;
            $this->authorizeScannedUnit($request, $asset->unit_id);

            return $this->scanView($request, 'asset', $asset, null);
        }

        $container = $model;
        $this->authorizeScannedUnit($request, $container->unit_id ?: $container->location?->unit_id);

        return $this->scanView($request, 'container', null, $container);
    }

    public function assetQr(Request $request, string $qrCode): View|RedirectResponse
    {
        $asset = $this->findScannedAsset($this->normalizeScanCode($qrCode));

        if (! $asset) {
            return view('assets.scan-not-found', ['qrCode' => $qrCode]);
        }

        $this->authorizeScannedUnit($request, $asset->unit_id);

        return $this->scanView($request, 'asset', $asset, null);
    }

    public function storageQr(Request $request, string $qrCode): View|RedirectResponse
    {
        $container = $this->findScannedContainer($this->normalizeScanCode($qrCode));

        if (! $container) {
            return view('assets.scan-not-found', ['qrCode' => $qrCode]);
        }

        $this->authorizeScannedUnit($request, $container->unit_id ?: $container->location?->unit_id);

        return $this->scanView($request, 'container', null, $container);
    }

    private function scanView(Request $request, string $type, ?Asset $asset, ?Container $container): View
    {
        $containerAssets = null;

        if ($container) {
            $containerAssets = Asset::with(['category', 'latestCompletedInventoryCheckItem'])
                ->where('container_id', $container->id)
                ->when($request->filled('keyword'), function (Builder $query) use ($request): void {
                    $keyword = $request->string('keyword');
                    $query->where(function (Builder $query) use ($keyword): void {
                        $query->where('name', 'like', "%{$keyword}%")
                            ->orWhere('asset_code', 'like', "%{$keyword}%");
                    });
                })
                ->orderBy('name')
                ->paginate(8)
                ->withQueryString();
        }

        return view('assets.scan', [
            'type' => $type,
            'asset' => $asset,
            'container' => $container,
            'containerAssets' => $containerAssets,
            'canManage' => (bool) $request->user()?->hasRole(['admin', 'pengelola']),
            'canMonitor' => (bool) $request->user()?->hasRole(['admin', 'pengelola', 'pimpinan']),
        ]);
    }

    private function authorizeScannedUnit(Request $request, ?int $unitId): void
    {
        $user = $request->user();

        if ($user && ! $user->canAccessUnit($unitId)) {
            abort(403);
        }
    }

    private function findScannedData(string $code): ?array
    {
        $asset = $this->findScannedAsset($code);

        if ($asset) {
            return ['asset', $asset];
        }

        $container = $this->findScannedContainer($code);

        return $container ? ['container', $container] : null;
    }

    private function findScannedAsset(string $code): ?Asset
    {
        return Asset::with(['unit', 'category', 'location', 'container', 'latestCompletedInventoryCheckItem'])
            ->where(function (Builder $query) use ($code): void {
                if (ctype_digit($code)) {
                    $query->orWhereKey((int) $code);
                }

                $query->orWhere('qr_code', $code)
                    ->orWhere('asset_code', $code)
                    ->orWhere('legacy_inventory_code', $code);
            })
            ->first();
    }

    private function findScannedContainer(string $code): ?Container
    {
        return Container::with(['unit', 'location.unit', 'assets.category', 'assets.latestCompletedInventoryCheckItem'])
            ->where(function (Builder $query) use ($code): void {
                if (ctype_digit($code)) {
                    $query->orWhereKey((int) $code);
                }

                $query->orWhere('qr_code', $code)
                    ->orWhere('code', $code);
            })
            ->first();
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
}
