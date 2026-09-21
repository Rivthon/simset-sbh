@extends('layouts.dashboard')

@section('title', 'Penggantian Alat Rusak')

@section('content')
    <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-[#465fff]">Surat Penggantian Alat Rusak</p>
                <h2 class="mt-2 text-2xl font-bold text-gray-900">Penggantian Alat Rusak</h2>
            </div>
        </div>

        <form method="GET" class="mt-5 grid gap-3 md:grid-cols-2 xl:grid-cols-7">
            @if (auth()->user()->canFilterUnits())
                <select name="unit_id" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <option value="">Semua unit</option>
                    @foreach ($units as $unit)
                        <option value="{{ $unit->id }}" @selected(request('unit_id') == $unit->id)>{{ $unit->name }}</option>
                    @endforeach
                </select>
            @endif
            <select name="status" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                <option value="">Semua status</option>
                @foreach ($statusLabels as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <input type="date" name="incident_date" value="{{ request('incident_date') }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
            <input name="student_name" value="{{ request('student_name') }}" placeholder="Nama mahasiswa" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
            <input name="asset_name" value="{{ request('asset_name') }}" placeholder="Nama aset" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
            <input name="practicum_name" value="{{ request('practicum_name') }}" placeholder="Praktikum" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
            <div class="flex gap-2">
                <button class="flex-1 rounded-lg bg-[#465fff] px-4 py-2 text-sm font-semibold text-white">Filter</button>
                <a href="{{ route('tool-replacements.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700">Reset</a>
            </div>
        </form>
    </section>

    <section class="mt-5 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-[980px] divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-gray-600">
                    <tr>
                        <th class="px-4 py-3">Mahasiswa</th>
                        <th class="px-4 py-3">Aset</th>
                        <th class="px-4 py-3">Unit</th>
                        <th class="px-4 py-3">Praktikum</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Jumlah</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($replacements as $replacement)
                        <tr>
                            <td class="px-4 py-3">
                                <p class="font-semibold text-gray-900">{{ $replacement->student_name }}</p>
                                <p class="text-xs text-gray-500">NIM {{ $replacement->display_nim }} | Semester {{ $replacement->display_semester }}</p>
                                <p class="text-xs text-gray-400">{{ $replacement->replacement_code }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-900">{{ $replacement->asset?->name }}</p>
                                <p class="text-xs text-gray-500">{{ $replacement->asset?->asset_code }}</p>
                            </td>
                            <td class="px-4 py-3">{{ $replacement->unit?->name ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $replacement->practicum_name }}</td>
                            <td class="px-4 py-3">{{ $replacement->incident_date?->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">{{ number_format($replacement->replacement_quantity) }} {{ $replacement->asset?->satuan ?: 'unit' }}</td>
                            <td class="px-4 py-3">
                                @include('partials.badge', ['value' => $replacement->status])
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('tool-replacements.show', $replacement) }}" class="rounded-lg border border-[#465fff] px-3 py-1.5 text-xs font-semibold text-[#465fff]">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">Belum ada surat penggantian alat rusak.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-gray-200 px-4 py-3">
            {{ $replacements->links() }}
        </div>
    </section>
@endsection
