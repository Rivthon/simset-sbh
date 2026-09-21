@extends('layouts.dashboard')

@section('title', 'Detail Penggantian Alat Rusak')

@section('content')
    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_380px]">
        <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-[#465fff]">Surat Penggantian Alat Rusak</p>
                    <h2 class="mt-2 text-2xl font-bold text-gray-900">{{ $replacement->student_name }}</h2>
                    <p class="mt-1 text-sm text-gray-500">{{ $replacement->replacement_code }} | NIM {{ $replacement->display_nim }} | Semester {{ $replacement->display_semester }}{{ $replacement->prodi_kelas ? ' | '.$replacement->prodi_kelas : '' }}</p>
                </div>
                @include('partials.badge', ['value' => $replacement->status])
            </div>

            <div class="mt-5 rounded-lg border border-[#c2d6ff] bg-[#ecf3ff] p-4">
                <p class="text-sm font-semibold text-gray-900">{{ $replacement->status_message }}</p>
                @if ($replacement->laboran_note)
                    <p class="mt-2 text-sm text-gray-600">Catatan laboran: {{ $replacement->laboran_note }}</p>
                @endif
            </div>

            <dl class="mt-6 grid gap-3 md:grid-cols-2">
                @foreach ([
                    ['Nama Alat', $replacement->asset?->name],
                    ['Kode Aset', $replacement->asset?->asset_code],
                    ['NIM', $replacement->display_nim],
                    ['Semester', $replacement->display_semester],
                    ['Unit', $replacement->unit?->name],
                    ['Lokasi', $replacement->asset?->location?->name],
                    ['Tempat Penyimpanan', $replacement->asset?->container?->name],
                    ['Praktikum', $replacement->practicum_name],
                    ['Tanggal Kejadian', $replacement->incident_date?->format('d/m/Y')],
                    ['Jumlah Rusak/Diganti', number_format($replacement->replacement_quantity).' '.($replacement->asset?->satuan ?: 'unit')],
                    ['Nomor WhatsApp', $replacement->whatsapp_number],
                    ['Diverifikasi Oleh', $replacement->verifier?->name],
                    ['Tanggal Verifikasi', $replacement->verified_at?->format('d/m/Y H:i')],
                    ['Tanggal Diterima', $replacement->received_at?->format('d/m/Y H:i')],
                ] as [$label, $value])
                    <div class="rounded-lg bg-gray-50 p-4">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ $label }}</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $value ?: '-' }}</dd>
                    </div>
                @endforeach
            </dl>

            <div class="mt-5 grid gap-4 md:grid-cols-2">
                <div class="rounded-lg border border-gray-200 p-4">
                    <h3 class="font-semibold text-gray-900">Keterangan Kerusakan</h3>
                    <p class="mt-2 whitespace-pre-line text-sm leading-6 text-gray-600">{{ $replacement->damage_description ?: '-' }}</p>
                </div>
                <div class="rounded-lg border border-gray-200 p-4">
                    <h3 class="font-semibold text-gray-900">Catatan Laboran</h3>
                    <p class="mt-2 whitespace-pre-line text-sm leading-6 text-gray-600">{{ $replacement->laboran_note ?: '-' }}</p>
                    @if ($replacement->rejection_reason)
                        <p class="mt-3 text-sm font-semibold text-red-700">Alasan ditolak: {{ $replacement->rejection_reason }}</p>
                    @endif
                </div>
            </div>

            @if ($replacement->damage_photo)
                <div class="mt-5 rounded-lg border border-gray-200 p-4">
                    <h3 class="font-semibold text-gray-900">Foto Kerusakan</h3>
                    <img src="{{ asset('storage/'.$replacement->damage_photo) }}" alt="Foto kerusakan" class="mt-3 max-h-96 rounded-lg border border-gray-200 object-contain">
                </div>
            @endif
        </section>

        <aside class="space-y-5">
            @if (! auth()->user()->isPimpinan())
                <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                    <h3 class="font-semibold text-gray-900">Verifikasi Laboran</h3>
                    <form method="POST" action="{{ route('tool-replacements.status', $replacement) }}" class="mt-4 space-y-4">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                @foreach ($statusLabels as $value => $label)
                                    <option value="{{ $value }}" @selected(old('status', $replacement->status) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Catatan Laboran</label>
                            <textarea name="laboran_note" rows="3" class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">{{ old('laboran_note', $replacement->laboran_note) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Alasan Penolakan</label>
                            <textarea name="rejection_reason" rows="3" class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">{{ old('rejection_reason', $replacement->rejection_reason) }}</textarea>
                        </div>
                        <button class="w-full rounded-lg bg-[#465fff] px-4 py-2.5 text-sm font-semibold text-white">Simpan Status</button>
                    </form>
                </section>
            @endif
        </aside>
    </div>
@endsection
