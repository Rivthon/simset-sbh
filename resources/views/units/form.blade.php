@extends('layouts.dashboard')

@section('title', $unit->exists ? 'Edit Unit' : 'Tambah Unit')

@section('content')
    <form method="POST" action="{{ $unit->exists ? route('units.update', $unit) : 
    route('units.store') }}" 
        class="max-w-2xl rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        @csrf
        @if ($unit->exists)
            @method('PUT')
        @endif

        <div class="grid gap-5">
            <div>
                <label class="block text-sm font-medium text-slate-700">Nama Unit</label>
                <input name="name" value="{{ old('name', $unit->name) }}" 
                required class="mt-2 w-full 
                rounded-md border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Kode Unit</label>
                <div class="mt-2 rounded-md border border-slate-200 bg-slate-50 px-3 py-2 
                text-sm text-slate-600">
                    {{ $unit->exists ? $unit->code : 'Dibuat otomatis saat disimpan' }}
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Status</label>
                <select name="status" class="mt-2 w-full rounded-md border border-slate-300 
                px-3 py-2 text-sm">
                    <option value="active" @selected(old('status', $unit->status ?: 'active') 
                    === 'active')>Aktif</option>
                    <option value="inactive" @selected(old('status', $unit->status) 
                    === 'inactive')>Nonaktif</option>
                </select>
            </div>
        </div>

        <div class="mt-6 flex gap-3">
            <button class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white 
            hover:bg-blue-700">Simpan</button>
            <a href="{{ route('units.index') }}" class="rounded-md border border-slate-300 
            px-4 py-2 text-sm font-medium text-slate-700">Batal</a>
        </div>
    </form>
@endsection
