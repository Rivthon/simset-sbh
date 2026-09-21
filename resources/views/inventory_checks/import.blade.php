@extends('layouts.dashboard')

@section('title', 'Import Hasil Stock Opname')

@section('content')
    @if (session('import_errors'))
        <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <p class="font-semibold">Import dibatalkan. Perbaiki data berikut dulu.</p>
            <ul class="mt-2 max-h-64 list-inside list-disc overflow-y-auto">
                @foreach (session('import_errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="mb-5 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <p class="text-sm font-semibold text-[#465fff]">{{ $check->check_code }}</p>
        <h2 class="mt-1 text-2xl font-bold">{{ $check->unit?->name }}</h2>
        <p class="mt-2 text-sm text-gray-500">{{ $check->periode ?: $check->semester.' '.$check->tahun_akademik }} | {{ $check->tanggal_pemeriksaan?->format('d/m/Y') }} | Lokasi: {{ $locationContext['label'] }}</p>
    </section>

    <form method="POST" action="{{ route('inventory-checks.import.store', $locationContext['filtered'] ? ['inventoryCheck' => $check, 'location_id' => $locationContext['id']] : $check) }}" enctype="multipart/form-data" class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        @csrf
        @if ($locationContext['filtered'])
            <input type="hidden" name="location_id" value="{{ $locationContext['id'] }}">
        @endif
        <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-base font-semibold text-gray-900">Upload hasil pemeriksaan</h2>
                <p class="mt-1 text-sm text-gray-500">Data akan masuk ke detail sesi stock opname ini, bukan langsung menimpa master aset.</p>
            </div>
            <a href="{{ route('inventory-checks.import.template', $locationContext['filtered'] ? ['inventoryCheck' => $check, 'location_id' => $locationContext['id']] : $check) }}" class="inline-flex justify-center rounded-lg border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-100">Download Template</a>
        </div>

        <label class="block text-sm font-medium text-gray-700">File Excel</label>
        <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="mt-2 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
        <p class="mt-2 text-xs text-gray-500">Kolom: kode_aset, jumlah_aktual, jumlah_baik, jumlah_sedang, jumlah_rusak, jumlah_hilang.</p>

        <div class="mt-6 flex flex-wrap gap-3">
            <button class="rounded-lg bg-[#465fff] px-4 py-2 text-sm font-semibold text-white">Import Hasil</button>
            <a href="{{ route('inventory-checks.show', $check) }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700">Batal</a>
        </div>
    </form>
@endsection
