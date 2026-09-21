@extends('layouts.dashboard')

@section('title', 'Data Aset')

@section('content')
    @php
        $conditionLabels = \App\Models\Asset::KONDISI_ASET_LABELS;
        $identificationLabels = [
            'individual' => 'QR Individual',
            'group' => 'QR Kelompok',
        ];
    @endphp

    <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-gray-600">Inventaris aset laboratorium sesuai unit dan penyimpanan.</p>
            <p class="mt-1 text-xs text-gray-500">Export Excel akan mengikuti filter yang sedang diterapkan.</p>
        </div>
        <div class="flex flex-col gap-2 sm:flex-row">
            <a href="{{ route('assets.export', request()->query()) }}" class="inline-flex w-full justify-center rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-100 sm:w-auto">Export Excel</a>
            @if (auth()->user()->hasRole(['admin', 'pengelola']))
                <a href="{{ route('assets.import') }}" class="inline-flex w-full justify-center rounded-lg border border-[#465fff] px-4 py-2 text-sm font-semibold text-[#465fff] hover:bg-blue-50 sm:w-auto">Import Excel</a>
                <a href="{{ route('assets.create') }}" class="inline-flex w-full justify-center rounded-lg bg-[#465fff] px-4 py-2 text-sm font-semibold text-white hover:bg-[#3641f5] sm:w-auto">Tambah Aset</a>
            @endif
        </div>
    </div>

    <form method="GET" class="mb-5 grid gap-3 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm md:grid-cols-4 xl:grid-cols-8">
        <input name="keyword" value="{{ request('keyword') }}" placeholder="Cari kode/nama/kode lama" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
        <select name="unit_id" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
            <option value="">Unit/Laboratorium</option>
            @foreach ($units as $unit)
                <option value="{{ $unit->id }}" @selected(request('unit_id') == $unit->id)>{{ $unit->name }}</option>
            @endforeach
        </select>
        <select name="category_id" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
            <option value="">Kategori</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        <select name="location_id" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
            <option value="">Lokasi</option>
            @foreach ($locations as $location)
                <option value="{{ $location->id }}" @selected(request('location_id') == $location->id)>{{ $location->name }}</option>
            @endforeach
        </select>
        <select name="container_id" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
            <option value="">Penyimpanan</option>
            @foreach ($containers as $container)
                <option value="{{ $container->id }}" @selected(request('container_id') == $container->id)>{{ $container->name }}</option>
            @endforeach
        </select>
        <select name="kondisi_aset" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
            <option value="">Kondisi Aset</option>
            @foreach ($conditionLabels as $value => $label)
                <option value="{{ $value }}" @selected(request('kondisi_aset') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="identification_type" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
            <option value="">Cara Identifikasi</option>
            @foreach ($identificationLabels as $value => $label)
                <option value="{{ $value }}" @selected(request('identification_type') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <button class="rounded-lg bg-[#465fff] px-4 py-2 text-sm font-semibold text-white hover:bg-[#3641f5]">Filter</button>
    </form>

    <div class="overflow-x-auto rounded-2xl border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-gray-600">
                <tr>
                    <th class="px-4 py-3">Kode Aset</th>
                    <th class="px-4 py-3">Nama Aset</th>
                    <th class="px-4 py-3">Keterangan</th>
                    <th class="px-4 py-3">Lokasi/Penyimpanan</th>
                    <th class="px-4 py-3">Kategori/Identifikasi</th>
                    <th class="px-4 py-3">Jumlah</th>
                    <th class="px-4 py-3">Kondisi</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($assets as $asset)
                    @php
                        $canDeleteAsset = auth()->user()->canDeleteAssetInUnit($asset->unit_id);
                    @endphp
                    <tr>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900">{{ $asset->asset_code }}</p>
                            <p class="mt-1 text-xs text-gray-500">Lama: {{ $asset->legacy_inventory_code ?: '-' }}</p>
                        </td>
                        <td class="px-4 py-3">{{ $asset->name }}</td>
                        <td class="px-4 py-3">{{ $asset->description ?: '-' }}</td>
                        <td class="px-4 py-3">
                            <p>{{ $asset->location?->name ?? '-' }}</p>
                            <p class="mt-1 text-xs text-gray-500">{{ $asset->container?->name ?? '-' }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <p>{{ $asset->category?->name ?? '-' }}</p>
                            <p class="mt-1 text-xs text-gray-500">{{ $asset->identificationLabel() }} - {{ $asset->identificationDescription() }}</p>
                        </td>
                        <td class="px-4 py-3">{{ number_format($asset->quantity ?? 1) }} {{ $asset->satuan ?: 'unit' }}</td>
                        <td class="px-4 py-3">{{ $asset->kondisi_aset_label }}</td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('assets.show', $asset) }}" class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-700">Detail</a>
                                @if (auth()->user()->hasRole(['admin', 'pengelola']))
                                    @if (in_array($asset->identification_type, ['individual', 'group'], true) && blank($asset->qr_code))
                                        <form method="POST" action="{{ route('assets.qr.generate', $asset) }}">
                                            @csrf
                                            <button type="submit" class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700">Generate QR Code</button>
                                        </form>
                                    @endif
                                    <a href="{{ route('assets.edit', $asset) }}" class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700">Edit</a>
                                @endif
                                @if ($canDeleteAsset)
                                    <form method="POST" action="{{ route('assets.destroy', $asset) }}" onsubmit="return confirm('Hapus data aset ini secara permanen? Data yang sudah memiliki riwayat transaksi tidak dapat dihapus.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100">Hapus Data</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-4 py-6 text-center text-gray-500">Belum ada aset.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-5">{{ $assets->links() }}</div>
@endsection
