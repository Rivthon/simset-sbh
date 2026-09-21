<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Penyimpanan - {{ $container->code }}</title>
    @include('partials.assets')
    <style>
        @page {
            size: A6 portrait;
            margin: 10mm;
        }

        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }

            [data-qr-card] {
                width: 100% !important;
                max-width: none !important;
                border: 0 !important;
                box-shadow: none !important;
                padding: 0 !important;
            }
        }
    </style>
</head>
<body class="min-h-screen bg-slate-100 px-4 py-4 text-slate-900">
    <main data-qr-card class="mx-auto max-w-[470px] rounded-xl border border-slate-200 bg-white px-5 py-5 text-center shadow-sm ring-1 ring-slate-100 print:ring-0 sm:px-7">
        <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-500">SIMASET SBH</p>
        <p class="mt-2 inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700 ring-1 ring-blue-100">QR Penyimpanan</p>
        <h1 class="mt-3 break-words text-2xl font-black tracking-wide text-slate-950">{{ $container->code }}</h1>
        <p class="mt-1 text-base font-semibold text-slate-700">{{ $container->name }}</p>

        <div class="my-5 rounded-xl border border-slate-100 bg-white p-3 shadow-inner shadow-slate-100">
            @include('partials.qr', ['data' => $qrData, 'size' => 360, 'class' => 'mx-auto inline-block bg-white [&_svg]:h-[min(78vw,360px)] [&_svg]:w-[min(78vw,360px)] print:[&_svg]:h-[82mm] print:[&_svg]:w-[82mm]'])
        </div>

        <div class="rounded-lg bg-slate-50 px-4 py-2.5 text-sm text-slate-600">
            <p class="font-semibold text-slate-800">{{ $container->location?->name ?? '-' }}</p>
            <p class="mt-1">{{ $container->unit?->name ?? $container->location?->unit?->name ?? '-' }}</p>
            <p class="mt-1">{{ $container->assets->count() }} aset di dalam penyimpanan</p>
        </div>

        <div class="mt-5 flex flex-col gap-3 print:hidden sm:flex-row sm:justify-center">
            <button onclick="window.print()" class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-blue-700">Cetak QR Penyimpanan</button>
            <a href="{{ route('containers.show', $container) }}" class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">Kembali</a>
        </div>
    </main>
</body>
</html>
