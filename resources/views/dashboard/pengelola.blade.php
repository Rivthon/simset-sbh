@extends('layouts.dashboard')

@section('title', 'Dashboard Laboran')

@section('content')
    @php
        $conditionLabels = \App\Models\Asset::KONDISI_ASET_LABELS;
        $conditionTones = [
            'baik' => 'bg-emerald-500',
            'sedang' => 'bg-amber-500',
            'rusak' => 'bg-red-500',
            'hilang' => 'bg-slate-500',
        ];
        $assetRows = [
            ['Total Aset', $stats['assets'], 'Record aset utama', 'asset'],
            ['Laporan Penggantian Aset', $stats['open_tool_replacements'], 'Belum selesai', 'report'],
        ];
    @endphp

    <div class="mb-4 flex flex-col gap-3 rounded-lg border border-slate-200 bg-white p-4 shadow-sm lg:flex-row lg:items-center lg:justify-between">
        <div>
            <p class="text-sm font-medium text-[#465fff]">{{ auth()->user()->unit?->name ?? 'Unit' }}</p>
            <h2 class="mt-1 text-xl font-bold text-slate-900">Dashboard Laboran</h2>
            <p class="mt-1 max-w-3xl text-xs text-slate-500">Fokus harian aset unit, laporan penggantian, dan pemeriksaan inventaris.</p>
        </div>
        <div class="flex flex-col gap-2 sm:flex-row">
            <a href="{{ route('assets.index') }}" class="inline-flex justify-center rounded-lg border border-[#465fff] px-4 py-2 text-sm font-semibold text-[#465fff] hover:bg-blue-50">Data Aset</a>
            <a href="{{ route('inventory-checks.create') }}" class="inline-flex justify-center rounded-lg bg-[#465fff] px-4 py-2 text-sm font-semibold text-white hover:bg-[#3641f5]">Buat Pemeriksaan</a>
        </div>
    </div>

    @if ($ongoingInventoryCheck)
        @php
            $ongoingTotal = $ongoingInventoryCheck->getAttribute('dashboard_total_assets');
            $ongoingChecked = $ongoingInventoryCheck->getAttribute('dashboard_checked_assets');
            $ongoingUnchecked = $ongoingInventoryCheck->getAttribute('dashboard_unchecked_assets');
            $ongoingPercent = $ongoingInventoryCheck->getAttribute('dashboard_progress_percent');
            $ongoingPeriod = $ongoingInventoryCheck->periode ?: trim(($ongoingInventoryCheck->semester ?? '').' '.($ongoingInventoryCheck->tahun_akademik ?? ''));
        @endphp
        <section class="mb-4 mt-1 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 shadow-sm">
            <div class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_160px] lg:items-center">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <p class="text-sm font-semibold text-slate-900">Pemeriksaan Inventaris Aktif</p>
                        <span class="rounded-full bg-blue-100 px-2.5 py-1 text-[11px] font-bold text-blue-700">Berjalan</span>
                        <span class="text-sm text-slate-600">{{ $ongoingPeriod ?: 'Periode belum diisi' }}</span>
                    </div>
                    <div class="mt-2 grid gap-2 text-xs text-slate-600 sm:grid-cols-3">
                        <span><strong class="text-slate-900">{{ number_format($ongoingTotal) }}</strong> total aset</span>
                        <span><strong class="text-slate-900">{{ number_format($ongoingChecked) }}</strong> sudah diperiksa</span>
                        <span><strong class="text-slate-900">{{ number_format($ongoingUnchecked) }}</strong> belum diperiksa</span>
                    </div>
                    <div class="mt-2 flex items-center gap-3">
                        <div class="h-1.5 flex-1 rounded-full bg-white">
                            <div class="h-1.5 rounded-full bg-[#465fff]" style="width: {{ $ongoingPercent }}%"></div>
                        </div>
                        <span class="shrink-0 text-xs font-bold text-blue-700">{{ $ongoingPercent }}%</span>
                    </div>
                </div>
                <a href="{{ route('inventory-checks.scan.form', $ongoingInventoryCheck) }}" class="inline-flex justify-center rounded-lg bg-[#465fff] px-4 py-2 text-sm font-semibold text-white hover:bg-[#3641f5]">Lanjut Scan</a>
            </div>
        </section>
    @endif

    <section class="grid gap-4 xl:grid-cols-[360px_minmax(0,1fr)]">
        <div class="rounded-lg border border-slate-200 bg-white p-3 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-sm font-semibold text-slate-900">Rekap Aset</h2>
                <a href="{{ route('assets.create') }}" class="text-xs font-semibold text-[#465fff]">Tambah aset</a>
            </div>
            <div class="mt-3 grid gap-2">
                @foreach ($assetRows as [$label, $value, $description, $iconName])
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-3">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-medium text-slate-500">{{ $label }}</p>
                                <p class="mt-1 text-2xl font-bold leading-none text-slate-900">{{ number_format($value) }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $description }}</p>
                            </div>
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white text-[#465fff]"><x-nav-icon :name="$iconName" /></span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rounded-lg border border-slate-200 bg-white p-3 shadow-sm">
            <h3 class="text-sm font-semibold text-slate-800">Aset Per Lokasi</h3>
            <div class="mt-2 grid gap-2 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($assetByLocation as $item)
                    @php
                        $percent = $stats['assets'] > 0 ? round(($item->total / $stats['assets']) * 100) : 0;
                    @endphp
                    <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
                        <div class="flex items-center justify-between gap-3">
                            <p class="min-w-0 truncate text-xs font-semibold text-slate-800">{{ $item->location?->name ?? 'Tanpa lokasi' }}</p>
                            <div class="shrink-0 text-right">
                                <p class="text-sm font-bold leading-none text-slate-900">{{ number_format($item->total) }}</p>
                                <p class="text-[10px] text-slate-500">aset</p>
                            </div>
                        </div>
                        <div class="mt-2 h-1 rounded-full bg-white"><div class="h-1 rounded-full bg-slate-500" style="width: {{ $percent }}%"></div></div>
                    </div>
                @empty
                    <p class="rounded-lg border border-dashed border-slate-300 p-4 text-sm text-slate-500">Belum ada data lokasi.</p>
                @endforelse
            </div>
        </div>
    </section>

    <div class="mt-4 grid gap-4 xl:grid-cols-3">
        <section class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Penggantian Alat Rusak</h2>
                    <p class="mt-1 text-sm text-slate-500">Surat terbaru yang masuk ke unit.</p>
                </div>
                <a href="{{ route('tool-replacements.index') }}" class="text-sm font-semibold text-[#465fff]">Kelola surat</a>
            </div>

            <div class="mt-4 space-y-2">
                @forelse ($latestToolReplacements as $replacement)
                    <a href="{{ route('tool-replacements.show', $replacement) }}" class="block rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm hover:bg-slate-100">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-slate-900">{{ $replacement->student_name }}</p>
                                <p class="mt-1 truncate text-slate-500">{{ $replacement->replacement_code }} | {{ $replacement->asset?->name ?? '-' }}</p>
                            </div>
                            @include('partials.badge', ['value' => $replacement->status])
                        </div>
                    </a>
                @empty
                    <p class="rounded-lg border border-dashed border-slate-300 p-4 text-sm text-slate-500">Belum ada surat penggantian alat.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Rekap Kondisi Aset</h2>
                    <p class="mt-1 text-sm text-slate-500">Berdasarkan jumlah kondisi terbaru dari stock opname.</p>
                </div>
                <a href="{{ route('assets.index', ['kondisi_aset' => 'rusak']) }}" class="text-sm font-semibold text-[#465fff]">Lihat aset rusak</a>
            </div>

            <div class="mt-4 grid gap-2 sm:grid-cols-2">
                @foreach ($conditionLabels as $condition => $label)
                    @php
                        $total = $assetCondition[$condition] ?? 0;
                        $percent = $stats['asset_quantity'] > 0 ? round(($total / $stats['asset_quantity']) * 100) : 0;
                    @endphp
                    <a href="{{ route('assets.index', ['kondisi_aset' => $condition]) }}" class="rounded-lg border border-slate-200 bg-slate-50 p-3 hover:bg-slate-100">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-slate-800">{{ $label }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $percent }}% dari total aset</p>
                            </div>
                            <span class="text-2xl font-bold text-slate-900">{{ number_format($total) }}</span>
                        </div>
                        <div class="mt-3 h-1.5 rounded-full bg-white"><div class="h-1.5 rounded-full {{ $conditionTones[$condition] ?? 'bg-[#465fff]' }}" style="width: {{ $percent }}%"></div></div>
                    </a>
                @endforeach
            </div>
        </section>

        <section class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Pemeriksaan Inventaris</h2>
                    <p class="mt-1 text-sm text-slate-500">Satu sesi untuk satu periode/semester, lalu aset dicek via QR atau input manual.</p>
                </div>
                <a href="{{ route('inventory-checks.index') }}" class="text-sm font-semibold text-[#465fff]">Semua sesi</a>
            </div>

            <div class="mt-4 space-y-2">
                @forelse ($latestInventoryChecks as $check)
                    @php
                        $total = $check->getAttribute('dashboard_total_assets');
                        $checked = $check->getAttribute('dashboard_checked_assets');
                        $unchecked = $check->getAttribute('dashboard_unchecked_assets');
                        $percent = $check->getAttribute('dashboard_progress_percent');
                        $period = $check->periode ?: trim(($check->semester ?? '').' '.($check->tahun_akademik ?? ''));
                    @endphp
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $period ?: 'Periode belum diisi' }}</p>
                                <p class="mt-1 text-slate-500">{{ $check->tanggal_pemeriksaan?->format('d/m/Y') ?? '-' }}</p>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @include('partials.badge', ['value' => $check->status])
                                @if ($check->status !== 'completed')
                                    <a href="{{ route('inventory-checks.scan.form', $check) }}" class="rounded-md bg-[#465fff] px-3 py-1.5 text-xs font-semibold text-white">Scan</a>
                                @endif
                            </div>
                        </div>
                        <div class="mt-3 h-1.5 rounded-full bg-white"><div class="h-1.5 rounded-full bg-[#465fff]" style="width: {{ $percent }}%"></div></div>
                        <div class="mt-2 grid gap-2 text-xs text-slate-500 sm:grid-cols-3">
                            <span>Total aset: <strong class="text-slate-800">{{ number_format($total) }}</strong></span>
                            <span>Sudah dicek: <strong class="text-slate-800">{{ number_format($checked) }}</strong></span>
                            <span>Belum dicek: <strong class="text-slate-800">{{ number_format($unchecked) }}</strong></span>
                        </div>
                    </div>
                @empty
                    <div class="rounded-lg border border-dashed border-slate-300 p-5 text-sm text-slate-500">Belum ada sesi pemeriksaan inventaris.</div>
                @endforelse
            </div>
        </section>
    </div>
@endsection
