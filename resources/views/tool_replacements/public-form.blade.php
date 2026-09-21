<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Penggantian Alat Rusak - SIMASET SBH</title>
    @include('partials.assets')
</head>
<body class="bg-gray-50 text-gray-900">
    <main class="mx-auto min-h-screen max-w-4xl px-4 py-6 sm:px-5 sm:py-10">
        <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <p class="text-sm font-semibold uppercase tracking-wide text-[#465fff]">Surat Penggantian Alat Rusak</p>
            <h1 class="mt-3 text-2xl font-bold">Isi Surat Penggantian Alat</h1>
            <p class="mt-2 text-sm text-gray-500">Data akan dikirim ke laboran untuk diverifikasi.</p>
            <a href="{{ route('tool-replacements.public.status') }}" class="mt-3 inline-flex text-sm font-semibold text-[#465fff]">Cek status surat yang sudah dikirim</a>

            <div class="mt-6 rounded-xl bg-gray-50 p-4">
                <p class="text-sm font-semibold text-gray-900">{{ $asset->name }}</p>
                <p class="mt-1 text-sm text-gray-500">{{ $asset->asset_code }} | {{ $asset->unit?->name ?? '-' }} | {{ number_format($asset->quantity ?? 1) }} {{ $asset->satuan ?: 'unit' }}</p>
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

            <form method="POST" action="{{ route('tool-replacements.public.store', $asset) }}" enctype="multipart/form-data" class="mt-6 space-y-5">
                @csrf
                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Mahasiswa</label>
                        <input name="student_name" value="{{ old('student_name') }}" required class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">NIM</label>
                        <input name="student_nim" value="{{ old('student_nim') }}" required class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Semester</label>
                        <input name="student_semester" value="{{ old('student_semester') }}" required class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Prodi/Kelas</label>
                        <input name="prodi_kelas" value="{{ old('prodi_kelas') }}" class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Praktikum</label>
                        <input name="practicum_name" value="{{ old('practicum_name') }}" required class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tanggal Kejadian</label>
                        <input type="date" name="incident_date" value="{{ old('incident_date', now()->toDateString()) }}" required class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jumlah Rusak/Diganti</label>
                        <input type="number" name="replacement_quantity" value="{{ old('replacement_quantity', ($asset->identification_type === 'individual' ? 1 : null)) }}" min="1" max="{{ max(1, (int) ($asset->quantity ?? 1)) }}" required class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <p class="mt-1 text-xs text-gray-500">Maksimal {{ number_format($asset->quantity ?? 1) }} {{ $asset->satuan ?: 'unit' }}.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nomor WhatsApp</label>
                        <input name="whatsapp_number" value="{{ old('whatsapp_number') }}" class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Foto Kerusakan</label>
                        <input type="file" name="damage_photo" accept="image/*" class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Keterangan Kerusakan</label>
                    <textarea name="damage_description" rows="4" class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">{{ old('damage_description') }}</textarea>
                </div>

                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 text-sm leading-6 text-gray-600">
                    Mahasiswa menyatakan telah mengganti dan bertanggung jawab atas kerusakan alat yang digunakan pada saat kegiatan praktikum berlangsung serta mematuhi tata tertib yang berlaku di laboratorium.
                </div>

                <div class="flex flex-col gap-3 sm:flex-row">
                    <button class="rounded-lg bg-[#465fff] px-5 py-3 text-sm font-semibold text-white hover:bg-[#3641f5]">Kirim Surat</button>
                    <a href="{{ url()->previous() }}" class="rounded-lg border border-gray-300 px-5 py-3 text-center text-sm font-semibold text-gray-700">Kembali</a>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
