@extends('layouts.dashboard')

@section('title', 'Import Aset Excel')

@section('content')
    @if (session('import_errors'))
        <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <p class="font-semibold">Hasil validasi import.</p>
            <ul class="mt-2 max-h-64 list-inside list-disc overflow-y-auto">
                @foreach (session('import_errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_360px]">
        <form method="POST" action="{{ route('assets.import.store') }}" enctype="multipart/form-data" class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Upload template aset</h2>
                    <p class="mt-1 text-sm text-slate-500">Gunakan template final SIMASET SBH untuk memasukkan data aset awal.</p>
                </div>
                <a href="{{ route('assets.import.template') }}" class="inline-flex justify-center rounded-md border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-100">Download Template</a>
            </div>

            @if (auth()->user()->isAdmin())
                <label class="block text-sm font-medium text-slate-700">Unit/Laboran</label>
                <select name="unit_id" required class="mt-2 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                    <option value="">Pilih unit/laboran</option>
                    @foreach ($units as $unit)
                        <option value="{{ $unit->id }}" @selected((int) old('unit_id') === $unit->id)>{{ $unit->name }}</option>
                    @endforeach
                </select>
                <p class="mt-2 text-xs text-slate-500">Unit ini akan dipakai untuk semua baris pada file Excel.</p>
            @else
                <input type="hidden" name="unit_id" value="{{ auth()->user()->unit_id }}">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Unit/Laboran</label>
                    <div class="mt-2 rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-600">{{ auth()->user()->unit?->name }}</div>
                </div>
            @endif

            <label class="mt-5 block text-sm font-medium text-slate-700">File Excel</label>
            <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="mt-2 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
            <p class="mt-2 text-xs text-slate-500">Format yang diterima: XLSX, XLS, CSV. Maksimal 5 MB.</p>
            <p class="mt-2 text-xs leading-5 text-slate-500">Isi kolom jumlah_baik, jumlah_sedang, jumlah_rusak, dan jumlah_hilang. Total keempatnya wajib sama dengan kolom jumlah.</p>

            <div class="mt-6 flex gap-3">
                <button class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Import Aset</button>
                <a href="{{ route('assets.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700">Batal</a>
            </div>
        </form>

        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-base font-semibold text-slate-900">Referensi import</h2>
            <p class="mt-1 text-sm text-slate-500">Kategori, lokasi, dan penyimpanan memakai nama sesuai master data.</p>

            <div class="mt-4 space-y-4 text-sm">
                <div>
                    <p class="font-semibold text-slate-700">Unit</p>
                    <div class="mt-2 max-h-32 overflow-y-auto rounded-md border border-slate-200">
                        @foreach ($units as $unit)
                            <div class="border-b border-slate-100 px-3 py-2 last:border-0">{{ $unit->name }}</div>
                        @endforeach
                    </div>
                </div>
                <div>
                    <p class="font-semibold text-slate-700">Kategori</p>
                    <div class="mt-2 max-h-32 overflow-y-auto rounded-md border border-slate-200">
                        @foreach ($categories as $category)
                            <div class="border-b border-slate-100 px-3 py-2 last:border-0">{{ $category->name }}</div>
                        @endforeach
                    </div>
                </div>
                <div>
                    <p class="font-semibold text-slate-700">Lokasi</p>
                    <div class="mt-2 max-h-32 overflow-y-auto rounded-md border border-slate-200">
                        @foreach ($locations as $location)
                            <div class="border-b border-slate-100 px-3 py-2 last:border-0">{{ $location->name }} - {{ $location->unit?->name }}</div>
                        @endforeach
                    </div>
                </div>
                <div>
                    <p class="font-semibold text-slate-700">Penyimpanan</p>
                    <div class="mt-2 max-h-32 overflow-y-auto rounded-md border border-slate-200">
                        @forelse ($containers as $container)
                            <div class="border-b border-slate-100 px-3 py-2 last:border-0">{{ $container->name }} - {{ $container->location?->name }}</div>
                        @empty
                            <div class="px-3 py-2 text-slate-500">Belum ada data.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
