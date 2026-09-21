@extends('layouts.dashboard')

@section('title', 'Laporan Aset')

@section('content')
    @php
        $conditionLabels = \App\Models\Asset::KONDISI_ASET_LABELS;
        $conditionTones = [
            'baik' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
            'sedang' => 'border-yellow-200 bg-yellow-50 text-yellow-700',
            'rusak' => 'border-amber-200 bg-amber-50 text-amber-700',
            'hilang' => 'border-red-200 bg-red-50 text-red-700',
        ];
        $selectedUnit = auth()->user()->isUnitScoped()
            ? auth()->user()->unit?->name
            : $units->firstWhere('id', (int) request('unit_id'))?->name;
        $selectedCategory = $categories->firstWhere('id', (int) request('category_id'))?->name;
        $selectedLocation = $locations->firstWhere('id', (int) request('location_id'))?->name;
        $selectedContainer = $containers->firstWhere('id', (int) request('container_id'))?->name;
        $selectedCondition = request('kondisi_aset') ? ($conditionLabels[(string) request('kondisi_aset')] ?? request('kondisi_aset')) : null;
        $reportScope = $reportScope ?? (request('report_scope', 'attention') === 'all' ? 'all' : 'attention');
        $reportFocusLabel = $reportScope === 'all' ? 'Semua Aset' : 'Aset Perlu Perhatian';
        $displayConditionLabels = $selectedCondition
            ? collect($conditionLabels)->only((string) request('kondisi_aset'))->all()
            : ($reportScope === 'all' ? $conditionLabels : collect($conditionLabels)->except('baik')->all());
        $reportQuery = request()->query();
        $reportQuery['report_scope'] = $reportScope;
        $quantityColumnLabel = $reportScope === 'all' && ! request('kondisi_aset') ? 'Jumlah Aset' : 'Jumlah Prioritas';
        $reportQuantityForAsset = function ($asset) use ($reportScope) {
            $counts = $asset->conditionCounts();
            $condition = \App\Models\Asset::normalizeKondisiAset((string) request('kondisi_aset'));

            if ($condition) {
                return (int) ($counts[$condition] ?? 0);
            }

            if ($reportScope !== 'all') {
                return (int) ($counts['sedang'] ?? 0) + (int) ($counts['rusak'] ?? 0) + (int) ($counts['hilang'] ?? 0);
            }

            return (int) ($asset->quantity ?? 1);
        };
        $activeFilters = collect([
            ['Fokus', $reportFocusLabel],
            ['Kata Kunci', request('keyword')],
            ['Unit', $selectedUnit && ! auth()->user()->isUnitScoped() ? $selectedUnit : null],
            ['Kategori', $selectedCategory],
            ['Lokasi', $selectedLocation],
            ['Penyimpanan', $selectedContainer],
            ['Kondisi', $selectedCondition],
        ])->filter(fn ($item) => filled($item[1]));
        $attentionData = ($byCondition['sedang'] ?? 0) + ($byCondition['rusak'] ?? 0) + ($byCondition['hilang'] ?? 0);
        $attentionQuantity = ($byConditionQuantity['sedang'] ?? 0) + ($byConditionQuantity['rusak'] ?? 0) + ($byConditionQuantity['hilang'] ?? 0);
        $reportMeta = [
            ['Jenis Laporan', $reportScope === 'all' ? 'Laporan Aset' : 'Laporan Aset Perlu Perhatian'],
            ['Unit/Laboratorium', $selectedUnit ?: 'Semua Unit'],
            ['Tanggal Cetak', now()->format('d/m/Y H:i')],
            ['Dicetak Oleh', auth()->user()->name],
        ];
        $filterMeta = [
            ['Fokus Laporan', $reportFocusLabel],
            ['Unit/Laboratorium', $selectedUnit ?: 'Semua Unit'],
            ['Kategori', $selectedCategory ?: 'Semua Kategori'],
            ['Lokasi', $selectedLocation ?: 'Semua Lokasi'],
            ['Penyimpanan', $selectedContainer ?: 'Semua Penyimpanan'],
            ['Kondisi Aset', $selectedCondition ?: ($reportScope === 'all' ? 'Semua Kondisi' : 'Sedang, Rusak, Hilang')],
        ];
        $summaryCards = [
            [$reportScope === 'all' ? 'Total Data Aset' : 'Total Data Prioritas', $totalAssets, 'Jumlah jenis aset dalam laporan', 'border-blue-200 bg-blue-50 text-blue-700'],
            [$reportScope === 'all' ? 'Total Jumlah Aset' : 'Total Jumlah Prioritas', $totalQuantity, 'Total kuantitas aset dalam laporan', 'border-indigo-200 bg-indigo-50 text-indigo-700'],
            ['Perlu Perhatian', $attentionData, number_format($attentionQuantity).' aset sedang/rusak/hilang', 'border-red-200 bg-red-50 text-red-700'],
        ];
        $summaryRows = collect([
            [$reportScope === 'all' ? 'Total Data Aset' : 'Total Data Prioritas', $totalAssets, 'Jumlah jenis aset'],
            [$reportScope === 'all' ? 'Total Jumlah Aset' : 'Total Jumlah Prioritas', $totalQuantity, 'Total kuantitas aset'],
            ['Perlu Perhatian', $attentionData, number_format($attentionQuantity).' aset sedang/rusak/hilang'],
        ])->merge(collect($displayConditionLabels)->map(fn ($label, $value) => [$label, $byCondition[$value] ?? 0, number_format($byConditionQuantity[$value] ?? 0).' total aset']))->all();
        $recapSections = [
            ['Rekap Aset per Unit', $byUnit, 'unit'],
            ['Rekap Aset per Kategori', $byCategory, 'category'],
            ['Rekap Aset per Lokasi', $byLocation, 'location'],
            ['Rekap Aset per Penyimpanan', $byContainer, 'container'],
        ];
    @endphp

    <style>
        #report-pdf-area {
            display: none;
        }

        @media print {
            @page {
                margin: 12mm;
                size: A4;
            }

            html,
            body {
                background: #ffffff !important;
                color: #000000 !important;
            }

            aside,
            header,
            nav,
            .print-hidden,
            .print\:hidden,
            [data-app-content] > header {
                display: none !important;
            }

            [data-app-shell],
            [data-app-content],
            main {
                display: block !important;
                width: 100% !important;
                max-width: none !important;
                min-width: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #ffffff !important;
            }

            body * {
                visibility: hidden;
            }

            #report-print-area {
                display: none !important;
            }

            #report-pdf-area,
            #report-pdf-area * {
                visibility: visible;
            }

            #report-pdf-area {
                display: block !important;
                position: absolute;
                inset: 0 auto auto 0;
                width: 100%;
                background: #ffffff !important;
                color: #000000 !important;
                font-family: Arial, Helvetica, sans-serif !important;
                font-size: 10px !important;
                line-height: 1.35 !important;
            }

            #report-pdf-area .pdf-title {
                margin: 0;
                color: #111827 !important;
                font-size: 16px !important;
                font-weight: 700 !important;
                text-align: center;
            }

            #report-pdf-area .pdf-kicker,
            #report-pdf-area .pdf-subtitle {
                margin: 0;
                color: #374151 !important;
                font-size: 10px !important;
                text-align: center;
            }

            #report-pdf-area .pdf-header {
                margin-bottom: 8px;
                padding-bottom: 8px;
                border-bottom: 2px solid #111827;
            }

            #report-pdf-area .pdf-section {
                margin-top: 8px;
                break-inside: avoid;
            }

            #report-pdf-area .pdf-section-title {
                margin: 0 0 4px;
                color: #111827 !important;
                font-size: 11px !important;
                font-weight: 700 !important;
            }

            #report-pdf-area .pdf-grid-2 {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 8px;
            }

            #report-pdf-area table {
                width: 100% !important;
                border-collapse: collapse !important;
                table-layout: fixed !important;
                background: #ffffff !important;
                page-break-inside: auto;
            }

            #report-pdf-area thead {
                display: table-header-group;
            }

            #report-pdf-area tr {
                page-break-inside: avoid;
            }

            #report-pdf-area th,
            #report-pdf-area td {
                border: 1px solid #d1d5db !important;
                color: #000000 !important;
                padding: 5px 6px !important;
                font-size: 10px !important;
                vertical-align: top !important;
                word-wrap: break-word !important;
                overflow-wrap: break-word !important;
                white-space: normal !important;
            }

            #report-pdf-area th {
                background: #f3f4f6 !important;
                font-weight: 700 !important;
            }

            #report-pdf-area .pdf-number {
                text-align: right;
            }

            #report-pdf-area .pdf-footer {
                position: fixed;
                right: 0;
                bottom: -5mm;
                left: 0;
                color: #4b5563 !important;
                font-size: 9px !important;
                text-align: center;
            }

            #report-pdf-area .pdf-page-number::after {
                content: counter(page);
            }
        }
    </style>

    <section class="print-hidden mb-6 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <div class="flex flex-col gap-3 border-b border-gray-200 pb-4 dark:border-gray-700 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Filter Laporan Aset</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Default laporan menampilkan aset sedang, rusak, dan hilang untuk kebutuhan manajemen.</p>
            </div>
            <div class="flex flex-col gap-2 sm:flex-row">
                <a href="{{ route('reports.index') }}" class="inline-flex justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-800">Reset</a>
                <a href="{{ route('assets.export', $reportQuery) }}" class="inline-flex justify-center rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-100">Export Excel</a>
                <button type="button" onclick="window.print()" class="inline-flex justify-center rounded-lg border border-[#465fff] px-4 py-2 text-sm font-semibold text-[#465fff] hover:bg-blue-50">Cetak/PDF</button>
            </div>
        </div>

        <form method="GET" class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <label class="block min-w-0 md:col-span-2 xl:col-span-3">
                <span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Cari Aset</span>
                <input type="search" name="keyword" value="{{ request('keyword') }}" placeholder="Nama, kode aset, kode lama, atau QR" class="mt-1 w-full min-w-0 rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-950">
            </label>

            <label class="block min-w-0">
                <span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Fokus Laporan</span>
                <select name="report_scope" class="mt-1 w-full min-w-0 rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-950">
                    <option value="attention" @selected($reportScope === 'attention')>Perlu Perhatian</option>
                    <option value="all" @selected($reportScope === 'all')>Semua Aset</option>
                </select>
            </label>

            @if (! auth()->user()->isUnitScoped())
                <label class="block min-w-0">
                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Unit/Laboratorium</span>
                    <select name="unit_id" class="mt-1 w-full min-w-0 rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-950">
                        <option value="">Semua Unit</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id }}" @selected(request('unit_id') == $unit->id)>{{ $unit->name }}</option>
                        @endforeach
                    </select>
                </label>
            @endif

            <label class="block min-w-0">
                <span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Kategori</span>
                <select name="category_id" class="mt-1 w-full min-w-0 rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-950">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </label>

            <label class="block min-w-0">
                <span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Lokasi</span>
                <select name="location_id" class="mt-1 w-full min-w-0 rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-950">
                    <option value="">Semua Lokasi</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location->id }}" @selected(request('location_id') == $location->id)>{{ $location->name }}</option>
                    @endforeach
                </select>
            </label>

            <label class="block min-w-0">
                <span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Penyimpanan</span>
                <select name="container_id" class="mt-1 w-full min-w-0 rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-950">
                    <option value="">Semua Penyimpanan</option>
                    @foreach ($containers as $container)
                        <option value="{{ $container->id }}" @selected(request('container_id') == $container->id)>{{ $container->name }}</option>
                    @endforeach
                </select>
            </label>

            <label class="block min-w-0">
                <span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Kondisi Aset</span>
                <select name="kondisi_aset" class="mt-1 w-full min-w-0 rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-950">
                    <option value="">{{ $reportScope === 'all' ? 'Semua Kondisi' : 'Semua Kondisi Prioritas' }}</option>
                    @foreach (collect($conditionLabels)->except('baik') as $value => $label)
                        <option value="{{ $value }}" @selected(request('kondisi_aset') === $value)>{{ $label }}</option>
                    @endforeach
                    <option value="baik" @selected(request('kondisi_aset') === 'baik')>Baik (opsional)</option>
                </select>
            </label>

            <div class="flex items-end">
                <button class="w-full rounded-lg bg-[#465fff] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#3641f5]">Filter</button>
            </div>
        </form>

        @if ($activeFilters->isNotEmpty())
            <div class="mt-4 flex flex-wrap gap-2">
                @foreach ($activeFilters as [$label, $value])
                    <span class="inline-flex items-center rounded-full border border-blue-100 bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                        {{ $label }}: {{ $value }}
                    </span>
                @endforeach
            </div>
        @endif
    </section>

    <div id="report-print-area" class="space-y-6">
        <section class="report-card rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="flex flex-col gap-4 border-b border-gray-200 pb-5 dark:border-gray-700 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-[#465fff]">SIMASET SBH</p>
                    <h1 class="mt-2 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $reportScope === 'all' ? 'Laporan Aset Laboratorium' : 'Laporan Aset Perlu Perhatian' }}</h1>
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">STIKes Bogor Husada</div>
            </div>
            <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                @foreach ($reportMeta as [$label, $value])
                    <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-800">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ $label }}</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $value }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-7">
            @foreach ($summaryCards as [$label, $value, $description, $tone])
                <div class="report-card rounded-2xl border {{ $tone }} p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                    <p class="text-sm font-semibold">{{ $label }}</p>
                    <h2 class="mt-2 text-3xl font-bold">{{ number_format($value) }}</h2>
                    <p class="mt-2 text-xs opacity-80">{{ $description }}</p>
                </div>
            @endforeach

            @foreach ($displayConditionLabels as $value => $label)
                <div class="report-card rounded-2xl border {{ $conditionTones[$value] ?? 'border-gray-200 bg-white text-gray-700' }} p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                    <p class="text-sm font-semibold">{{ $label }}</p>
                    <h2 class="mt-2 text-3xl font-bold">{{ number_format($byCondition[$value] ?? 0) }}</h2>
                    <p class="mt-2 text-xs opacity-80">{{ number_format($byConditionQuantity[$value] ?? 0) }} total aset</p>
                </div>
            @endforeach
        </section>

        <div class="grid gap-6 lg:grid-cols-2">
            @foreach ($recapSections as [$title, $items, $relation])
                <section class="report-card rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ $title }}</h2>
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                            <thead class="bg-gray-50 text-left text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                                <tr>
                                    <th class="px-4 py-3">Nama</th>
                                    <th class="px-4 py-3 text-right">Data Aset</th>
                                    <th class="px-4 py-3 text-right">{{ $quantityColumnLabel }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse ($items as $item)
                                    <tr>
                                        <td class="px-4 py-3 text-gray-800 dark:text-gray-100">{{ $item->{$relation}?->name ?? ($relation === 'container' ? 'Tanpa Penyimpanan' : '-') }}</td>
                                        <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-gray-100">{{ number_format($item->total) }}</td>
                                        <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-200">{{ number_format($item->quantity_total) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="px-4 py-6 text-center text-gray-500">Data belum tersedia.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            @endforeach
        </div>

        <section class="report-card rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">Rekap Kondisi Aset</h2>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                    <thead class="bg-gray-50 text-left text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                        <tr>
                            <th class="px-4 py-3">Kondisi Aset</th>
                            <th class="px-4 py-3 text-right">Data Aset</th>
                            <th class="px-4 py-3 text-right">{{ $quantityColumnLabel }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach ($displayConditionLabels as $value => $label)
                            <tr>
                                <td class="px-4 py-3 text-gray-800 dark:text-gray-100">{{ $label }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-gray-100">{{ number_format($byCondition[$value] ?? 0) }}</td>
                                <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-200">{{ number_format($byConditionQuantity[$value] ?? 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="report-card rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">Rincian Aset</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ number_format($reportAssets->count()) }} data sesuai filter laporan.</p>
                </div>
                <a href="{{ route('assets.index', $reportQuery) }}" class="print-hidden inline-flex justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-800">Buka Data Aset</a>
            </div>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                    <thead class="bg-gray-50 text-left text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                        <tr>
                            <th class="px-4 py-3">Kode</th>
                            <th class="px-4 py-3">Nama Aset</th>
                            <th class="px-4 py-3">Unit</th>
                            <th class="px-4 py-3">Lokasi</th>
                            <th class="px-4 py-3">Penyimpanan</th>
                            <th class="px-4 py-3 text-right">{{ $quantityColumnLabel }}</th>
                            <th class="px-4 py-3">Kondisi</th>
                            <th class="print-hidden px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($reportAssets as $asset)
                            <tr>
                                <td class="px-4 py-3 font-mono text-xs text-gray-700 dark:text-gray-200">{{ $asset->asset_code }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $asset->name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $asset->category?->name ?? 'Tanpa kategori' }}</div>
                                </td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-200">{{ $asset->unit?->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-200">{{ $asset->location?->name ?? 'Tanpa Lokasi' }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-200">{{ $asset->container?->name ?? 'Tanpa Penyimpanan' }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="font-semibold text-gray-900 dark:text-gray-100">{{ number_format($reportQuantityForAsset($asset)) }} {{ $asset->satuan ?: 'unit' }}</div>
                                    @if ($reportQuantityForAsset($asset) !== (int) ($asset->quantity ?? 1))
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Total fisik: {{ number_format($asset->quantity ?? 1) }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-100">{{ $asset->conditionSummaryLabel() }}</td>
                                <td class="print-hidden px-4 py-3 text-right">
                                    <a href="{{ route('assets.show', $asset) }}" class="font-semibold text-[#465fff] hover:underline">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">Tidak ada aset yang cocok dengan filter laporan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

    </div>

    @include('reports.pdf')
@endsection
