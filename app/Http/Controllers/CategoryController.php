<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        return view('categories.index', [
            'categories' => Category::query()
                ->visibleFor($request->user())
                ->with('unit')
                ->orderByDesc('id')
                ->paginate(10),
        ]);
    }

    public function create(Request $request): View
    {
        abort_if($request->user()->isReadOnly(), 403, 'Anda tidak memiliki akses ke data ini.');

        return view('categories.form', [
            'category' => new Category(),
            'units' => $this->unitOptions($request),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_if($request->user()->isReadOnly(), 403, 'Anda tidak memiliki akses ke data ini.');

        $data = $this->validated($request);

        if ($this->isUnitScopedManager($request)) {
            $data['unit_id'] = $request->user()->unit_id;
        }

        Category::create($data);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Request $request, Category $category): View
    {
        $this->authorizeCategory($request, $category);

        return view('categories.form', [
            'category' => $category,
            'units' => $this->unitOptions($request),
        ]);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $this->authorizeCategory($request, $category);

        $data = $this->validated($request, $category);

        if ($this->isUnitScopedManager($request)) {
            $data['unit_id'] = $request->user()->unit_id;
        }

        $category->update($data);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Request $request, Category $category): RedirectResponse
    {
        $this->authorizeCategory($request, $category);
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus.');
    }

    private function validated(Request $request, ?Category $category = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'unit_id' => ['nullable', 'exists:units,id'],
            'status' => ['required', 'in:active,inactive'],
        ]);
    }

    private function unitOptions(Request $request)
    {
        return Unit::where('status', 'active')
            ->when($this->isUnitScopedManager($request), fn (Builder $query) => $query->whereKey($request->user()->unit_id))
            ->orderBy('name')
            ->get();
    }

    private function authorizeCategory(Request $request, Category $category): void
    {
        if ($request->user()->isAdmin()) {
            return;
        }

        if ($this->isUnitScopedManager($request) && $category->unit_id === $request->user()->unit_id) {
            return;
        }

        abort(403, 'Anda tidak memiliki akses ke data ini.');
    }

    private function isUnitScopedManager(Request $request): bool
    {
        return $request->user()->isUnitScopedManager();
    }
}
