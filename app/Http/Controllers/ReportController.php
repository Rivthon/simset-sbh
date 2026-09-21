<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Container;
use App\Models\Location;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $assetQuery = $this->filteredAssetQuery($request);
        $reportScope = $request->input('report_scope', 'attention') === 'all' ? 'all' : 'attention';
        $reportAssets = (clone $assetQuery)
            ->with(['unit', 'category', 'location', 'container', 'latestCompletedInventoryCheckItem'])
            ->orderBy('unit_id')
            ->orderBy('location_id')
            ->orderBy('name')
            ->get();
        $conditionTotals = $this->conditionTotals($reportAssets);
        $reportQuantity = $reportAssets->sum(fn (Asset $asset): int => $this->reportQuantity($asset, $request, $reportScope));

        return view('reports.index', [
            'totalAssets' => (clone $assetQuery)->count(),
            'totalQuantity' => $reportQuantity,
            'byCondition' => $conditionTotals['data'],
            'byConditionQuantity' => $conditionTotals['quantity'],
            'byUnit' => $this->groupedTotals($reportAssets, 'unit_id', 'unit', $request, $reportScope),
            'byCategory' => $this->groupedTotals($reportAssets, 'category_id', 'category', $request, $reportScope),
            'byLocation' => $this->groupedTotals($reportAssets, 'location_id', 'location', $request, $reportScope),
            'byContainer' => $this->groupedTotals($reportAssets, 'container_id', 'container', $request, $reportScope),
            'reportAssets' => $reportAssets,
            'units' => $this->units($request),
            'categories' => $this->categories($request),
            'locations' => $this->locations($request),
            'containers' => $this->containers($request),
            'reportScope' => $reportScope,
        ]);
    }

    private function filteredAssetQuery(Request $request): Builder
    {
        return Asset::query()
            ->visibleFor($request->user())
            ->when($request->filled('keyword'), function (Builder $query) use ($request): void {
                $keyword = $request->string('keyword');
                $query->where(function (Builder $query) use ($keyword): void {
                    $query->where('name', 'like', "%{$keyword}%")
                        ->orWhere('asset_code', 'like', "%{$keyword}%")
                        ->orWhere('legacy_inventory_code', 'like', "%{$keyword}%")
                        ->orWhere('qr_code', 'like', "%{$keyword}%");
                });
            })
            ->when($request->filled('unit_id') && $this->canFilterUnit($request), fn (Builder $query) => $query->where('unit_id', $request->integer('unit_id')))
            ->when($request->filled('category_id'), fn (Builder $query) => $query->where('category_id', $request->integer('category_id')))
            ->when($request->filled('location_id'), fn (Builder $query) => $query->where('location_id', $request->integer('location_id')))
            ->when($request->filled('container_id'), fn (Builder $query) => $query->where('container_id', $request->integer('container_id')))
            ->when($request->filled('kondisi_aset'), fn (Builder $query) => $query->whereConditionHas((string) $request->string('kondisi_aset')))
            ->when(! $request->filled('kondisi_aset') && $request->input('report_scope', 'attention') !== 'all', fn (Builder $query) => $query->whereAnyConditionHas(['sedang', 'rusak', 'hilang']));
    }

    private function conditionTotals($assets): array
    {
        $data = collect(array_fill_keys(array_keys(Asset::KONDISI_ASET_LABELS), 0));
        $quantity = collect(array_fill_keys(array_keys(Asset::KONDISI_ASET_LABELS), 0));

        foreach ($assets as $asset) {
            foreach ($asset->conditionCounts() as $condition => $total) {
                if ($total <= 0) {
                    continue;
                }

                $data[$condition] = (int) ($data[$condition] ?? 0) + 1;
                $quantity[$condition] = (int) ($quantity[$condition] ?? 0) + (int) $total;
            }
        }

        return compact('data', 'quantity');
    }

    private function groupedTotals($assets, string $key, string $relation, Request $request, string $reportScope)
    {
        return $assets
            ->groupBy(fn (Asset $asset) => $asset->{$key} ?? 'none')
            ->map(function ($group) use ($relation, $request, $reportScope) {
                return (object) [
                    $relation => $group->first()?->{$relation},
                    'total' => $group->count(),
                    'quantity_total' => $group->sum(fn (Asset $asset): int => $this->reportQuantity($asset, $request, $reportScope)),
                ];
            })
            ->sortByDesc('total')
            ->values();
    }

    private function reportQuantity(Asset $asset, Request $request, string $reportScope): int
    {
        $counts = $asset->conditionCounts();
        $condition = Asset::normalizeKondisiAset((string) $request->input('kondisi_aset'));

        if ($condition !== null) {
            return (int) ($counts[$condition] ?? 0);
        }

        if ($reportScope !== 'all') {
            return (int) ($counts['sedang'] ?? 0)
                + (int) ($counts['rusak'] ?? 0)
                + (int) ($counts['hilang'] ?? 0);
        }

        return (int) ($asset->quantity ?? 1);
    }

    private function units(Request $request)
    {
        return Unit::where('status', 'active')
            ->when($request->user()->isUnitScoped(), fn (Builder $query) => $query->whereKey($request->user()->unit_id))
            ->orderBy('name')
            ->get();
    }

    private function categories(Request $request)
    {
        return Category::where('status', 'active')
            ->when($request->user()->isUnitScoped(), function (Builder $query) use ($request): void {
                $query->where(function (Builder $query) use ($request): void {
                    $query->whereNull('unit_id')
                        ->orWhere('unit_id', $request->user()->unit_id);
                });
            })
            ->orderBy('name')
            ->get();
    }

    private function locations(Request $request)
    {
        return Location::where('status', 'active')
            ->when($request->user()->isUnitScoped(), fn (Builder $query) => $query->where('unit_id', $request->user()->unit_id))
            ->orderBy('name')
            ->get();
    }

    private function containers(Request $request)
    {
        return Container::with(['unit', 'location'])
            ->where('status', 'active')
            ->visibleFor($request->user())
            ->orderBy('name')
            ->get();
    }

    private function canFilterUnit(Request $request): bool
    {
        return $request->user()->canFilterUnits();
    }
}
