@extends('layouts.dashboard')

@section('title', 'Detail Aset')

@section('content')
    @php
        $canDeleteAsset = auth()->user()->canDeleteAssetInUnit($asset->unit_id);
        $scanCode = $asset->qr_code;
    @endphp

    <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-sm font-medium text-blue-600">{{ $asset->asset_code }}</p>
                <h2 class="mt-1 text-2xl font-bold">{{ $asset->name }}</h2>
                <p class="mt-2 text-sm text-slate-600">{{ $asset->category?->name ?? '-' }} | {{ $asset->unit?->name ?? '-' }} | {{ $asset->location?->name ?? '-' }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                @if ($scanCode)
                    <a href="{{ route('assets.qr', $asset) }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">QR Code</a>
                @elseif (auth()->user()->hasRole(['admin', 'pengelola']) && in_array($asset->identification_type, ['individual', 'group'], true))
                    <form method="POST" action="{{ route('assets.qr.generate', $asset) }}">
                        @csrf
                        <button class="rounded-md border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-100">Generate QR Code</button>
                    </form>
                @endif
                @if (auth()->user()->hasRole(['admin', 'pengelola']))
                    <a href="{{ route('assets.edit', $asset) }}" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Edit Aset</a>
                @endif
                @if ($canDeleteAsset)
                    <form method="POST" action="{{ route('assets.destroy', $asset) }}" onsubmit="return confirm('Hapus data aset ini secara permanen? Data yang sudah memiliki riwayat transaksi tidak dapat dihapus.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-md border border-red-200 bg-red-50 px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-100">Hapus Data</button>
                    </form>
                @endif
            </div>
        </div>

        <div class="mt-6 grid gap-6 lg:grid-cols-[1fr_240px]">
            <div>
                <dl class="grid gap-4 md:grid-cols-3">
                    @foreach ([
                        ['Kode Aset Sistem', $asset->asset_code],
                        ['Kode Aset Lama', $asset->legacy_inventory_code],
                        ['Nama Aset', $asset->name],
                        ['Unit/Laboratorium', $asset->unit?->name],
                        ['Kategori', $asset->category?->name],
                        ['Lokasi', $asset->location?->name],
                        ['Penyimpanan', $asset->container?->name],
                        ['Jumlah', number_format($asset->quantity ?? 1).' '.($asset->satuan ?: 'unit')],
                        ['Cara Identifikasi Aset', $asset->identificationLabel().' - '.$asset->identificationDescription()],
                        ['Kondisi Aset', $asset->kondisi_aset_label],
                        ['QR Code', $asset->qr_code],
                        ['Dibuat Oleh', $asset->creator?->name],
                    ] as [$label, $value])
                        <div class="rounded-md bg-slate-50 p-4">
                            <dt class="text-xs uppercase tracking-wide text-slate-500">{{ $label }}</dt>
                            <dd class="mt-1 text-sm font-semibold">{{ $value ?: '-' }}</dd>
                        </div>
                    @endforeach
                </dl>

                <div class="mt-4 rounded-md bg-slate-50 p-4">
                    <p class="text-xs uppercase tracking-wide text-slate-500">Keterangan</p>
                    <p class="mt-2 text-sm leading-6 text-slate-700">{{ $asset->description ?: '-' }}</p>
                </div>
            </div>

            <div class="space-y-4">
                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4 text-center">
                    @if ($scanCode)
                        @include('partials.qr', ['data' => route('assets.scan', $scanCode), 'size' => 180, 'class' => 'mx-auto inline-block rounded bg-white p-2 [&_svg]:h-44 [&_svg]:w-44'])
                        <p class="mt-3 text-xs text-slate-500">Scan untuk membuka halaman aset.</p>
                    @else
                        <div class="flex h-44 items-center justify-center rounded bg-white p-4 text-sm text-slate-500">QR Code belum tersedia.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if (auth()->user()->hasRole(['admin', 'pengelola']))
        <section class="mt-6 rounded-lg border border-blue-100 bg-white p-5 shadow-sm">
            <div>
                <h3 class="font-semibold text-slate-900">Tambah Kuantitas</h3>
                <p class="mt-1 text-sm text-slate-500">Tambahkan barang baru ke aset ini tanpa mengubah riwayat stock opname sebelumnya.</p>
            </div>
            <form
                method="POST"
                action="{{ route('assets.quantity-additions.store', $asset) }}"
                class="mt-5 space-y-4"
                x-data="{
                    quantity: {{ (int) old('quantity', 1) }},
                    baik: {{ (int) old('jumlah_baik', 1) }},
                    sedang: {{ (int) old('jumlah_sedang', 0) }},
                    rusak: {{ (int) old('jumlah_rusak', 0) }},
                    hilang: {{ (int) old('jumlah_hilang', 0) }},
                    get total() { return Number(this.baik) + Number(this.sedang) + Number(this.rusak) + Number(this.hilang) }
                }"
            >
                @csrf
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Kuantitas Tambahan</label>
                        <input type="number" name="quantity" x-model.number="quantity" min="1" required class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Tanggal Penambahan</label>
                        <input type="date" name="addition_date" value="{{ old('addition_date', now()->toDateString()) }}" required class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    </div>
                    <div class="md:col-span-2 xl:col-span-1">
                        <label class="block text-sm font-medium text-slate-700">Catatan</label>
                        <input name="notes" value="{{ old('notes') }}" placeholder="Contoh: pengadaan tahap kedua" class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-sm font-medium text-slate-700">Kondisi Barang Tambahan</p>
                        <p class="text-xs font-semibold" :class="total === quantity ? 'text-emerald-600' : 'text-red-600'">Total: <span x-text="total"></span> / <span x-text="quantity"></span></p>
                    </div>
                    <div class="mt-3 grid gap-4 grid-cols-2 lg:grid-cols-4">
                        @foreach (\App\Models\Asset::KONDISI_ASET_LABELS as $condition => $label)
                            <div>
                                <label class="block text-xs font-medium uppercase tracking-wide text-slate-500">{{ $label }}</label>
                                <input type="number" name="jumlah_{{ $condition }}" x-model.number="{{ $condition }}" min="0" required class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                            </div>
                        @endforeach
                    </div>
                </div>
                @error('quantity')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
                <div class="flex justify-end">
                    <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white hover:bg-blue-700">Simpan Penambahan</button>
                </div>
            </form>
        </section>
    @endif

    <section class="mt-6 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <h3 class="font-semibold">Riwayat Penambahan Kuantitas</h3>
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-slate-600">
                    <tr><th class="px-4 py-3">Tanggal</th><th class="px-4 py-3">Tambahan</th><th class="px-4 py-3">Pembagian Kondisi</th><th class="px-4 py-3">Dicatat Oleh</th><th class="px-4 py-3">Catatan</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($asset->quantityAdditions->sortByDesc('id') as $addition)
                        @php
                            $parts = collect(\App\Models\Asset::KONDISI_ASET_LABELS)
                                ->map(fn ($label, $condition) => ($addition->{'jumlah_'.$condition} ?? 0) > 0 ? number_format($addition->{'jumlah_'.$condition}).' '.$label : null)
                                ->filter()->implode(', ');
                        @endphp
                        <tr>
                            <td class="px-4 py-3">{{ $addition->addition_date->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 font-semibold">+{{ number_format($addition->quantity) }} {{ $asset->satuan ?: 'unit' }}</td>
                            <td class="px-4 py-3">{{ $parts }}</td>
                            <td class="px-4 py-3">{{ $addition->creator?->name ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $addition->notes ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-6 text-center text-slate-500">Belum ada riwayat penambahan kuantitas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="mt-6 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <h3 class="font-semibold">Riwayat Stock Opname</h3>
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-slate-600">
                    <tr><th class="px-4 py-3">Sesi</th><th class="px-4 py-3">Jumlah Sistem</th><th class="px-4 py-3">Jumlah Aktual</th><th class="px-4 py-3">Baik</th><th class="px-4 py-3">Sedang</th><th class="px-4 py-3">Rusak</th><th class="px-4 py-3">Hilang</th><th class="px-4 py-3">Hasil</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($asset->inventoryCheckItems->sortByDesc('id')->take(10) as $item)
                        <tr>
                            <td class="px-4 py-3">{{ $item->inventoryCheck?->periode ?: $item->inventoryCheck?->check_code }}</td>
                            <td class="px-4 py-3">{{ number_format($item->jumlah_sistem) }}</td>
                            <td class="px-4 py-3">{{ $item->jumlah_aktual !== null ? number_format($item->jumlah_aktual) : '-' }}</td>
                            <td class="px-4 py-3">{{ number_format($item->jumlah_baik ?? 0) }}</td>
                            <td class="px-4 py-3">{{ number_format($item->jumlah_sedang ?? 0) }}</td>
                            <td class="px-4 py-3">{{ number_format($item->jumlah_rusak ?? 0) }}</td>
                            <td class="px-4 py-3">{{ number_format($item->jumlah_hilang ?? 0) }}</td>
                            <td class="px-4 py-3">{{ str_replace('_', ' ', $item->hasil_pemeriksaan) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-4 py-6 text-center text-slate-500">Belum ada riwayat stock opname.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
