@extends('layouts.dashboard')

@section('title', $category->exists ? 'Edit Kategori' : 'Tambah Kategori')

@section('content')
    <form method="POST" action="{{ $category->exists ? route('categories.update', $category) : route('categories.store') }}" class="max-w-2xl rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        @csrf
        @if ($category->exists)
            @method('PUT')
        @endif
        <div class="grid gap-5">
            <div><label class="block text-sm font-medium text-slate-700">Nama</label><input name="name" value="{{ old('name', $category->name) }}" required class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm"></div>
            @if (auth()->user()->isAdmin())
                <div><label class="block text-sm font-medium text-slate-700">Unit</label><select name="unit_id" class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm"><option value="">Global / semua unit</option>@foreach ($units as $unit)<option value="{{ $unit->id }}" @selected((int) old('unit_id', $category->unit_id) === $unit->id)>{{ $unit->name }}</option>@endforeach</select><p class="mt-1 text-xs text-slate-500">Kosongkan jika kategori boleh dipakai semua unit.</p></div>
            @else
                <input type="hidden" name="unit_id" value="{{ auth()->user()->unit_id }}">
                <div><label class="block text-sm font-medium text-slate-700">Unit</label><div class="mt-2 rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-600">{{ auth()->user()->unit?->name }}</div></div>
            @endif
            <div><label class="block text-sm font-medium text-slate-700">Status</label><select name="status" class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm"><option value="active" @selected(old('status', $category->status ?: 'active') === 'active')>Aktif</option><option value="inactive" @selected(old('status', $category->status) === 'inactive')>Nonaktif</option></select></div>
        </div>
        <div class="mt-6 flex gap-3"><button class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Simpan</button><a href="{{ route('categories.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700">Batal</a></div>
    </form>
@endsection
