<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UnitController extends Controller
{
    public function index(): View
    {
        return view('units.index', [
            'units' => Unit::orderByDesc('id')->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('units.form', ['unit' => new Unit()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['code'] = Unit::generateCode($data['name']);

        Unit::create($data);

        return redirect()->route('units.index')->with('success', 'Unit berhasil ditambahkan.');
    }

    public function edit(Unit $unit): View
    {
        return view('units.form', compact('unit'));
    }

    public function update(Request $request, Unit $unit): RedirectResponse
    {
        $unit->update($this->validated($request, $unit));

        return redirect()->route('units.index')->with('success', 'Unit berhasil diperbarui.');
    }

    public function destroy(Unit $unit): RedirectResponse
    {
        $unit->delete();

        return redirect()->route('units.index')->with('success', 'Unit berhasil dihapus.');
    }

    private function validated(Request $request, ?Unit $unit = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
        ]);
    }
}
