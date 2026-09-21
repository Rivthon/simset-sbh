@extends('layouts.dashboard')

@section('title', 'Data Penyimpanan')

@section('content')
    <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm text-slate-600">Rak, lemari, box, laci, atau penyimpanan aset laboratorium.</p>
        @if (auth()->user()->hasRole(['admin', 'pengelola']))
            <a href="{{ route('containers.create') }}" class="inline-flex w-full justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 sm:w-auto">Tambah Penyimpanan</a>
        @endif
    </div>

    <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-slate-600">
                <tr>
                    <th class="px-4 py-3">Kode Penyimpanan</th>
                    <th class="px-4 py-3">Nama Penyimpanan</th>
                    <th class="px-4 py-3">Unit/Laboratorium</th>
                    <th class="px-4 py-3">Lokasi</th>
                    <th class="px-4 py-3">Jumlah Aset</th>
                    <th class="px-4 py-3">QR Code</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($containers as $container)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $container->code }}</td>
                        <td class="px-4 py-3">{{ $container->name }}</td>
                        <td class="px-4 py-3">{{ $container->unit?->name ?? $container->location?->unit?->name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $container->location?->name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ number_format($container->assets_count) }}</td>
                        <td class="px-4 py-3">{{ $container->qr_code ?: '-' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('containers.show', $container) }}" class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">Detail</a>
                                @if ($container->qr_code)
                                    <a href="{{ route('containers.qr', $container) }}" class="rounded-md border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-medium text-blue-700 hover:bg-blue-100">Cetak QR Penyimpanan</a>
                                @endif
                                @if (auth()->user()->hasRole(['admin', 'pengelola']))
                                    @unless ($container->qr_code)
                                        <form method="POST" action="{{ route('containers.qr.generate', $container) }}">
                                            @csrf
                                            <button type="submit" class="rounded-md border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-medium text-blue-700 hover:bg-blue-100">Generate QR Penyimpanan</button>
                                        </form>
                                    @endunless
                                    <a href="{{ route('containers.edit', $container) }}" class="rounded-md border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-medium text-blue-700 hover:bg-blue-100">Edit</a>
                                    <form method="POST" action="{{ route('containers.destroy', $container) }}" onsubmit="return confirm('Hapus penyimpanan ini? Pastikan tidak ada aset yang masih memakai penyimpanan ini.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-md border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-100">Hapus</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-6 text-center text-slate-500">Belum ada data penyimpanan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-5">{{ $containers->links() }}</div>
@endsection
