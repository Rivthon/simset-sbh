@extends('layouts.dashboard')

@section('title', 'Detail Penyimpanan')

@section('content')
    @php
        $identificationLabels = [
            'individual' => 'QR Individual',
            'group' => 'QR Kelompok',
        ];
        $canManageAssets = auth()->user()->hasRole(['admin', 'pengelola']);
    @endphp

    <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-sm font-medium text-blue-600">{{ $container->code }}</p>
                <h2 class="mt-1 text-2xl font-bold">{{ $container->name }}</h2>
                <p class="mt-2 text-sm text-slate-600">{{ $container->unit?->name ?? $container->location?->unit?->name ?? '-' }} | {{ $container->location?->name ?? '-' }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                @if ($container->qr_code)
                    <a href="{{ route('containers.qr', $container) }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cetak QR Penyimpanan</a>
                @elseif (auth()->user()->hasRole(['admin', 'pengelola']))
                    <form method="POST" action="{{ route('containers.qr.generate', $container) }}">
                        @csrf
                        <button class="rounded-md border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-100">Generate QR Penyimpanan</button>
                    </form>
                @endif
                @if (auth()->user()->hasRole(['admin', 'pengelola']))
                    <a href="{{ route('containers.edit', $container) }}" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Edit Penyimpanan</a>
                @endif
            </div>
        </div>

        <div class="mt-6 grid gap-6 lg:grid-cols-[1fr_240px]">
            <dl class="grid gap-4 md:grid-cols-2">
                @foreach ([
                    ['Kode Penyimpanan', $container->code],
                    ['Nama Penyimpanan', $container->name],
                    ['Unit/Laboratorium', $container->unit?->name ?? $container->location?->unit?->name],
                    ['Lokasi', $container->location?->name],
                    ['QR Code', $container->qr_code],
                    ['Jumlah Aset', number_format($container->assets->count())],
                    ['Status', $container->status === 'active' ? 'Aktif' : 'Nonaktif'],
                ] as [$label, $value])
                    <div class="rounded-md bg-slate-50 p-4">
                        <dt class="text-xs uppercase tracking-wide text-slate-500">{{ $label }}</dt>
                        <dd class="mt-1 text-sm font-semibold">{{ $value ?: '-' }}</dd>
                    </div>
                @endforeach
            </dl>

            <div class="rounded-lg border border-slate-200 bg-slate-50 p-4 text-center">
                @if ($qrData)
                    @include('partials.qr', ['data' => $qrData, 'size' => 180, 'class' => 'mx-auto inline-block rounded bg-white p-2 [&_svg]:h-44 [&_svg]:w-44'])
                    <p class="mt-3 text-xs text-slate-500">QR Code Penyimpanan</p>
                @else
                    <div class="flex h-44 items-center justify-center rounded bg-white p-4 text-sm text-slate-500">QR Code belum tersedia.</div>
                @endif
            </div>
        </div>
    </div>

    @if (auth()->user()->hasRole(['admin', 'pengelola']))
        <section class="mt-6 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-1">
                <h3 class="font-semibold">Masukkan Aset ke Penyimpanan</h3>
                <p class="text-sm text-slate-500">Pilih aset yang sudah terdaftar. Lokasi aset akan mengikuti lokasi penyimpanan ini.</p>
            </div>
            <form method="POST" action="{{ route('containers.assets.store', $container) }}" class="mt-4 flex flex-col gap-3 lg:flex-row">
                @csrf
                <select name="asset_id" required class="min-w-0 flex-1 rounded-md border border-slate-300 px-3 py-2 text-sm">
                    <option value="">Pilih aset yang sudah ada</option>
                    @foreach ($assignableAssets as $assetOption)
                        <option value="{{ $assetOption->id }}" @selected(old('asset_id') == $assetOption->id)>
                            {{ $assetOption->asset_code }} - {{ $assetOption->name }}{{ $assetOption->container ? ' (sekarang: '.$assetOption->container->name.')' : '' }}
                        </option>
                    @endforeach
                </select>
                <button class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Masukkan Aset</button>
            </form>
            @if ($assignableAssets->isEmpty())
                <p class="mt-3 text-sm text-slate-500">Tidak ada aset lain yang bisa dimasukkan ke penyimpanan ini.</p>
            @endif
        </section>
    @endif

    <section class="mt-6 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <h3 class="font-semibold">Daftar Aset Dalam Penyimpanan</h3>
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-slate-600">
                    <tr>
                        <th class="px-4 py-3">Kode Aset</th>
                        <th class="px-4 py-3">Nama Aset</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3">Jumlah</th>
                        <th class="px-4 py-3">Satuan</th>
                        <th class="px-4 py-3">Cara Identifikasi</th>
                        <th class="px-4 py-3">Kondisi Aset</th>
                        @if ($canManageAssets)
                            <th class="px-4 py-3">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($container->assets as $asset)
                        <tr>
                            <td class="px-4 py-3 font-medium">{{ $asset->asset_code }}</td>
                            <td class="px-4 py-3">{{ $asset->name }}</td>
                            <td class="px-4 py-3">{{ $asset->category?->name ?? '-' }}</td>
                            <td class="px-4 py-3">{{ number_format($asset->quantity ?? 1) }}</td>
                            <td class="px-4 py-3">{{ $asset->satuan ?: 'unit' }}</td>
                            <td class="px-4 py-3">{{ $identificationLabels[$asset->identification_type] ?? $asset->identificationLabel() }}</td>
                            <td class="px-4 py-3">{{ $asset->kondisi_aset_label }}</td>
                            @if ($canManageAssets)
                                <td class="px-4 py-3">
                                    <form method="POST" action="{{ route('containers.assets.destroy', [$container, $asset]) }}" onsubmit="return confirm('Keluarkan aset ini dari penyimpanan? Lokasi aset tetap dipertahankan.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-md border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700 hover:bg-amber-100">Keluarkan</button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr><td colspan="{{ $canManageAssets ? 8 : 7 }}" class="px-4 py-6 text-center text-slate-500">Belum ada aset dalam penyimpanan ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
