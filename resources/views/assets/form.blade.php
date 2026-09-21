@extends('layouts.dashboard')

@section('title', $asset->exists ? 'Edit Aset' : 'Tambah Aset')

@section('content')
    @php
        $conditionOptions = \App\Models\Asset::KONDISI_ASET_LABELS;
        $identificationOptions = [
            'individual' => 'QR Individual',
            'group' => 'QR Kelompok',
        ];
        $currentCounts = $asset->conditionCounts();
        if (! $asset->exists && array_sum($currentCounts) === 0) {
            $currentCounts['baik'] = 1;
        }
        $conditionValues = collect($conditionOptions)->mapWithKeys(
            fn ($label, $condition) => [$condition => (int) old('jumlah_'.$condition, $currentCounts[$condition] ?? 0)]
        )->all();
        $satuanOptions = \App\Models\Asset::SATUAN_OPTIONS;
        $savedSatuan = old('satuan', $asset->satuan ?: 'unit');
        $selectedSatuan = old('satuan_choice', array_key_exists($savedSatuan, $satuanOptions) ? $savedSatuan : 'lainnya');
        $customSatuan = old('satuan_custom', $selectedSatuan === 'lainnya' ? $savedSatuan : '');
    @endphp

    <form
        method="POST"
        action="{{ $asset->exists ? route('assets.update', $asset) : route('assets.store') }}"
        enctype="multipart/form-data"
        class="space-y-5"
        x-data="{
            quantity: {{ (int) old('quantity', $asset->quantity ?: 1) }},
            baik: {{ $conditionValues['baik'] }},
            sedang: {{ $conditionValues['sedang'] }},
            rusak: {{ $conditionValues['rusak'] }},
            hilang: {{ $conditionValues['hilang'] }},
            get conditionTotal() { return Number(this.baik) + Number(this.sedang) + Number(this.rusak) + Number(this.hilang) },
            get remaining() { return Number(this.quantity) - this.conditionTotal }
        }"
    >
        @csrf
        @if ($asset->exists)
            @method('PUT')
        @endif

        @if (! $asset->exists && isset($sourceContainer) && $sourceContainer)
            <input type="hidden" name="redirect_container_id" value="{{ $sourceContainer->id }}">
            <section class="rounded-2xl border border-blue-100 bg-blue-50 px-5 py-4 text-sm text-blue-800">
                <p class="font-semibold">Aset akan dimasukkan ke {{ $sourceContainer->name }}</p>
                <p class="mt-1 text-blue-700">{{ $sourceContainer->location?->name ?? '-' }} | {{ $sourceContainer->unit?->name ?? $sourceContainer->location?->unit?->name ?? '-' }}</p>
            </section>
        @endif

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-5 flex flex-col gap-1">
                <h2 class="text-base font-semibold text-slate-900">Identitas Aset</h2>
                <p class="text-sm text-slate-500">Kode sistem dibuat otomatis, kode lama boleh dikosongkan jika tidak ada.</p>
            </div>
            <div class="grid gap-5 md:grid-cols-3">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Kode Aset Sistem</label>
                    @if ($asset->exists)
                        <input value="{{ $asset->asset_code }}" disabled class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-700">
                    @else
                        <div class="mt-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-500">Dibuat otomatis saat disimpan</div>
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Kode Aset Lama</label>
                    <input name="legacy_inventory_code" value="{{ old('legacy_inventory_code', $asset->legacy_inventory_code) }}" placeholder="Opsional" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Nama Aset</label>
                    <input name="name" value="{{ old('name', $asset->name) }}" required placeholder="Contoh: Tabung Reaksi" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-5 flex flex-col gap-1">
                <h2 class="text-base font-semibold text-slate-900">Penempatan</h2>
                <p class="text-sm text-slate-500">Pilih unit, kategori, lokasi, dan penyimpanan aset.</p>
            </div>
            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                @if (auth()->user()->isPengelola())
                    <input type="hidden" name="unit_id" value="{{ auth()->user()->unit_id }}">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Unit/Laboratorium</label>
                        <div class="mt-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-600">{{ auth()->user()->unit?->name ?? '-' }}</div>
                    </div>
                @else
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Unit/Laboratorium</label>
                        <select name="unit_id" required class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                            <option value="">Pilih unit/laboratorium</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}" @selected((int) old('unit_id', $asset->unit_id) === $unit->id)>{{ $unit->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-slate-700">Kategori</label>
                    <select name="category_id" required class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        <option value="">Pilih kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((int) old('category_id', $asset->category_id) === $category->id)>{{ $category->name }}{{ $category->unit ? ' - '.$category->unit->name : '' }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Lokasi</label>
                    <select name="location_id" required class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        <option value="">Pilih lokasi</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}" @selected((int) old('location_id', $asset->location_id) === $location->id)>{{ $location->name }}{{ auth()->user()->isAdmin() && $location->unit ? ' - '.$location->unit->name : '' }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Penyimpanan</label>
                    <select name="container_id" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        <option value="">Tanpa penyimpanan</option>
                        @foreach ($containers as $container)
                            <option value="{{ $container->id }}" @selected((int) old('container_id', $asset->container_id) === $container->id)>{{ $container->name }} - {{ $container->location?->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-5 flex flex-col gap-1">
                <h2 class="text-base font-semibold text-slate-900">Jumlah dan Identifikasi</h2>
                <p class="text-sm text-slate-500">Atur jumlah, satuan, kondisi, dan cara aset diberi identitas QR.</p>
            </div>
            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Jumlah</label>
                    <input type="number" name="quantity" x-model.number="quantity" value="{{ old('quantity', $asset->quantity ?: 1) }}" min="1" required @readonly($conditionLocked) class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm read-only:bg-slate-100 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    @if ($conditionLocked)
                        <p class="mt-2 text-xs text-amber-700">Jumlah dikunci karena aset sudah memiliki stock opname selesai.</p>
                    @endif
                </div>

                <div x-data="{ satuanChoice: @js($selectedSatuan) }">
                    <label class="block text-sm font-medium text-slate-700">Satuan</label>
                    <select name="satuan_choice" x-model="satuanChoice" required class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        @foreach ($satuanOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('satuan_choice')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('satuan')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <div x-show="satuanChoice === 'lainnya'" x-cloak>
                        <input name="satuan_custom" value="{{ $customSatuan }}" x-bind:required="satuanChoice === 'lainnya'" placeholder="Tulis satuan khusus" class="mt-3 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        @error('satuan_custom')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Cara Identifikasi Aset</label>
                    <select name="identification_type" required class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        @foreach ($identificationOptions as $value => $label)
                            <option value="{{ $value }}" @selected(old('identification_type', $asset->identification_type ?: 'individual') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <p class="mt-2 text-xs leading-5 text-slate-500">QR Individual untuk satu aset. QR Kelompok untuk beberapa aset sejenis dalam satu data aset.</p>
                </div>
            </div>

            <div class="mt-5 border-t border-slate-200 pt-5">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <label class="text-sm font-medium text-slate-700">Pembagian Kondisi Aset</label>
                    <p class="text-xs font-semibold" :class="remaining === 0 ? 'text-emerald-600' : 'text-red-600'">
                        Total kondisi: <span x-text="conditionTotal"></span> / <span x-text="quantity"></span>
                        <span x-show="remaining !== 0">(<span x-text="remaining > 0 ? 'Sisa '+remaining : 'Lebih '+Math.abs(remaining)"></span>)</span>
                    </p>
                </div>
                <div class="mt-3 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    @foreach ($conditionOptions as $condition => $label)
                        <div>
                            <label class="block text-xs font-medium uppercase tracking-wide text-slate-500">{{ $label }}</label>
                            <input type="number" name="jumlah_{{ $condition }}" x-model.number="{{ $condition }}" value="{{ $conditionValues[$condition] }}" min="0" required @readonly($conditionLocked) class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm read-only:bg-slate-100 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </div>
                    @endforeach
                </div>
                @error('quantity')
                    <p class="mt-3 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @if ($conditionLocked)
                    <p class="mt-3 text-xs leading-5 text-slate-500">Untuk menambah barang baru tanpa mengubah riwayat stock opname, gunakan menu Tambah Kuantitas pada halaman detail aset.</p>
                @endif
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <label class="block text-sm font-medium text-slate-700">Keterangan</label>
            <textarea name="description" rows="4" placeholder="Tambahkan catatan aset jika diperlukan" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">{{ old('description', $asset->description) }}</textarea>
        </section>

        <div class="sticky bottom-0 z-10 -mx-4 border-t border-slate-200 bg-white/95 px-4 py-4 backdrop-blur sm:static sm:mx-0 sm:rounded-2xl sm:border sm:bg-white sm:shadow-sm">
            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a href="{{ $asset->exists ? route('assets.show', $asset) : route('assets.index') }}" class="inline-flex justify-center rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</a>
                <button class="inline-flex justify-center rounded-xl bg-blue-600 px-5 py-2 text-sm font-semibold text-white hover:bg-blue-700">{{ $asset->exists ? 'Simpan Perubahan' : 'Simpan Aset' }}</button>
            </div>
        </div>
    </form>
@endsection
