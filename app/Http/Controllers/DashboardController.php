<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Container;
use App\Models\InventoryCheck;
use App\Models\ToolReplacementRequest;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $assetQuery = Asset::query()
            ->visibleFor($request->user());
        $inventoryCheckQuery = InventoryCheck::query()
            ->visibleFor($request->user());
        $replacementQuery = ToolReplacementRequest::query()
            ->visibleFor($request->user());
        $stats = [
            'assets' => (clone $assetQuery)->count(),
            'asset_quantity' => (clone $assetQuery)->sum('quantity'),
            'units' => Unit::query()
                ->when($request->user()->isUnitScoped(), fn (Builder $query) => $query->whereKey($request->user()->unit_id))
                ->count(),
            'categories' => Category::query()
                ->visibleFor($request->user())
                ->count(),
            'users' => User::count(),
            'containers' => Container::query()
                ->visibleFor($request->user())
                ->count(),
            'inventory_checks' => (clone $inventoryCheckQuery)->count(),
            'inventory_checks_ongoing' => (clone $inventoryCheckQuery)->where('status', 'ongoing')->count(),
            'inventory_checks_completed' => (clone $inventoryCheckQuery)->where('status', 'completed')->count(),
            'tool_replacements' => (clone $replacementQuery)->count(),
            'tool_replacements_pending' => (clone $replacementQuery)->where('status', 'menunggu_verifikasi')->count(),
            'tool_replacements_waiting' => (clone $replacementQuery)->where('status', 'menunggu_penggantian')->count(),
            'tool_replacements_done' => (clone $replacementQuery)->where('status', 'sudah_diganti')->count(),
            'tool_replacements_rejected' => (clone $replacementQuery)->where('status', 'ditolak')->count(),
        ];

        $assetCondition = $this->assetConditionTotals((clone $assetQuery)
            ->with('latestCompletedInventoryCheckItem')
            ->get());

        $stats['good_assets'] = (int) ($assetCondition['baik'] ?? 0);
        $stats['moderate_assets'] = (int) ($assetCondition['sedang'] ?? 0);
        $stats['damaged_assets'] = (int) ($assetCondition['rusak'] ?? 0);
        $stats['lost_assets'] = (int) ($assetCondition['hilang'] ?? 0);
        $stats['attention_assets'] = $stats['moderate_assets'] + $stats['damaged_assets'] + $stats['lost_assets'];
        $stats['open_tool_replacements'] = $stats['tool_replacements_pending'] + $stats['tool_replacements_waiting'];
        $assetByCategory = (clone $assetQuery)->selectRaw('category_id, count(*) as total, COALESCE(SUM(quantity), 0) as quantity_total')
            ->groupBy('category_id')
            ->with('category')
            ->orderByDesc('total')
            ->limit(8)
            ->get();
        $assetByLocation = (clone $assetQuery)->selectRaw('location_id, count(*) as total, COALESCE(SUM(quantity), 0) as quantity_total')
            ->groupBy('location_id')
            ->with('location')
            ->orderByDesc('total')
            ->limit(8)
            ->get();
        $assetByUnit = (clone $assetQuery)->selectRaw('unit_id, count(*) as total')
            ->groupBy('unit_id')
            ->with('unit')
            ->get();
        $latestAssets = (clone $assetQuery)->with(['unit', 'category', 'latestCompletedInventoryCheckItem'])
            ->orderByDesc('id')
            ->limit(5)
            ->get();
        $latestInventoryChecks = (clone $inventoryCheckQuery)->with(['unit', 'creator', 'items'])
            ->orderByDesc('id')
            ->limit(5)
            ->get();
        $ongoingInventoryCheck = (clone $inventoryCheckQuery)->with(['unit', 'creator', 'items'])
            ->where('status', 'ongoing')
            ->orderByDesc('id')
            ->first();
        if ($ongoingInventoryCheck && ! $latestInventoryChecks->contains('id', $ongoingInventoryCheck->id)) {
            $latestInventoryChecks->prepend($ongoingInventoryCheck);
        }
        $latestToolReplacements = (clone $replacementQuery)->with(['asset', 'unit'])
            ->orderByDesc('id')
            ->limit(5)
            ->get();
        $setInventoryProgress = function ($check) use ($request): void {
            $check = $this->resolveInventoryCheck($check);
            if (! $check) {
                return;
            }
            $check->loadMissing('items');
            $totalAssets = Asset::query()
                ->visibleFor($request->user())
                ->where('unit_id', $check->unit_id)
                ->count();
            $checkedAssets = $check->items->whereNotNull('jumlah_aktual')->unique('asset_id')->count();
            $check->setAttribute('dashboard_total_assets', $totalAssets);
            $check->setAttribute('dashboard_checked_assets', $checkedAssets);
            $check->setAttribute('dashboard_unchecked_assets', max($totalAssets - $checkedAssets, 0));
            $check->setAttribute('dashboard_progress_percent', $totalAssets > 0 ? round(($checkedAssets / $totalAssets) * 100) : 0);
        };
        $latestInventoryChecks = $this->hydrateInventoryChecks($latestInventoryChecks);
        $latestInventoryChecks->each($setInventoryProgress);
        if ($ongoingInventoryCheck) {
            $setInventoryProgress($ongoingInventoryCheck);
        }
        return match ($request->user()->role) {
            'admin' => view('dashboard.admin', compact('stats', 'assetCondition', 'assetByUnit', 'latestAssets', 
            'latestInventoryChecks', 'latestToolReplacements', 'ongoingInventoryCheck')),
            'pengelola' => view('dashboard.pengelola', compact('stats', 'assetCondition', 'assetByCategory', 
            'assetByLocation', 'latestInventoryChecks', 'latestToolReplacements', 'ongoingInventoryCheck')),
            'pimpinan' => view('dashboard.pimpinan', compact('stats', 'assetCondition', 'assetByUnit', 
            'latestInventoryChecks', 'latestToolReplacements', 'ongoingInventoryCheck')),
            default => view('dashboard.index', compact('stats')),
        };
    }

    private function assetConditionTotals($assets)
    {
        $totals = collect(array_fill_keys(array_keys(Asset::KONDISI_ASET_LABELS), 0));
        foreach ($assets as $asset) {
            foreach ($asset->conditionCounts() as $condition => $total) {
                $totals[$condition] = (int) ($totals[$condition] ?? 0) + (int) $total;
            }
        }
        return $totals;
    }

    private function hydrateInventoryChecks(Collection $checks): Collection
    {
        if ($checks->every(fn ($check): bool => $check instanceof InventoryCheck)) {
            return $checks;
        }
        $ids = $checks
            ->pluck('id')
            ->filter()
            ->map(fn ($id): int => (int) $id)
            ->values();
        if ($ids->isEmpty()) {
            return collect();
        }
        $hydrated = InventoryCheck::with(['unit', 'creator', 'items'])
            ->whereKey($ids)
            ->get()
            ->keyBy('id');
        return $ids
            ->map(fn (int $id) => $hydrated->get($id))
            ->filter()
            ->values();
    }

    private function resolveInventoryCheck($check): ?InventoryCheck
    {
        if ($check instanceof InventoryCheck) {
            return $check;
        }
        $id = data_get($check, 'id');
        return $id ? InventoryCheck::with(['unit', 'creator', 'items'])->find($id) : null;
    }
}