@extends('layouts.dashboard')

@section('title', 'Scan QR Code')

@section('content')
    <section class="mb-5 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm print:hidden" data-qr-scanner-root>
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-base font-semibold text-gray-900">Kamera Scanner</h2>
                <p class="mt-1 text-sm text-gray-500">Scan QR aset atau QR Penyimpanan. Input manual tetap tersedia.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="button" data-start-camera class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-700">Aktifkan Kamera</button>
                <button type="button" data-scan-image-button class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700">Scan dari Foto</button>
                <input type="file" accept="image/*" data-scan-image-input class="hidden">
            </div>
        </div>
        <div class="mx-auto mt-4 hidden w-full max-w-[420px] overflow-hidden rounded-xl border border-gray-200 bg-black aspect-[3/4]" data-camera-wrap>
            <div data-camera-reader class="h-full w-full"></div>
        </div>
        <p data-camera-status class="mt-3 text-sm text-gray-500">Kamera belum aktif</p>
        @include('partials.qr-scanner-script')
    </section>

    <form method="POST" action="{{ route('scan.qr.process') }}" class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm" data-scan-form>
        @csrf
        <label class="block text-sm font-medium text-gray-700">Kode QR / Kode Aset / Kode Penyimpanan</label>
        <div class="mt-2 flex flex-col gap-3 sm:flex-row">
            <input name="scan_code" value="{{ old('scan_code') }}" autofocus required placeholder="Scan atau ketik kode" class="min-w-0 flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm" data-scan-input>
            <button class="rounded-lg bg-[#465fff] px-4 py-2 text-sm font-semibold text-white">Cari Data</button>
        </div>
    </form>

@endsection
