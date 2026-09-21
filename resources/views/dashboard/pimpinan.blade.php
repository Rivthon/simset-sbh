@extends('layouts.dashboard')

@section('title', 'Dashboard Kaprodi')

@section('content')
    @php
        $conditionLabels = \App\Models\Asset::KONDISI_ASET_LABELS;
        $summaryCards = [
            ['Total Aset', $stats['assets'], 'Semua unit', 'asset', 'bg-blue-50 text-blue-700'],
            ['Perlu Perhatian', $stats['attention_assets'], 'Sedang/rusak/hilang', 'report', 'bg-amber-50 text-amber-700'],
            ['Laporan Penggantian Aset', $stats['open_tool_replacements'], 'Belum selesai', 'report', 'bg-red-50 text-red-700'],
            ['Stock Opname Berjalan', $stats['inventory_checks_ongoing'], 'Sesi aktif', 'inventory', 'bg-emerald-50 text-emerald-700'],
        ];
    @endphp

    <section class="mb-4 rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-medium text-[#465fff]">Monitoring kaprodi</p>
                <h2 class="mt-1 text-xl font-bold text-slate-900">Ringkasan SIMASET SBH</h2>
                <p class="mt-1 max-w-3xl text-xs text-slate-500">Pantau aset lintas unit, laporan penggantian, dan progres stock opname laboratorium.</p>
            </div>
            <div class="flex flex-col gap-2 sm:flex-row">
                <a href="{{ route('reports.index') }}" class="inline-flex justify-center rounded-lg border border-[#465fff] px-4 py-2 text-sm font-semibold text-[#465fff] hover:bg-blue-50">Laporan Aset</a>
                <a href="{{ route('inventory-checks.index') }}" class="inline-flex justify-center rounded-lg bg-[#465fff] px-4 py-2 text-sm font-semibold text-white hover:bg-[#3641f5]">Stock Opname</a>
            </div>
        </div>
    </section>

    <section class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
        @foreach ($summaryCards as [$label, $value, $caption, $iconName, $tone])
            <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-slate-500">{{ $label }}</p>
                        <h3 class="mt-1 text-3xl font-bold leading-none text-slate-900">{{ number_format($value) }}</h3>
                        <p class="mt-2 text-xs text-slate-500">{{ $caption }}</p>
                    </div>
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg {{ $tone }}"><x-nav-icon :name="$iconName" /></span>
                </div>
            </div>
        @endforeach
    </section>

    <div class="mt-4 grid gap-4 xl:grid-cols-[minmax(0,1fr)_420px]">
        <section class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Rekap Aset Per Unit</h2>
                    <p class="mt-1 text-xs text-slate-500">Sebaran aset lintas laboratorium.</p>
                </div>
                <a href="{{ route('assets.index') }}" class="text-xs font-semibold text-[#465fff]">Lihat aset</a>
            </div>
            <div class="mt-3 grid gap-2 md:grid-cols-2">
                @forelse ($assetByUnit as $item)
                    @php
                        $percent = $stats['assets'] > 0 ? round(($item->total / $stats['assets']) * 100) : 0;
                    @endphp
                    <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
                        <div class="flex items-center justify-between gap-3">
                            <p class="min-w-0 truncate text-sm font-semibold text-slate-800">{{ $item->unit?->name ?? '-' }}</p>
                            <span class="text-sm font-bold text-slate-900">{{ number_format($item->total) }}</span>
                        </div>
                        <div class="mt-2 h-1.5 rounded-full bg-white">
                            <div class="h-1.5 rounded-full bg-[#465fff]" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="rounded-lg bg-slate-50 p-4 text-sm text-slate-500">Belum ada data aset.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <h2 class="text-base font-semibold text-slate-900">Kondisi Aset</h2>
            <div class="mt-3 grid gap-2">
                @foreach ($conditionLabels as $condition => $label)
                    @php
                        $total = $assetCondition[$condition] ?? 0;
                        $percent = $stats['asset_quantity'] > 0 ? round(($total / $stats['asset_quantity']) * 100) : 0;
                    @endphp
                    <div>
                        <div class="mb-1.5 flex items-center justify-between gap-3 text-xs">
                            <span class="font-semibold text-slate-700">{{ $label }}</span>
                            <span class="text-slate-500">{{ number_format($total) }} aset</span>
                        </div>
                        <div class="h-1.5 rounded-full bg-slate-100">
                            <div class="h-1.5 rounded-full bg-[#465fff]" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>

    <div class="mt-4 grid gap-4 xl:grid-cols-[minmax(0,1fr)_minmax(0,1fr)]">
        <section class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Laporan Penggantian Aset</h2>
                    <p class="mt-1 text-xs text-slate-500">Data terbaru dari unit laboratorium.</p>
                </div>
                <a href="{{ route('tool-replacements.index') }}" class="text-xs font-semibold text-[#465fff]">Lihat semua</a>
            </div>
            <div class="mt-3 space-y-2">
                @forelse ($latestToolReplacements as $replacement)
                    <a href="{{ route('tool-replacements.show', $replacement) }}" class="block rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm hover:bg-slate-100">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-slate-900">{{ $replacement->student_name }}</p>
                                <p class="mt-1 truncate text-xs text-slate-500">{{ $replacement->unit?->name ?? '-' }} | {{ $replacement->asset?->name ?? '-' }}</p>
                            </div>
                            @include('partials.badge', ['value' => $replacement->status])
                        </div>
                    </a>
                @empty
                    <p class="rounded-lg bg-slate-50 p-4 text-sm text-slate-500">Belum ada laporan penggantian aset.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Stock Opname Terbaru</h2>
                    <p class="mt-1 text-xs text-slate-500">Progress pemeriksaan inventaris per periode.</p>
                </div>
                <a href="{{ route('inventory-checks.index') }}" class="text-xs font-semibold text-[#465fff]">Lihat semua</a>
            </div>
            <div class="mt-3 space-y-2">
                @forelse ($latestInventoryChecks as $check)
                    @php
                        $total = $check->getAttribute('dashboard_total_assets');
                        $checked = $check->getAttribute('dashboard_checked_assets');
                        $unchecked = $check->getAttribute('dashboard_unchecked_assets');
                        $percent = $check->getAttribute('dashboard_progress_percent');
                    @endphp
                    <a href="{{ route('inventory-checks.show', $check) }}" class="block rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm hover:bg-slate-100">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-slate-900">{{ $check->unit?->name ?? '-' }}</p>
                                <p class="mt-1 truncate text-xs text-slate-500">{{ $check->periode ?: $check->semester.' '.$check->tahun_akademik }}</p>
                            </div>
                            @include('partials.badge', ['value' => $check->status])
                        </div>
                        <div class="mt-3 h-1.5 rounded-full bg-white">
                            <div class="h-1.5 rounded-full bg-[#465fff]" style="width: {{ $percent }}%"></div>
                        </div>
                        <p class="mt-2 text-xs text-slate-500">{{ number_format($checked) }} / {{ number_format($total) }} aset dicek, {{ number_format($unchecked) }} belum dicek</p>
                    </a>
                @empty
                    <p class="rounded-lg bg-slate-50 p-4 text-sm text-slate-500">Belum ada stock opname.</p>
                @endforelse
            </div>
        </section>
    </div>
@endsection
