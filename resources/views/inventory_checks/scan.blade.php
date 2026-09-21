@extends('layouts.dashboard')

@section('title', $type === 'asset' ? 'Form Pemeriksaan Aset' : 'Pilih Aset Penyimpanan')

@section('content')
    <section class="mb-4 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm md:mb-6 md:p-6">
        <p class="text-xs font-semibold text-[#465fff] md:text-sm">{{ $check->check_code }}</p>

        @if ($type === 'asset')
            <div class="md:hidden">
                <div class="mt-1 flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h2 class="truncate text-lg font-bold text-gray-900">{{ $asset->name }}</h2>
                        <p class="mt-0.5 text-xs font-medium text-gray-500">{{ $asset->asset_code }}</p>
                    </div>
                    <div class="shrink-0 rounded-lg bg-blue-50 px-3 py-2 text-right">
                        <p class="text-[10px] font-semibold uppercase tracking-wide text-gray-500">Jumlah Sistem</p>
                        <p class="text-sm font-bold text-[#465fff]">{{ number_format($asset->quantity ?? 1) }} {{ $asset->satuan ?: 'unit' }}</p>
                    </div>
                </div>
                <p class="mt-3 text-xs text-gray-500">Kondisi saat ini: <span class="font-semibold text-gray-700">{{ $asset->kondisi_aset_label }}</span></p>

                <details class="mt-3 rounded-xl border border-gray-200 bg-gray-50">
                    <summary class="cursor-pointer list-none px-3 py-2.5 text-xs font-semibold text-gray-700">Lihat Detail Aset</summary>
                    <dl class="grid grid-cols-2 gap-x-3 gap-y-2 border-t border-gray-200 px-3 py-3 text-xs">
                        <div>
                            <dt class="text-gray-500">Unit</dt>
                            <dd class="mt-0.5 font-medium text-gray-800">{{ $asset->unit?->name ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Lokasi</dt>
                            <dd class="mt-0.5 font-medium text-gray-800">{{ $asset->location?->name ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Penyimpanan</dt>
                            <dd class="mt-0.5 font-medium text-gray-800">{{ $asset->container?->name ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Identifikasi</dt>
                            <dd class="mt-0.5 font-medium text-gray-800">{{ $asset->identificationLabel() }}</dd>
                        </div>
                        <div class="col-span-2">
                            <dt class="text-gray-500">Lokasi Pemeriksaan</dt>
                            <dd class="mt-0.5 font-medium text-gray-800">{{ $locationContext['label'] }}</dd>
                        </div>
                    </dl>
                </details>
            </div>

            <div class="hidden md:block">
                <h2 class="mt-1 text-2xl font-bold">Form Pemeriksaan Aset</h2>
                <p class="mt-2 text-sm text-gray-500">Unit: {{ $check->unit?->name ?? '-' }} | Lokasi pemeriksaan: {{ $locationContext['label'] }}</p>
                <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ([
                        ['Kode Aset', $asset->asset_code],
                        ['Nama Aset', $asset->name],
                        ['Unit', $asset->unit?->name ?? '-'],
                        ['Jumlah Sistem', number_format($asset->quantity ?? 1).' '.($asset->satuan ?: 'unit')],
                        ['Kondisi Aset Saat Ini', $asset->kondisi_aset_label],
                        ['Lokasi', $asset->location?->name ?? '-'],
                        ['Penyimpanan', $asset->container?->name ?? '-'],
                        ['Cara Identifikasi', $asset->identificationLabel()],
                    ] as [$label, $value])
                        <div class="rounded-xl bg-gray-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ $label }}</p>
                            <p class="mt-1 font-semibold text-gray-900">{{ $value }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="mt-1">
                <h2 class="text-lg font-bold md:text-2xl">Daftar Aset Dalam Penyimpanan</h2>
                <p class="mt-1 text-sm font-semibold text-gray-700">{{ $container->name }}</p>
                <p class="mt-1 text-xs text-gray-500 md:text-sm">{{ $container->location?->name ?? '-' }} · {{ $container->unit?->name ?? $container->location?->unit?->name ?? '-' }}</p>
                <p class="mt-1 hidden text-sm text-gray-500 md:block">Lokasi pemeriksaan: {{ $locationContext['label'] }}</p>
            </div>
        @endif
    </section>

    @if ($type === 'container')
        <section class="rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="space-y-3 p-3 md:hidden">
                @forelse ($items as $item)
                    <article class="rounded-xl border border-gray-200 p-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-bold text-gray-900">{{ $item->asset?->name }}</p>
                                <p class="mt-0.5 text-xs font-medium text-[#465fff]">{{ $item->asset?->asset_code }}</p>
                            </div>
                            <span class="shrink-0 rounded-lg bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-700">{{ number_format($item->asset?->quantity ?? 0) }} {{ $item->asset?->satuan ?: 'unit' }}</span>
                        </div>
                        <div class="mt-3 grid grid-cols-2 gap-2 text-xs">
                            <div>
                                <p class="text-gray-500">Kategori</p>
                                <p class="mt-0.5 font-medium text-gray-800">{{ $item->asset?->category?->name ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Kondisi</p>
                                <p class="mt-0.5 font-medium text-gray-800">{{ $item->asset?->kondisi_aset_label ?? '-' }}</p>
                            </div>
                        </div>
                        <a href="{{ route('inventory-checks.scan', ['inventoryCheck' => $check, 'qrCode' => $item->asset?->asset_code, 'location_id' => $locationContext['id']]) }}" class="mt-3 block rounded-lg bg-[#465fff] px-3 py-2.5 text-center text-sm font-semibold text-white">Periksa Aset</a>
                    </article>
                @empty
                    <p class="px-3 py-6 text-center text-sm text-gray-500">Belum ada aset di penyimpanan ini.</p>
                @endforelse
            </div>

            <div class="hidden overflow-x-auto md:block">
                <table class="min-w-[900px] divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr>
                            <th class="px-4 py-3">Kode Aset</th>
                            <th class="px-4 py-3">Nama Aset</th>
                            <th class="px-4 py-3">Kategori</th>
                            <th class="px-4 py-3">Jumlah</th>
                            <th class="px-4 py-3">Satuan</th>
                            <th class="px-4 py-3">Cara Identifikasi</th>
                            <th class="px-4 py-3">Kondisi Aset</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($items as $item)
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $item->asset?->asset_code }}</td>
                                <td class="px-4 py-3">{{ $item->asset?->name }}</td>
                                <td class="px-4 py-3">{{ $item->asset?->category?->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ number_format($item->asset?->quantity ?? 0) }}</td>
                                <td class="px-4 py-3">{{ $item->asset?->satuan ?: 'unit' }}</td>
                                <td class="px-4 py-3">{{ $item->asset?->identificationLabel() }}</td>
                                <td class="px-4 py-3">{{ $item->asset?->kondisi_aset_label ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('inventory-checks.scan', ['inventoryCheck' => $check, 'qrCode' => $item->asset?->asset_code, 'location_id' => $locationContext['id']]) }}" class="rounded-lg bg-[#465fff] px-3 py-1.5 text-xs font-semibold text-white">Periksa</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-6 text-center text-gray-500">Belum ada aset di penyimpanan ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="flex flex-wrap items-center gap-2 border-t border-gray-200 p-3 md:gap-3 md:p-4">
                <a href="{{ route('inventory-checks.scan.form', $locationContext['filtered'] ? ['inventoryCheck' => $check, 'location_id' => $locationContext['id']] : $check) }}" class="rounded-lg border border-gray-300 px-3 py-2 text-xs font-semibold text-gray-700 md:px-4 md:text-sm">Scan Lagi</a>
                <a href="{{ route('inventory-checks.show', $check) }}" class="rounded-lg border border-gray-300 px-3 py-2 text-xs font-semibold text-gray-700 md:px-4 md:text-sm">Detail Pemeriksaan</a>
            </div>
        </section>
    @else
        <form method="POST" action="{{ route('inventory-checks.update', $check) }}" class="rounded-2xl border border-gray-200 bg-white shadow-sm">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="{{ $check->status }}">
            <input type="hidden" name="redirect_to_scan" value="1">
            @if ($locationContext['filtered'])
                <input type="hidden" name="location_id" value="{{ $locationContext['id'] }}">
            @endif

            <div class="hidden grid-cols-5 gap-3 border-b border-gray-200 bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-600 md:grid">
                <span>Jumlah Aktual</span>
                <span>Baik</span>
                <span>Sedang</span>
                <span>Rusak</span>
                <span>Hilang</span>
            </div>

            <div class="divide-y divide-gray-100">
                @foreach ($items as $item)
                    @php
                        $systemQty = (int) $item->jumlah_sistem;
                        $actualQty = (int) old("items.{$item->id}.jumlah_aktual", $item->jumlah_aktual ?? $systemQty);
                        $alreadyChecked = $item->jumlah_aktual !== null;
                        $goodQty = (int) old("items.{$item->id}.jumlah_baik", $alreadyChecked ? ($item->jumlah_baik ?? 0) : $systemQty);
                        $mediumQty = (int) old("items.{$item->id}.jumlah_sedang", $item->jumlah_sedang ?? 0);
                        $brokenQty = (int) old("items.{$item->id}.jumlah_rusak", $item->jumlah_rusak ?? 0);
                        $missingQty = (int) old("items.{$item->id}.jumlah_hilang", $item->jumlah_hilang ?? 0);
                    @endphp
                    <div
                        class="p-4"
                        x-data="{
                            actual: {{ $actualQty }},
                            good: {{ $goodQty }},
                            medium: {{ $mediumQty }},
                            broken: {{ $brokenQty }},
                            missing: {{ $missingQty }},
                            number(value) { return Math.max(0, Number(value) || 0) },
                            get total() { return this.number(this.good) + this.number(this.medium) + this.number(this.broken) + this.number(this.missing) },
                            get remaining() { return this.number(this.actual) - this.total },
                            allGood() {
                                this.good = this.number(this.actual);
                                this.medium = 0;
                                this.broken = 0;
                                this.missing = 0;
                            }
                        }"
                    >
                        <div class="mb-4 flex items-center justify-between gap-3 md:hidden">
                            <div>
                                <h3 class="text-sm font-bold text-gray-900">Kondisi Fisik Aset</h3>
                                <p class="mt-0.5 text-xs text-gray-500">Jumlah kondisi harus sama dengan jumlah aktual.</p>
                            </div>
                            <button type="button" @click="allGood()" class="shrink-0 rounded-lg border border-green-200 bg-green-50 px-3 py-2 text-xs font-semibold text-green-700">Semua Baik</button>
                        </div>

                        <div class="grid gap-3 md:grid-cols-5">
                            @foreach ([
                                ['jumlah_aktual', 'Jumlah Aktual', 'actual'],
                                ['jumlah_baik', 'Baik', 'good'],
                                ['jumlah_sedang', 'Sedang', 'medium'],
                                ['jumlah_rusak', 'Rusak', 'broken'],
                                ['jumlah_hilang', 'Hilang', 'missing'],
                            ] as [$field, $label, $model])
                                <label class="{{ $field === 'jumlah_aktual' ? 'block' : 'block' }}">
                                    <span class="mb-1.5 block text-xs font-semibold text-gray-600 md:hidden">{{ $label }}</span>
                                    <input
                                        type="number"
                                        min="0"
                                        name="items[{{ $item->id }}][{{ $field }}]"
                                        x-model.number="{{ $model }}"
                                        required
                                        class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm md:py-2"
                                    >
                                    @error("items.{$item->id}.{$field}")
                                        <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span>
                                    @enderror
                                </label>
                            @endforeach
                        </div>

                        <div class="mt-4 flex items-center justify-between rounded-xl px-3 py-2.5 text-xs font-medium md:mt-3"
                            :class="remaining === 0 ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'">
                            <span>Total kondisi: <strong x-text="total"></strong></span>
                            <span x-show="remaining !== 0">
                                <span x-text="remaining > 0 ? 'Sisa' : 'Kelebihan'"></span>:
                                <strong x-text="Math.abs(remaining)"></strong>
                            </span>
                            <span x-show="remaining === 0">Jumlah sesuai</span>
                        </div>

                        <div class="mt-3 hidden justify-end md:flex">
                            <button type="button" @click="allGood()" class="rounded-lg border border-green-200 bg-green-50 px-3 py-2 text-xs font-semibold text-green-700">Semua Baik</button>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="sticky bottom-0 z-20 grid grid-cols-2 gap-2 border-t border-gray-200 bg-white/95 p-3 shadow-[0_-8px_20px_rgba(15,23,42,0.06)] backdrop-blur md:static md:flex md:flex-wrap md:items-center md:gap-3 md:p-4 md:shadow-none">
                <button class="rounded-lg bg-[#465fff] px-3 py-2.5 text-sm font-semibold text-white md:px-4 md:py-2">Simpan Pemeriksaan</button>
                <a href="{{ route('inventory-checks.scan.form', $locationContext['filtered'] ? ['inventoryCheck' => $check, 'location_id' => $locationContext['id']] : $check) }}" class="rounded-lg border border-gray-300 px-3 py-2.5 text-center text-sm font-semibold text-gray-700 md:px-4 md:py-2">Scan Lagi</a>
                <a href="{{ route('inventory-checks.show', $check) }}" class="col-span-2 text-center text-xs font-semibold text-gray-500 md:text-sm">Detail Pemeriksaan</a>
            </div>
        </form>
    @endif
@endsection
