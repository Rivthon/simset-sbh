<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Status Penggantian Alat - SIMASET SBH</title>
    @include('partials.assets')
</head>
<body class="bg-gray-50 text-gray-900">
    <main class="mx-auto min-h-screen max-w-4xl px-4 py-8 sm:py-12">
        <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="text-center">
                <p class="text-sm font-semibold uppercase tracking-wide text-[#465fff]">SIMASET SBH</p>
                <h1 class="mt-3 text-2xl font-bold">Cek Status Penggantian Alat</h1>
                <p class="mt-2 text-sm leading-6 text-gray-500">Masukkan kode penggantian dan NIM untuk melihat status surat tanpa login.</p>
            </div>

            @if ($errors->any())
                <div class="mt-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <p class="font-semibold">Data belum valid.</p>
                    <ul class="mt-2 list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('tool-replacements.public.status.short.check') }}" class="mt-6 grid gap-4 sm:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto]">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700">Kode Penggantian</label>
                    <input name="replacement_code" value="{{ old('replacement_code', $replacementCode ?? request('replacement_code')) }}" required class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="PGA-GZI-05-2026-001">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">NIM</label>
                    <input name="student_nim" value="{{ old('student_nim', $studentNim ?? '') }}" required class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Masukkan NIM">
                </div>
                <div class="flex items-end">
                    <button class="w-full rounded-lg bg-[#465fff] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#3641f5]">Cek Status</button>
                </div>
            </form>
        </section>

        @isset($searched)
            <section class="mt-5 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                @if ($replacement)
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="text-sm font-semibold text-[#465fff]">{{ $replacement->replacement_code }}</p>
                            <h2 class="mt-2 text-xl font-bold text-gray-900">{{ $replacement->asset?->name }}</h2>
                            <p class="mt-1 text-sm text-gray-500">{{ $replacement->student_name }} | NIM {{ $replacement->display_nim }} | Semester {{ $replacement->display_semester }}</p>
                        </div>
                        @include('partials.badge', ['value' => $replacement->status])
                    </div>

                    <div class="mt-5 rounded-xl border border-[#c2d6ff] bg-[#ecf3ff] p-4">
                        <p class="text-sm font-semibold text-gray-900">{{ $replacement->status_message }}</p>
                        @if ($replacement->laboran_note)
                            <p class="mt-2 text-sm leading-6 text-gray-600">Catatan laboran: {{ $replacement->laboran_note }}</p>
                        @endif
                    </div>

                    <dl class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ([
                            ['Kode Penggantian', $replacement->replacement_code],
                            ['Status', $replacement->status_label],
                            ['Nama Mahasiswa', $replacement->student_name],
                            ['NIM', $replacement->display_nim],
                            ['Semester', $replacement->display_semester],
                            ['Nama Alat', $replacement->asset?->name],
                            ['Kode Aset', $replacement->asset?->asset_code],
                            ['Unit/Laboratorium', $replacement->unit?->name],
                            ['Praktikum', $replacement->practicum_name],
                            ['Tanggal Kejadian', $replacement->incident_date?->format('d/m/Y')],
                            ['Jumlah Rusak/Diganti', number_format($replacement->replacement_quantity).' '.($replacement->asset?->satuan ?: 'unit')],
                            ['Tanggal Verifikasi', $replacement->verified_at?->format('d/m/Y H:i')],
                            ['Tanggal Diterima/Selesai', $replacement->received_at?->format('d/m/Y H:i')],
                        ] as [$label, $value])
                            <div class="rounded-xl bg-gray-50 p-4">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ $label }}</dt>
                                <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $value ?: '-' }}</dd>
                            </div>
                        @endforeach
                    </dl>

                    @if ($replacement->damage_description)
                        <div class="mt-5 rounded-xl border border-gray-200 p-4">
                            <h3 class="font-semibold text-gray-900">Keterangan Kerusakan</h3>
                            <p class="mt-2 whitespace-pre-line text-sm leading-6 text-gray-600">{{ $replacement->damage_description }}</p>
                        </div>
                    @endif
                @else
                    <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-800">
                        Data tidak ditemukan. Pastikan kode penggantian dan NIM sudah benar.
                    </div>
                @endif
            </section>
        @endisset
    </main>
</body>
</html>
