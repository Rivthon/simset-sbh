<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Container;
use App\Models\Location;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ContainerController extends Controller
{
    public function index(Request $request): View
    {
        return view('containers.index', [
            'containers' => Container::with(['unit', 'location.unit'])
                ->withCount('assets')
                ->visibleFor($request->user())
                ->orderByDesc('id')
                ->paginate(10),
        ]);
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()->hasRole(['admin', 'pengelola']), 403);

        return view('containers.form', [
            'container' => new Container(),
            'units' => $this->units($request),
            'locations' => $this->locations($request),
            'selectedUnitId' => old('unit_id', $request->user()->isPengelola() ? $request->user()->unit_id : null),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->hasRole(['admin', 'pengelola']), 403);

        $data = $this->validated($request);

        if ($request->user()->isUnitScoped()) {
            $data['unit_id'] = $request->user()->unit_id;
        }

        $this->authorizeUnitId($request, (int) $data['unit_id']);
        $this->authorizeLocationId($request, (int) $data['location_id']);
        $this->ensureLocationBelongsToUnit((int) $data['location_id'], (int) $data['unit_id']);

        $data['code'] = Container::generateCode(Location::findOrFail($data['location_id']));
        Container::create($data);

        return redirect()->route('containers.index')->with('success', 'Penyimpanan berhasil ditambahkan.');
    }

    public function show(Request $request, Container $container): View
    {
        $this->authorizeContainer($request, $container);

        return view('containers.show', [
            'container' => $container->load([
                'location.unit',
                'unit',
                'assets.category',
                'assets.latestCompletedInventoryCheckItem',
            ]),
            'assignableAssets' => $this->assignableAssets($request, $container),
            'qrData' => $container->qr_code ? route('assets.scan', $container->qr_code) : null,
        ]);
    }

    public function storeAsset(Request $request, Container $container): RedirectResponse
    {
        abort_unless($request->user()->hasRole(['admin', 'pengelola']), 403);
        $this->authorizeContainer($request, $container);

        $data = $request->validate([
            'asset_id' => ['required', 'exists:assets,id'],
        ], [], [
            'asset_id' => 'aset',
        ]);

        $asset = Asset::with(['unit', 'location'])->findOrFail($data['asset_id']);
        $containerUnitId = $container->unit_id ?: $container->location?->unit_id;

        abort_unless($request->user()->canAccessUnit($asset->unit_id), 403, 'Anda tidak memiliki akses ke aset ini.');
        abort_unless((int) $asset->unit_id === (int) $containerUnitId, 422, 'Aset harus berada pada unit/laboratorium yang sama dengan penyimpanan.');

        $asset->update([
            'container_id' => $container->id,
            'location_id' => $container->location_id,
        ]);

        return redirect()
            ->route('containers.show', $container)
            ->with('success', 'Aset berhasil dimasukkan ke penyimpanan.');
    }

    public function removeAsset(Request $request, Container $container, Asset $asset): RedirectResponse
    {
        abort_unless($request->user()->hasRole(['admin', 'pengelola']), 403);
        $this->authorizeContainer($request, $container);
        abort_unless($request->user()->canAccessUnit($asset->unit_id), 403, 'Anda tidak memiliki akses ke aset ini.');
        abort_unless((int) $asset->container_id === (int) $container->id, 422, 'Aset tidak berada dalam penyimpanan ini.');

        $asset->update(['container_id' => null]);

        return redirect()
            ->route('containers.show', $container)
            ->with('success', 'Aset berhasil dikeluarkan dari penyimpanan.');
    }

    public function edit(Container $container): View
    {
        abort_unless(request()->user()->hasRole(['admin', 'pengelola']), 403);
        $this->authorizeContainer(request(), $container);

        return view('containers.form', [
            'container' => $container,
            'units' => $this->units(request()),
            'locations' => $this->locations(request()),
            'selectedUnitId' => old('unit_id', $container->location?->unit_id),
        ]);
    }

    public function update(Request $request, Container $container): RedirectResponse
    {
        abort_unless($request->user()->hasRole(['admin', 'pengelola']), 403);
        $this->authorizeContainer($request, $container);
        $data = $this->validated($request, $container);

        if ($request->user()->isUnitScoped()) {
            $data['unit_id'] = $request->user()->unit_id;
        }

        $this->authorizeUnitId($request, (int) $data['unit_id']);
        $this->authorizeLocationId($request, (int) $data['location_id']);
        $this->ensureLocationBelongsToUnit((int) $data['location_id'], (int) $data['unit_id']);
        $container->update($data);

        return redirect()->route('containers.index')->with('success', 'Penyimpanan berhasil diperbarui.');
    }

    public function destroy(Container $container): RedirectResponse
    {
        abort_unless(request()->user()->hasRole(['admin', 'pengelola']), 403);
        $this->authorizeContainer(request(), $container);

        if ($container->assets()->exists()) {
            return back()->with('error', 'Penyimpanan tidak dapat dihapus karena masih memiliki aset.');
        }

        $container->delete();

        return redirect()->route('containers.index')->with('success', 'Penyimpanan berhasil dihapus.');
    }

    public function qr(Container $container): View
    {
        $this->authorizeContainer(request(), $container);
        abort_if(blank($container->qr_code), 404);

        return view('containers.qr', [
            'container' => $container->load(['location.unit', 'assets']),
            'qrData' => route('qr.storages.show', $container->qr_code),
        ]);
    }

    public function generateQr(Request $request, Container $container): RedirectResponse
    {
        abort_unless($request->user()->hasRole(['admin', 'pengelola']), 403);
        $this->authorizeContainer($request, $container);

        if (blank($container->qr_code)) {
            $container->forceFill(['qr_code' => Container::generateQrCode()])->save();
        }

        return redirect()->route('containers.show', $container)->with('success', 'QR Penyimpanan berhasil dibuat.');
    }

    private function validated(Request $request, ?Container $container = null): array
    {
        return $request->validate([
            'unit_id' => [$request->user()->isUnitScoped() ? 'nullable' : 'required', 'exists:units,id'],
            'location_id' => ['required', 'exists:locations,id'],
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
        ]);
    }

    private function locations(Request $request)
    {
        return Location::with('unit')
            ->where('status', 'active')
            ->when($request->user()->isUnitScoped(), fn (Builder $query) => $query->where('unit_id', $request->user()->unit_id))
            ->orderBy('name')
            ->get();
    }

    private function units(Request $request)
    {
        return Unit::where('status', 'active')
            ->when($request->user()->isUnitScoped(), fn (Builder $query) => $query->whereKey($request->user()->unit_id))
            ->orderBy('name')
            ->get();
    }

    private function assignableAssets(Request $request, Container $container)
    {
        $unitId = $container->unit_id ?: $container->location?->unit_id;

        return Asset::with(['category', 'location', 'container'])
            ->where('unit_id', $unitId)
            ->where(function (Builder $query) use ($container): void {
                $query->whereNull('container_id')
                    ->orWhere('container_id', '!=', $container->id);
            })
            ->when($request->user()->isUnitScoped(), fn (Builder $query) => $query->where('unit_id', $request->user()->unit_id))
            ->orderBy('name')
            ->get();
    }

    private function authorizeContainer(Request $request, Container $container): void
    {
        $unitId = $container->unit_id ?: $container->location?->unit_id;

        if ($request->user()->isUnitScoped() && $unitId !== $request->user()->unit_id) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }
    }

    private function authorizeLocationId(Request $request, int $locationId): void
    {
        if ($request->user()->isUnitScoped() && ! Location::whereKey($locationId)->where('unit_id', $request->user()->unit_id)->exists()) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }
    }

    private function authorizeUnitId(Request $request, int $unitId): void
    {
        abort_unless($request->user()->canAccessUnit($unitId), 403, 'Anda tidak memiliki akses ke data ini.');
    }

    private function ensureLocationBelongsToUnit(int $locationId, int $unitId): void
    {
        if (Location::whereKey($locationId)->where('unit_id', $unitId)->exists()) {
            return;
        }

        throw ValidationException::withMessages([
            'location_id' => 'Lokasi harus berada pada unit/laboratorium yang dipilih.',
        ]);
    }
}
