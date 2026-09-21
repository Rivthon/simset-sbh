@extends('layouts.dashboard')

@section('title', $container->exists ? 'Edit Penyimpanan' : 'Tambah Penyimpanan')

@section('content')
    <form method="POST" action="{{ $container->exists ? route('containers.update', $container) : route('containers.store') }}" class="max-w-2xl rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        @csrf
        @if ($container->exists)
            @method('PUT')
        @endif

        <div class="grid gap-5">
            @if (auth()->user()->isPengelola())
                <input type="hidden" name="unit_id" value="{{ auth()->user()->unit_id }}">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Unit/Laboratorium</label>
                    <div class="mt-2 rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-600">{{ auth()->user()->unit?->name ?? '-' }}</div>
                </div>
            @else
                <div>
                    <label class="block text-sm font-medium text-slate-700">Unit/Laboratorium</label>
                    <select name="unit_id" required class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                        <option value="">Pilih unit/laboratorium</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id }}" @selected((int) $selectedUnitId === $unit->id)>{{ $unit->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-slate-700">Lokasi</label>
                <select name="location_id" required class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                    <option value="">Pilih lokasi</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location->id }}" @selected((int) old('location_id', $container->location_id) === $location->id)>{{ $location->name }} - {{ $location->unit?->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Nama Penyimpanan</label>
                <input name="name" value="{{ old('name', $container->name) }}" required class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Kode Penyimpanan</label>
                <div class="mt-2 rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-600">{{ $container->exists ? $container->code : 'Dibuat otomatis saat disimpan' }}</div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">QR Code</label>
                <div class="mt-2 rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-600">{{ $container->qr_code ?: 'Dibuat otomatis saat disimpan' }}</div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Status</label>
                <select name="status" class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                    <option value="active" @selected(old('status', $container->status ?: 'active') === 'active')>Aktif</option>
                    <option value="inactive" @selected(old('status', $container->status) === 'inactive')>Nonaktif</option>
                </select>
            </div>
        </div>

        <div class="mt-6 flex gap-3">
            <button class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Simpan</button>
            <a href="{{ route('containers.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700">Batal</a>
        </div>
    </form>
@endsection
