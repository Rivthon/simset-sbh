@extends('layouts.dashboard')

@section('title', 'Buat Sesi Pemeriksaan')

@section('content')
    <form method="POST" action="{{ route('inventory-checks.store') }}" class="max-w-2xl rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        @csrf
        <div class="grid gap-5 md:grid-cols-2">
            @if (auth()->user()->isPengelola())
                <input type="hidden" name="unit_id" value="{{ auth()->user()->unit_id }}">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Unit/Laboratorium</label>
                    <div class="mt-2 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-600">{{ auth()->user()->unit?->name ?? '-' }}</div>
                </div>
            @else
                <div>
                    <label class="block text-sm font-medium text-gray-700">Unit/Laboratorium</label>
                    <select name="unit_id" required class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <option value="">Pilih unit/laboratorium</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id }}" @selected(old('unit_id') == $unit->id)>{{ $unit->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700">Semester</label>
                <select name="semester" required class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <option value="Ganjil" @selected(old('semester') === 'Ganjil')>Ganjil</option>
                    <option value="Genap" @selected(old('semester') === 'Genap')>Genap</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Tahun Akademik</label>
                <input name="tahun_akademik" value="{{ old('tahun_akademik', now()->year.'/'.now()->addYear()->year) }}" required class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Tanggal Pemeriksaan</label>
                <input type="date" name="tanggal_pemeriksaan" value="{{ old('tanggal_pemeriksaan', now()->format('Y-m-d')) }}" required class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
            </div>
        </div>
        <div class="mt-5">
            <label class="block text-sm font-medium text-gray-700">Catatan</label>
            <textarea name="notes" rows="3" class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">{{ old('notes') }}</textarea>
        </div>
        <div class="mt-6 flex gap-3">
            <button class="rounded-lg bg-[#465fff] px-4 py-2 text-sm font-semibold text-white">Buat Sesi</button>
            <a href="{{ route('inventory-checks.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700">Batal</a>
        </div>
    </form>
@endsection
