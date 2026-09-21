<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Tidak Ditemukan - SIMASET SBH</title>
    @include('partials.assets')
</head>
<body class="bg-gray-50 text-gray-900">
    <main class="mx-auto flex min-h-screen max-w-lg items-center px-4 py-8">
        <section class="w-full rounded-2xl border border-gray-200 bg-white p-6 text-center shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-wide text-red-600">Kode tidak ditemukan</p>
            <h1 class="mt-3 text-2xl font-bold">Data QR Code tidak tersedia</h1>
            <p class="mt-3 text-sm leading-6 text-gray-500">
                Kode yang dipindai tidak terdaftar sebagai QR aset atau QR Penyimpanan SIMASET SBH.
            </p>
            <p class="mt-4 rounded-xl bg-gray-50 px-4 py-3 font-mono text-xs text-gray-600">{{ $qrCode }}</p>
            <a href="{{ route('login') }}" class="mt-6 inline-flex justify-center rounded-lg bg-[#465fff] px-4 py-2.5 text-sm font-semibold text-white">Masuk ke SIMASET SBH</a>
        </section>
    </main>
</body>
</html>
