@extends('layouts.dashboard')

@section('title', 'Dashboard Admin')

@section('content')
    @php
        $conditionLabels = \App\Models\Asset::KONDISI_ASET_LABELS;
        $topStats = [
            ['Total Aset', $stats['assets'], 'Semua aset tercatat', 'asset', 'bg-blue-50 text-blue-700'],
            ['Perlu Perhatian', $stats['attention_assets'], 'Sedang, rusak, atau hilang', 'report', 'bg-amber-50 
            text-amber-700'],
            ['Surat Terbuka', $stats['open_tool_replacements'], 'Menunggu verifikasi/penggantian', 'report', 
            'bg-red-50 text-red-700'],
            ['Pemeriksaan Aktif', $stats['inventory_checks_ongoing'], 'Sesi inventaris berjalan', 'inventory', 
            'bg-emerald-50 text-emerald-700'],
        ];
        $supportStats = [
            ['Total Unit/Laboratorium', $stats['units'], 'unit', 'bg-blue-50 text-blue-700'],
            ['Total Kategori', $stats['categories'], 'category', 'bg-cyan-50 text-cyan-700'],
            ['Total Penyimpanan', $stats['containers'], 'container', 'bg-indigo-50 text-indigo-700'],
            ['Total Pengguna', $stats['users'], 'user', 'bg-slate-100 text-slate-700'],
        ];
    @endphp

    <div class="mb-6 flex flex-col gap-4 rounded-lg border border-gray-200 bg-white p-5 shadow-sm lg:flex-row 
    lg:items-center lg:justify-between">
        <div>
            <p class="text-sm font-medium text-[#465fff]">Ringkasan sistem</p>
            <h2 class="mt-2 text-2xl font-bold text-gray-900">Kontrol inventaris SIMASET SBH</h2>
            <p class="mt-2 max-w-3xl text-sm text-gray-500">Pantau kesehatan aset, surat penggantian alat rusak, 
                dan progres pemeriksaan dari satu layar.</p>
        </div>
        <div class="flex flex-col gap-2 sm:flex-row">
            <a href="{{ route('reports.index') }}" class="inline-flex justify-center rounded-lg border border-[#465fff] px-4 
            py-2 text-sm font-semibold text-[#465fff] hover:bg-blue-50">Laporan Aset</a>
            <a href="{{ route('assets.create') }}" class="inline-flex justify-center rounded-lg bg-[#465fff] px-4 py-2 text-sm 
            font-semibold text-white hover:bg-[#3641f5]">Tambah Aset</a>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach ($topStats as [$label, $value, $caption, $iconName, $tone])
            <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <p class="text-sm text-gray-500">{{ $label }}</p>
                        <h3 class="mt-2 break-words text-2xl font-bold text-gray-900">{{ number_format($value) }}</h3>
                    </div>
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg {{ $tone }}"><x-nav-icon :name="$iconName" /></span>
                </div>
                <p class="mt-4 text-sm text-gray-500">{{ $caption }}</p>
            </section>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1.25fr)_430px]">
        <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-gray-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Aset Terbaru</h2>
                    <p class="mt-1 text-sm text-gray-500">Data inventaris terakhir yang masuk ke sistem.</p>
                </div>
                <a href="{{ route('assets.index') }}" class="text-sm font-semibold text-[#465fff] hover:text-[#3641f5]">Lihat semua</a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr>
                            <th class="px-5 py-3">Kode</th>
                            <th class="px-5 py-3">Nama</th>
                            <th class="px-5 py-3">Kategori</th>
                            <th class="px-5 py-3">Unit</th>
                            <th class="px-5 py-3">Kondisi Aset</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($latestAssets as $asset)
                            <tr>
                                <td class="px-5 py-3 font-medium text-gray-900">{{ $asset->asset_code }}</td>
                                <td class="px-5 py-3 text-gray-700">{{ $asset->name }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $asset->category?->name ?? '-' }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $asset->unit?->name ?? '-' }}</td>
                                <td class="px-5 py-3">
                                    <span class="rounded-md bg-gray-100 px-2 py-1 text-xs font-medium text-gray-700">{{ 
                                        $asset->kondisi_aset_label }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-10 text-center text-gray-500">Belum ada aset.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <aside class="space-y-6">
            <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Penggantian Alat</h2>
                        <p class="mt-1 text-sm text-gray-500">Surat terbaru dari mahasiswa.</p>
                    </div>
                    <a href="{{ route('tool-replacements.index') }}" class="text-sm font-semibold text-[#465fff] 
                    hover:text-[#3641f5]">Kelola</a>
                </div>
                <div class="mt-4 space-y-3">
                    @forelse ($latestToolReplacements as $replacement)
                        <a href="{{ route('tool-replacements.show', $replacement) }}" class="block rounded-lg bg-gray-50 
                        p-3 text-sm hover:bg-gray-100">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="truncate font-semibold text-gray-900">{{ $replacement->student_name }}</p>
                                    <p class="mt-1 truncate text-xs text-gray-500">{{ $replacement->replacement_code }} |
                                        {{ $replacement->asset?->name ?? '-' }}</p>
                                </div>
                                @include('partials.badge', ['value' => $replacement->status])
                            </div>
                        </a>
                    @empty
                        <p class="rounded-lg border border-dashed border-gray-300 p-4 text-sm text-gray-500">Belum ada 
                            surat penggantian.</p>
                    @endforelse
                </div>
            </section>

            <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900">Kondisi Aset</h2>
                <p class="mt-1 text-sm text-gray-500">Berdasarkan jumlah kondisi terbaru dari stock opname.</p>

                <div class="mt-5 space-y-4">
                    @foreach ($conditionLabels as $condition => $label)
                        @php
                            $total = $assetCondition[$condition] ?? 0;
                            $percent = $stats['asset_quantity'] > 0 ? round(($total / $stats['asset_quantity']) * 100) : 0;
                        @endphp
                        <div>
                            <div class="mb-2 flex items-center justify-between gap-3 text-sm">
                                <span class="font-medium text-gray-700">{{ $label }}</span>
                                <span class="text-gray-500">{{ number_format($total) }} aset</span>
                            </div>
                            <div class="h-2 rounded-full bg-gray-100">
                                <div class="h-2 rounded-full bg-[#465fff]" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900">Master Data</h2>
                <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-1">
                    @foreach ($supportStats as [$label, $value, $iconName, $tone])
                        <div class="flex items-center justify-between gap-3 rounded-lg bg-gray-50 px-4 py-3">
                            <div class="flex min-w-0 items-center gap-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg 
                                {{ $tone }}"><x-nav-icon :name="$iconName" /></span>
                                <span class="truncate text-sm font-medium text-gray-700">{{ $label }}</span>
                            </div>
                            <span class="text-sm font-bold text-gray-900">{{ number_format($value) }}</span>
                        </div>
                    @endforeach
                </div>
            </section>
        </aside>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[380px_minmax(0,1fr)]">
        <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900">Aksi Cepat</h2>
            <div class="mt-4 grid gap-3">
                @foreach ([
                    ['Tambah Unit', route('units.create'), 'unit'],
                    ['Tambah Pengguna', route('users.create'), 'user'],
                    ['Tambah Kategori', route('categories.create'), 'category'],
                    ['Laporan Aset', route('reports.index'), 'report'],
                ] as [$label, $url, $iconName])
                    <a href="{{ $url }}" class="flex items-center justify-between rounded-lg border border-gray-200 
                    px-4 py-3 text-sm font-semibold text-gray-700 hover:border-[#c2d6ff] hover:bg-[#ecf3ff] hover:text-[#465fff]">
                        <span class="flex items-center gap-3"><x-nav-icon :name="$iconName" /> {{ $label }}</span>
                        <span aria-hidden="true">&gt;</span>
                    </a>
                @endforeach
            </div>

            <div class="mt-5 grid grid-cols-2 gap-3 border-t border-gray-200 pt-5">
                <div class="rounded-lg bg-gray-50 p-3">
                    <div class="flex items-center gap-2 text-xs font-medium text-gray-500"><x-nav-icon name="inventory" /> Sesi Pemeriksaan</div>
                    <p class="mt-2 text-xl font-bold text-gray-900">{{ number_format($stats['inventory_checks']) }}</p>
                </div>
                <div class="rounded-lg bg-gray-50 p-3">
                    <div class="flex items-center gap-2 text-xs font-medium text-gray-500"><x-nav-icon name="inventory" /> Pemeriksaan Aktif</div>
                    <p class="mt-2 text-xl font-bold text-gray-900">{{ number_format($stats['inventory_checks_ongoing']) }}</p>
                </div>
                <div class="rounded-lg bg-gray-50 p-3">
                    <div class="flex items-center gap-2 text-xs font-medium text-gray-500"><x-nav-icon name="report" /> Pemeriksaan Selesai</div>
                    <p class="mt-2 text-xl font-bold text-gray-900">{{ number_format($stats['inventory_checks_completed']) }}</p>
                </div>
                <div class="rounded-lg bg-gray-50 p-3">
                    <div class="flex items-center gap-2 text-xs font-medium text-gray-500"><x-nav-icon name="container" /> Penyimpanan</div>
                    <p class="mt-2 text-xl font-bold text-gray-900">{{ number_format($stats['containers']) }}</p>
                </div>
            </div>
        </section>

        <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Aset Per Unit</h2>
                    <p class="mt-1 text-sm text-gray-500">Sebaran aset berdasarkan unit kerja.</p>
                </div>
                <a href="{{ route('units.index') }}" class="text-sm font-semibold text-[#465fff] hover:text-[#3641f5]">Kelola unit</a>
            </div>

            <div class="mt-5 space-y-3">
                @forelse ($assetByUnit as $item)
                    @php
                        $percent = $stats['assets'] > 0 ? round(($item->total / $stats['assets']) * 100) : 0;
                    @endphp
                    <div>
                        <div class="mb-2 flex items-center justify-between gap-3 text-sm">
                            <span class="font-medium text-gray-700">{{ $item->unit?->name ?? '-' }}</span>
                            <span class="text-gray-500">{{ number_format($item->total) }} aset</span>
                        </div>
                        <div class="h-2 rounded-full bg-gray-100">
                            <div class="h-2 rounded-full bg-[#465fff]" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-lg border border-dashed border-gray-300 p-5 text-sm text-gray-500">Belum ada data aset per unit.</div>
                @endforelse
            </div>
        </section>
    </div>

    <section class="mt-6 rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Pemeriksaan Inventaris Terbaru</h2>
                <p class="mt-1 text-sm text-gray-500">Progress pemeriksaan inventaris per sesi.</p>
            </div>
            <a href="{{ route('inventory-checks.index') }}" class="text-sm font-semibold text-[#465fff] hover:text-[#3641f5]">Lihat semua</a>
        </div>

        <div class="mt-5 grid gap-3 lg:grid-cols-2">
            @forelse ($latestInventoryChecks as $check)
                @php
                    $total = $check->getAttribute('dashboard_total_assets');
                    $checked = $check->getAttribute('dashboard_checked_assets');
                    $unchecked = $check->getAttribute('dashboard_unchecked_assets');
                    $percent = $check->getAttribute('dashboard_progress_percent');
                @endphp
                <a href="{{ route('inventory-checks.show', $check) }}" class="rounded-lg bg-gray-50 p-4 text-sm hover:bg-gray-100">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold text-gray-900">{{ $check->unit?->name ?? '-' }}</p>
                            <p class="mt-1 text-gray-500">{{ $check->periode ?: $check->semester.' '.$check->tahun_akademik }}</p>
                        </div>
                        @include('partials.badge', ['value' => $check->status])
                    </div>
                    <div class="mt-3 h-2 rounded-full bg-gray-200">
                        <div class="h-2 rounded-full bg-[#465fff]" style="width: {{ $percent }}%"></div>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">{{ number_format($checked) }} dari {{ number_format($total) }} 
                        aset dicek, {{ number_format($unchecked) }} belum dicek</p>
                </a>
            @empty
                <div class="rounded-lg border border-dashed border-gray-300 p-5 text-sm text-gray-500">Belum ada sesi pemeriksaan inventaris.</div>
            @endforelse
        </div>
    </section>
@endsection
