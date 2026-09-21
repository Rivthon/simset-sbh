<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(Request $request): View
    {
        return view('locations.index', [
            'locations' => Location::with('unit')
                ->visibleFor($request->user())
                ->orderByDesc('id')
                ->paginate(10),
        ]);
    }

    public function create(Request $request): View
    {
        abort_if($request->user()->isReadOnly(), 403, 'Anda tidak memiliki akses ke data ini.');

        return view('locations.form', [
            'location' => new Location(),
            'units' => $this->units($request),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_if($request->user()->isReadOnly(), 403, 'Anda tidak memiliki akses ke data ini.');

        $data = $this->validated($request);

        if ($this->isUnitScopedManager($request)) {
            $data['unit_id'] = $request->user()->unit_id;
        }

        Location::create($data);

        return redirect()->route('locations.index')->with('success', 'Lokasi berhasil ditambahkan.');
    }

    public function edit(Location $location): View
    {
        $this->authorizeLocation(request(), $location);

        return view('locations.form', [
            'location' => $location,
            'units' => $this->units(request()),
        ]);
    }

    public function update(Request $request, Location $location): RedirectResponse
    {
        $this->authorizeLocation($request, $location);

        $data = $this->validated($request, $location);

        if ($this->isUnitScopedManager($request)) {
            $data['unit_id'] = $request->user()->unit_id;
        }

        $location->update($data);

        return redirect()->route('locations.index')->with('success', 'Lokasi berhasil diperbarui.');
    }

    public function destroy(Location $location): RedirectResponse
    {
        $this->authorizeLocation(request(), $location);
        $location->delete();

        return redirect()->route('locations.index')->with('success', 'Lokasi berhasil dihapus.');
    }

    private function validated(Request $request, ?Location $location = null): array
    {
        return $request->validate([
            'unit_id' => [$this->isUnitScopedManager($request) ? 'nullable' : 'required', 'exists:units,id'],
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
        ]);
    }

    private function units(Request $request)
    {
        return Unit::where('status', 'active')
            ->when($this->isUnitScopedManager($request), fn (Builder $query) => $query->whereKey($request->user()->unit_id))
            ->orderBy('name')
            ->get();
    }

    private function authorizeLocation(Request $request, Location $location): void
    {
        if ($this->isUnitScopedManager($request) && $location->unit_id !== $request->user()->unit_id) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }
    }

    private function isUnitScopedManager(Request $request): bool
    {
        return $request->user()?->isUnitScopedManager() ?? false;
    }
}
