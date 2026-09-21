<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Berhasil Dikirim - SIMASET SBH</title>
    @include('partials.assets')
</head>
<body class="bg-gray-50 text-gray-900">
    <main class="flex min-h-screen items-center justify-center px-4 py-8">
        <section class="w-full max-w-2xl rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="text-center">
                <p class="text-sm font-semibold uppercase tracking-wide text-[#465fff]">Surat Penggantian Alat Rusak</p>
                <h1 class="mt-3 text-2xl font-bold">Surat Berhasil Dikirim</h1>
                <p class="mt-3 text-sm leading-6 text-gray-600">
                    Surat penggantian alat berhasil dikirim dan menunggu verifikasi laboran.
                </p>
            </div>

            <div class="mt-6 rounded-xl border border-[#c2d6ff] bg-[#ecf3ff] p-4 text-center">
                <p class="text-xs font-semibold uppercase tracking-wide text-[#465fff]">Kode Penggantian</p>
                <p id="replacement-code" class="mt-2 text-3xl font-bold tracking-wide text-gray-950">{{ $replacement->replacement_code }}</p>
                <p class="mt-2 text-sm text-gray-600">Simpan kode penggantian ini untuk mengecek status penggantian alat.</p>
            </div>

            <dl class="mt-5 grid gap-3 sm:grid-cols-2">
                @foreach ([
                    ['Nama alat', $replacement->asset?->name],
                    ['Nama mahasiswa', $replacement->student_name],
                    ['NIM', $replacement->display_nim],
                    ['Semester', $replacement->display_semester],
                    ['Status saat ini', $replacement->status_label],
                ] as [$label, $value])
                    <div class="rounded-xl bg-gray-50 p-4">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ $label }}</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $value ?: '-' }}</dd>
                    </div>
                @endforeach
            </dl>

            <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:justify-center">
                <a href="{{ route('tool-replacements.public.status.short', ['replacement_code' => $replacement->replacement_code]) }}" class="inline-flex justify-center rounded-lg border border-[#465fff] px-5 py-3 text-sm font-semibold text-[#465fff]">Cek Status Penggantian</a>
                <button type="button" data-copy-code class="inline-flex justify-center rounded-lg border border-gray-300 px-5 py-3 text-sm font-semibold text-gray-700">Salin Kode</button>
                <a href="{{ route('assets.scan', $replacement->asset?->qr_code ?: $replacement->asset?->asset_code) }}" class="inline-flex justify-center rounded-lg bg-[#465fff] px-5 py-3 text-sm font-semibold text-white">Kembali ke Hasil Scan</a>
            </div>
            <p data-copy-message class="mt-3 hidden text-center text-sm font-semibold text-emerald-700">Kode berhasil disalin.</p>
        </section>
    </main>

    <script>
        document.querySelector('[data-copy-code]')?.addEventListener('click', async () => {
            const code = document.getElementById('replacement-code')?.textContent?.trim() || '';
            await navigator.clipboard.writeText(code);
            document.querySelector('[data-copy-message]')?.classList.remove('hidden');
        });
    </script>
</body>
</html>
