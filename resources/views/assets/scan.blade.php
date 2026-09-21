<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan QR Code - SIMASET SBH</title>
    @include('partials.assets')
</head>
<body class="bg-gray-50 text-gray-900">
    @php
    @endphp

    <main class="mx-auto min-h-screen max-w-5xl px-4 py-6 sm:px-5 sm:py-10">
        <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-[#465fff]">SIMASET SBH</p>
                <h1 class="mt-1 text-2xl font-bold">Hasil Scan QR</h1>
            </div>
            @auth
                <a href="{{ route('dashboard') }}" class="inline-flex justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-white">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="inline-flex justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-white">Login Petugas</a>
            @endauth
        </div>

        @if ($type === 'asset')
            <section class="w-full rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-wide text-[#465fff]">{{ $asset->identificationLabel() }}</p>
                        <p class="mt-1 text-xs text-gray-500">{{ $asset->identificationDescription() }}</p>
                        <h2 class="mt-3 text-2xl font-bold">{{ $asset->name }}</h2>
                        <p class="mt-2 text-sm text-gray-500">{{ $asset->asset_code }}</p>
                    </div>
                    <a href="{{ route('tool-replacements.public.create', $asset) }}" class="inline-flex justify-center rounded-lg bg-[#465fff] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#3641f5]">
                        Isi Surat Penggantian Alat
                    </a>
                </div>

                <dl class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ([
                        ['Kode Aset', $asset->asset_code],
                        ['Kode Inventaris Lama', $asset->legacy_inventory_code],
                        ['Nama Aset', $asset->name],
                        ['Unit', $asset->unit?->name],
                        ['Lokasi', $asset->location?->name],
                        ['Tempat Penyimpanan', $asset->container?->name],
                        ['Jumlah', number_format($asset->quantity ?? 1).' '.($asset->satuan ?: 'unit')],
                        ['Kondisi', $asset->kondisi_aset_label],
                    ] as [$label, $value])
                        <div class="rounded-xl bg-gray-50 p-4">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ $label }}</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $value ?: '-' }}</dd>
                        </div>
                    @endforeach
                </dl>

                @if ($canManage)
                    <div class="mt-5 flex flex-wrap gap-3 border-t border-gray-200 pt-5">
                        <a href="{{ route('assets.show', $asset) }}" class="rounded-lg border border-[#465fff] px-4 py-2 text-sm font-semibold text-[#465fff] hover:bg-blue-50">Detail Internal</a>
                        <a href="{{ route('tool-replacements.index', ['asset_name' => $asset->name]) }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Lihat Surat Masuk</a>
                    </div>
                @endif
            </section>
        @else
            <section class="w-full rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-wide text-[#465fff]">QR Penyimpanan</p>
                        <h2 class="mt-3 text-2xl font-bold">{{ $container->name }}</h2>
                        <p class="mt-2 text-sm text-gray-500">{{ $container->code }} | {{ $container->location?->name }} | {{ $container->unit?->name ?? $container->location?->unit?->name }}</p>
                    </div>
                    @if ($canManage)
                        <a href="{{ route('containers.show', $container) }}" class="inline-flex justify-center rounded-lg border border-[#465fff] px-4 py-2.5 text-sm font-semibold text-[#465fff] hover:bg-blue-50">Detail Internal</a>
                    @endif
                </div>

                <form method="GET" class="mt-6 flex flex-col gap-3 sm:flex-row">
                    <input name="keyword" value="{{ request('keyword') }}" placeholder="Cari nama atau kode aset" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <button class="rounded-lg bg-[#465fff] px-4 py-2 text-sm font-semibold text-white">Cari</button>
                    @if (request()->filled('keyword'))
                        <a href="{{ url()->current() }}" class="rounded-lg border border-gray-300 px-4 py-2 text-center text-sm font-semibold text-gray-700">Reset</a>
                    @endif
                </form>

                <div class="mt-5 overflow-x-auto rounded-xl border border-gray-200">
                    <table class="min-w-[760px] divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-left text-gray-600">
                            <tr>
                                <th class="px-4 py-3">Kode Aset</th>
                                <th class="px-4 py-3">Kode Lama</th>
                                <th class="px-4 py-3">Nama Aset</th>
                                <th class="px-4 py-3">Jumlah</th>
                                <th class="px-4 py-3">Kategori</th>
                                <th class="px-4 py-3">Kondisi</th>
                                <th class="px-4 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($containerAssets as $item)
                                <tr>
                                    <td class="px-4 py-3 font-medium">{{ $item->asset_code }}</td>
                                    <td class="px-4 py-3">{{ $item->legacy_inventory_code ?: '-' }}</td>
                                    <td class="px-4 py-3">{{ $item->name }}</td>
                                    <td class="px-4 py-3">{{ number_format($item->quantity ?? 1) }} {{ $item->satuan ?: 'unit' }}</td>
                                    <td class="px-4 py-3">{{ $item->category?->name ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $item->kondisi_aset_label }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-wrap gap-2">
                                            <a href="{{ route('tool-replacements.public.create', $item) }}" class="inline-flex rounded-lg bg-[#465fff] px-3 py-1.5 text-xs font-semibold text-white hover:bg-[#3641f5]">Isi Penggantian</a>
                                            @if ($canMonitor)
                                                <a href="{{ route('assets.show', $item) }}" class="inline-flex rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50">Detail</a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-4 py-6 text-center text-gray-500">Belum ada aset di tempat penyimpanan ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-5">
                    {{ $containerAssets?->links() }}
                </div>
            </section>
        @endif
    </main>
</body>
</html>
