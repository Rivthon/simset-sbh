@extends('layouts.dashboard')

@section('title', 'Pemeriksaan Inventaris')

@section('content')
    <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm text-gray-600">Sesi pemeriksaan inventaris aset per unit/laboratorium, dengan pengerjaan per lokasi.</p>
        @if (auth()->user()->hasRole(['admin', 'pengelola']))
            <a href="{{ route('inventory-checks.create') }}" class="inline-flex w-full justify-center rounded-lg bg-[#465fff] px-4 py-2 text-sm font-semibold text-white sm:w-auto">Buat Sesi Pemeriksaan</a>
        @endif
    </div>

    <div class="overflow-x-auto rounded-2xl border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-gray-600">
                <tr><th class="px-4 py-3">Kode Sesi</th><th class="px-4 py-3">Unit/Laboratorium</th><th class="px-4 py-3">Periode</th><th class="px-4 py-3">Semester</th><th class="px-4 py-3">Tanggal</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Aksi</th></tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($checks as $check)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $check->check_code }}</td>
                        <td class="px-4 py-3">{{ $check->unit?->name }}</td>
                        <td class="px-4 py-3">{{ $check->periode ?: $check->semester.' '.$check->tahun_akademik }}</td>
                        <td class="px-4 py-3">{{ $check->semester ?: '-' }}</td>
                        <td class="px-4 py-3">{{ $check->tanggal_pemeriksaan?->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">@include('partials.badge', ['value' => $check->status])</td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('inventory-checks.show', $check) }}" class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-700">Detail</a>
                                @if (auth()->user()->hasRole(['admin', 'pengelola']) && $check->status !== 'completed')
                                    <a href="{{ route('inventory-checks.scan.form', $check) }}" class="rounded-lg bg-[#465fff] px-3 py-1.5 text-xs font-semibold text-white">Input/Scan</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-6 text-center text-gray-500">Belum ada sesi pemeriksaan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-5">{{ $checks->links() }}</div>
@endsection
