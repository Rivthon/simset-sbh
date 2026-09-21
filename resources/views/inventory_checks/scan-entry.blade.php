@extends('layouts.dashboard')

@section('title', 'Scan QR Pemeriksaan Inventaris')

@section('content')
    <div x-data="{ scanMode: null }">
        <section class="mb-4 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm md:mb-5 md:p-6">
            <div class="md:hidden">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="truncate text-xs font-semibold text-[#465fff]">{{ $check->check_code }}</p>
                        <h2 class="mt-0.5 text-lg font-bold text-gray-900">Stock Opname</h2>
                        <p class="mt-1 truncate text-xs text-gray-500">{{ $locationContext['label'] }} · {{ $check->semester }} {{ $check->tahun_akademik }}</p>
                    </div>
                    @include('partials.badge', ['value' => $check->status])
                </div>

                <div class="mt-4">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-medium text-gray-600">{{ number_format($progress['checked']) }} dari {{ number_format($progress['total']) }} aset diperiksa</span>
                        <span class="font-bold text-[#465fff]">{{ $progress['percent'] }}%</span>
                    </div>
                    <div class="mt-2 h-2 overflow-hidden rounded-full bg-gray-100">
                        <div class="h-full rounded-full bg-[#465fff]" style="width: {{ min(100, max(0, $progress['percent'])) }}%"></div>
                    </div>
                    <div class="mt-2 flex items-center justify-between text-xs text-gray-500">
                        <span>{{ number_format($progress['unchecked']) }} belum dicek</span>
                        <span>{{ $check->tanggal_pemeriksaan?->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>

            <div class="hidden md:block">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-[#465fff]">{{ $check->check_code }}</p>
                        <h2 class="mt-1 text-2xl font-bold">Scan QR Pemeriksaan Inventaris</h2>
                        <div class="mt-2 grid gap-1 text-sm text-gray-500">
                            <p>Unit: <span class="font-medium text-gray-700">{{ $check->unit?->name }}</span></p>
                            <p>Lokasi: <span class="font-medium text-gray-700">{{ $locationContext['label'] }}</span></p>
                            <p>Semester: <span class="font-medium text-gray-700">{{ $check->semester }}</span></p>
                            <p>Tahun Akademik: <span class="font-medium text-gray-700">{{ $check->tahun_akademik }}</span></p>
                            <p>Tanggal: <span class="font-medium text-gray-700">{{ $check->tanggal_pemeriksaan?->format('d/m/Y') }}</span></p>
                        </div>
                    </div>
                    @include('partials.badge', ['value' => $check->status])
                </div>

                <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ([['Total Aset', $progress['total']], ['Sudah Dicek', $progress['checked']], ['Belum Dicek', $progress['unchecked']], ['Progress', $progress['percent'].'%']] as [$label, $value])
                        <div class="rounded-xl bg-gray-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ $label }}</p>
                            <p class="mt-2 text-2xl font-bold text-gray-900">{{ is_numeric($value) ? number_format($value) : $value }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <div class="mb-4 grid grid-cols-2 gap-2 md:hidden">
            <button type="button" @click="scanMode = scanMode === 'camera' ? null : 'camera'" :class="scanMode === 'camera' ? 'border-[#465fff] bg-blue-50 text-[#465fff]' : 'border-gray-200 bg-white text-gray-700'" class="rounded-xl border px-3 py-3 text-sm font-semibold shadow-sm">Scan QR</button>
            <button type="button" @click="scanMode = scanMode === 'manual' ? null : 'manual'; if (scanMode === 'manual') $nextTick(() => $refs.scanInput.focus())" :class="scanMode === 'manual' ? 'border-[#465fff] bg-blue-50 text-[#465fff]' : 'border-gray-200 bg-white text-gray-700'" class="rounded-xl border px-3 py-3 text-sm font-semibold shadow-sm">Input Kode</button>
        </div>

        <section :class="scanMode === 'camera' ? 'block' : 'hidden md:block'" class="mb-4 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm print:hidden md:mb-5 md:p-6" data-qr-scanner-root>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="hidden md:block">
                    <h2 class="text-base font-semibold text-gray-900">Scan QR Pemeriksaan Inventaris</h2>
                    <p class="mt-1 text-sm text-gray-500">Gunakan kamera untuk QR aset atau QR Penyimpanan pada lokasi pemeriksaan aktif. Input manual kode aset/kode penyimpanan tetap tersedia.</p>
                </div>
                <div class="grid grid-cols-2 gap-2 sm:flex sm:flex-wrap">
                    <button type="button" data-start-camera class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-2.5 text-sm font-semibold text-blue-700 md:px-4 md:py-2">Aktifkan Kamera</button>
                    <button type="button" data-scan-image-button class="rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm font-semibold text-gray-700 md:px-4 md:py-2">Scan dari Foto</button>
                    <input type="file" accept="image/*" data-scan-image-input class="hidden">
                </div>
            </div>
            <div class="mx-auto mt-3 hidden w-full max-w-[420px] overflow-hidden rounded-xl border border-gray-200 bg-black aspect-[3/4] md:mt-4" data-camera-wrap>
                <div data-camera-reader class="h-full w-full"></div>
            </div>
            <p data-camera-status class="mt-3 text-xs text-gray-500 md:text-sm">Kamera belum aktif</p>
            @include('partials.qr-scanner-script')
        </section>

        <form method="POST" action="{{ route('inventory-checks.scan.process', $locationContext['filtered'] ? ['inventoryCheck' => $check, 'location_id' => $locationContext['id']] : $check) }}" :class="scanMode === 'manual' ? 'block' : 'hidden md:block'" class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm md:p-6" data-scan-form>
            @csrf
            @if ($locationContext['filtered'])
                <input type="hidden" name="location_id" value="{{ $locationContext['id'] }}">
            @endif
            <label class="block text-sm font-medium text-gray-700">Kode QR / Kode Aset / Kode Penyimpanan</label>
            <div class="mt-2 flex flex-col gap-3 sm:flex-row">
                <input x-ref="scanInput" name="scan_code" value="{{ old('scan_code') }}" required placeholder="Scan atau ketik kode" class="min-w-0 flex-1 rounded-lg border border-gray-300 px-3 py-2.5 text-sm md:py-2" data-scan-input>
                <button class="rounded-lg bg-[#465fff] px-4 py-2.5 text-sm font-semibold text-white md:py-2">Cari Data</button>
            </div>
            @error('scan_code')
                <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>
            @enderror
        </form>

        <div class="mt-4 flex flex-wrap gap-2 md:mt-5 md:gap-3">
            <a href="{{ route('inventory-checks.show', $check) }}" class="rounded-lg border border-gray-300 px-3 py-2 text-xs font-semibold text-gray-700 md:px-4 md:text-sm">Lihat Detail Pemeriksaan</a>
            @if ($locationContext['filtered'])
                <a href="{{ route('inventory-checks.scan.form', $check) }}" class="rounded-lg border border-gray-300 px-3 py-2 text-xs font-semibold text-gray-700 md:px-4 md:text-sm">Scan Semua Lokasi</a>
            @endif
        </div>
    </div>
@endsection
