@extends('layouts.dashboard')

@section('title', 'Detail Pemeriksaan Inventaris')

@section('content')
    @php
        $resultLabels = ['pending' => 'Belum Dicek', 'sesuai' => 'Sesuai', 'tidak_sesuai' => 'Tidak Sesuai'];
        $checkedItems = $check->items->whereNotNull('jumlah_aktual')->sortBy(fn ($item) => ($item->asset?->location?->name ?? 'Tanpa Lokasi').' '.$item->asset?->name);
        $uncheckedItems = $check->items->whereNull('jumlah_aktual')->sortBy(fn ($item) => ($item->asset?->location?->name ?? 'Tanpa Lokasi').' '.$item->asset?->name);
        $checkedGroups = $checkedItems->groupBy(fn ($item) => $item->asset?->location?->name ?? 'Tanpa Lokasi');
        $uncheckedGroups = $uncheckedItems->groupBy(fn ($item) => $item->asset?->location?->name ?? 'Tanpa Lokasi');
        $canComplete = $check->status !== 'completed' && $progress['unchecked'] === 0;
    @endphp

    <section class="mb-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <p class="text-sm font-semibold text-[#465fff]">{{ $check->check_code }}</p>
                <h2 class="mt-1 text-2xl font-bold">{{ $check->unit?->name }}</h2>
                <p class="mt-2 text-sm text-gray-500">{{ $check->periode ?: $check->semester.' '.$check->tahun_akademik }} | {{ $check->tanggal_pemeriksaan?->format('d/m/Y') }}</p>
                @if ($check->notes)
                    <p class="mt-2 text-sm text-gray-600">{{ $check->notes }}</p>
                @endif
            </div>
            @include('partials.badge', ['value' => $check->status])
        </div>

        <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-6">
            @foreach ([['Total Aset', $progress['total']], ['Sudah Diperiksa', $progress['checked']], ['Belum Diperiksa', $progress['unchecked']], ['Progress', $progress['percent'].'%'], ['Sesuai', $progress['sesuai']], ['Tidak Sesuai', $progress['tidak_sesuai']]] as [$label, $value])
                <div class="rounded-xl bg-gray-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ $label }}</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">{{ is_numeric($value) ? number_format($value) : $value }}</p>
                </div>
            @endforeach
        </div>

        @if (auth()->user()->hasRole(['admin', 'pengelola']) && $check->status !== 'completed')
            <div class="mt-5 flex flex-wrap gap-3">
                <a href="{{ route('inventory-checks.scan.form', $check) }}" class="rounded-lg bg-[#465fff] px-4 py-2 text-sm font-semibold text-white">Scan QR</a>
            </div>
        @endif
    </section>

    <section class="mb-6 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h3 class="font-semibold">Progress Berdasarkan Lokasi</h3>
            @if (auth()->user()->hasRole(['admin', 'pengelola']) && $check->status !== 'completed')
                <a href="{{ route('inventory-checks.scan.form', $check) }}" class="inline-flex justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700">Scan Semua Lokasi</a>
            @endif
        </div>
        <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($locationProgress as $location)
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold text-gray-900">{{ $location['label'] }}</p>
                            <p class="mt-1 text-sm text-gray-500">{{ number_format($location['checked']) }} dari {{ number_format($location['total']) }} aset diperiksa</p>
                        </div>
                        <p class="text-sm font-semibold text-[#465fff]">{{ $location['percent'] }}%</p>
                    </div>
                    <div class="mt-3 h-2 overflow-hidden rounded-full bg-gray-200">
                        <div class="h-full rounded-full bg-[#465fff]" style="width: {{ min($location['percent'], 100) }}%"></div>
                    </div>
                    <div class="mt-3 flex items-center justify-between gap-3 text-sm">
                        <span class="text-gray-500">Belum: {{ number_format($location['unchecked']) }}</span>
                        @if (auth()->user()->hasRole(['admin', 'pengelola']) && $check->status !== 'completed')
                            <a href="{{ route('inventory-checks.scan.form', ['inventoryCheck' => $check, 'location_id' => $location['id']]) }}" class="rounded-lg bg-[#465fff] px-3 py-1.5 text-xs font-semibold text-white">Input/Scan</a>
                        @endif
                    </div>
                </div>
            @empty
                <p class="rounded-xl bg-gray-50 p-4 text-sm text-gray-500">Belum ada aset pada unit ini.</p>
            @endforelse
        </div>
    </section>

    @if (auth()->user()->hasRole(['admin', 'pengelola']))
        <form method="POST" action="{{ route('inventory-checks.update', $check) }}" class="mb-6 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="completed">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-gray-600">
                    @if ($progress['unchecked'] > 0)
                        Masih ada {{ number_format($progress['unchecked']) }} aset yang belum diperiksa. Sesi bisa diselesaikan setelah semua aset diperiksa.
                    @else
                        Selesaikan sesi untuk memperbarui kondisi aset berdasarkan hasil pemeriksaan terakhir.
                    @endif
                </p>
                <button class="rounded-lg px-4 py-2 text-sm font-semibold text-white {{ $canComplete ? 'bg-[#465fff] hover:bg-[#3641f5]' : 'cursor-not-allowed bg-gray-300' }}" @disabled(! $canComplete)>Selesaikan Sesi</button>
            </div>
        </form>
    @endif

    <section class="mb-6 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
        <h3 class="font-semibold">Aset Sudah Diperiksa</h3>
        <div class="mt-4 w-full overflow-x-auto rounded-xl border border-gray-100">
            <table class="w-full min-w-[1280px] table-fixed divide-y divide-gray-200 text-sm">
                <colgroup>
                    <col class="w-[150px]">
                    <col class="w-[220px]">
                    <col class="w-[190px]">
                    <col class="w-[110px]">
                    <col class="w-[90px]">
                    <col class="w-[80px]">
                    <col class="w-[90px]">
                    <col class="w-[80px]">
                    <col class="w-[80px]">
                    <col class="w-[110px]">
                    <col class="w-[110px]">
                </colgroup>
                <thead class="bg-gray-50 text-left text-gray-600">
                    <tr><th class="px-4 py-3">Kode Aset</th><th class="px-4 py-3">Nama Aset</th><th class="px-4 py-3">Penyimpanan</th><th class="px-4 py-3">Jumlah Sistem</th><th class="px-4 py-3">Aktual</th><th class="px-4 py-3">Baik</th><th class="px-4 py-3">Sedang</th><th class="px-4 py-3">Rusak</th><th class="px-4 py-3">Hilang</th><th class="px-4 py-3">Hasil</th><th class="px-4 py-3">Aksi</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($checkedGroups as $locationName => $items)
                        <tr class="bg-gray-50">
                            <td colspan="11" class="px-4 py-2 text-xs font-semibold uppercase tracking-wide text-gray-600">{{ $locationName }}</td>
                        </tr>
                        @foreach ($items as $item)
                            <tr>
                                <td class="break-words px-4 py-3 font-medium">{{ $item->asset?->asset_code }}</td>
                                <td class="break-words px-4 py-3">{{ $item->asset?->name }}<p class="text-xs text-gray-500">{{ $item->asset?->category?->name }}</p></td>
                                <td class="break-words px-4 py-3">
                                    {{ $item->asset?->container?->name ?? '-' }}
                                    <p class="text-xs text-gray-500">{{ $item->asset?->location?->name ?? '-' }}</p>
                                </td>
                                <td class="px-4 py-3">{{ number_format($item->jumlah_sistem) }}</td>
                                <td class="px-4 py-3">{{ number_format($item->jumlah_aktual ?? 0) }}</td>
                                <td class="px-4 py-3">{{ number_format($item->jumlah_baik ?? 0) }}</td>
                                <td class="px-4 py-3">{{ number_format($item->jumlah_sedang ?? 0) }}</td>
                                <td class="px-4 py-3">{{ number_format($item->jumlah_rusak ?? 0) }}</td>
                                <td class="px-4 py-3">{{ number_format($item->jumlah_hilang ?? 0) }}</td>
                                <td class="px-4 py-3">{{ $resultLabels[$item->hasil_pemeriksaan] ?? ($item->hasil_pemeriksaan ?: '-') }}</td>
                                <td class="px-4 py-3">
                                    @if (auth()->user()->hasRole(['admin', 'pengelola']) && $check->status !== 'completed')
                                        <a href="{{ route('inventory-checks.scan', ['inventoryCheck' => $check, 'qrCode' => $item->asset?->asset_code, 'location_id' => $item->asset?->location_id ?? 'none']) }}" class="inline-flex whitespace-nowrap rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700">Edit Hasil</a>
                                    @else
                                        <span class="text-xs text-gray-400">Read-only</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr><td colspan="11" class="px-4 py-6 text-center text-gray-500">Belum ada aset yang diperiksa.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
        <h3 class="font-semibold">Aset Belum Diperiksa</h3>
        <div class="mt-4 w-full overflow-x-auto rounded-xl border border-gray-100">
            <table class="w-full min-w-[920px] table-fixed divide-y divide-gray-200 text-sm">
                <colgroup>
                    <col class="w-[150px]">
                    <col class="w-[260px]">
                    <col class="w-[170px]">
                    <col class="w-[190px]">
                    <col class="w-[110px]">
                    <col class="w-[120px]">
                </colgroup>
                <thead class="bg-gray-50 text-left text-gray-600">
                    <tr><th class="px-4 py-3">Kode Aset</th><th class="px-4 py-3">Nama Aset</th><th class="px-4 py-3">Kategori</th><th class="px-4 py-3">Penyimpanan</th><th class="px-4 py-3">Jumlah</th><th class="px-4 py-3">Aksi</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($uncheckedGroups as $locationName => $items)
                        <tr class="bg-gray-50">
                            <td colspan="6" class="px-4 py-2 text-xs font-semibold uppercase tracking-wide text-gray-600">{{ $locationName }}</td>
                        </tr>
                        @foreach ($items as $item)
                            <tr>
                                <td class="break-words px-4 py-3 font-medium">{{ $item->asset?->asset_code }}</td>
                                <td class="break-words px-4 py-3">{{ $item->asset?->name }}</td>
                                <td class="break-words px-4 py-3">{{ $item->asset?->category?->name ?? '-' }}</td>
                                <td class="break-words px-4 py-3">
                                    {{ $item->asset?->container?->name ?? '-' }}
                                    <p class="text-xs text-gray-500">{{ $item->asset?->location?->name ?? '-' }}</p>
                                </td>
                                <td class="px-4 py-3">{{ number_format($item->jumlah_sistem) }} {{ $item->asset?->satuan ?: 'unit' }}</td>
                                <td class="px-4 py-3">
                                    @if (auth()->user()->hasRole(['admin', 'pengelola']) && $check->status !== 'completed')
                                        <a href="{{ route('inventory-checks.scan', ['inventoryCheck' => $check, 'qrCode' => $item->asset?->asset_code, 'location_id' => $item->asset?->location_id ?? 'none']) }}" class="inline-flex whitespace-nowrap rounded-lg bg-[#465fff] px-3 py-1.5 text-xs font-semibold text-white">Input Hasil</a>
                                    @else
                                        <span class="text-xs text-gray-400">Belum diperiksa</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">Semua aset sudah diperiksa.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
