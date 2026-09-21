<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMASET SBH - Sistem Manajemen Aset</title>
    @include('partials.assets')
    <style>
        @keyframes scanMove {
            0% { top: 8%; opacity: .25; }
            50% { opacity: 1; }
            100% { top: 88%; opacity: .25; }
        }

        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 0 28px rgba(20, 184, 166, .22); }
            50% { box-shadow: 0 0 46px rgba(34, 211, 238, .42); }
        }

        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(14px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        .scan-line {
            animation: scanMove 2.8s ease-in-out infinite alternate;
        }

        .scanner-glow {
            animation: pulseGlow 3.2s ease-in-out infinite;
        }

        .asset-found-card {
            animation: fadeInUp .85s ease-out .35s both;
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-900 antialiased">
    @php
        $loginUrl = \Illuminate\Support\Facades\Route::has('login') ? route('login') : url('/login');
        $statusUrl = \Illuminate\Support\Facades\Route::has('tool-replacements.public.status.short')
            ? route('tool-replacements.public.status.short')
            : url('/cek-status-penggantian');
    @endphp

    <div class="relative overflow-hidden bg-[radial-gradient(circle_at_top_left,_rgba(249,115,22,0.18),_transparent_34%),linear-gradient(135deg,_#06111f_0%,_#0f172a_45%,_#102a3d_100%)]">
        <div class="pointer-events-none absolute left-[-120px] top-24 h-72 w-72 rounded-full bg-orange-400/20 blur-3xl"></div>
        <div class="pointer-events-none absolute right-[-90px] top-56 h-80 w-80 rounded-full bg-teal-300/[0.16] blur-3xl"></div>
        <div class="pointer-events-none absolute bottom-16 left-1/3 h-72 w-72 rounded-full bg-orange-500/10 blur-3xl"></div>

        <header class="sticky top-0 z-50 border-b border-white/10 bg-slate-950/75 backdrop-blur-xl">
            <nav class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ url('/') }}" class="flex min-w-0 items-center gap-3">
                    <img src="{{ asset('images/logo-simaset-rounded.png') }}" alt="Logo SIMASET SBH" class="h-10 w-10 shrink-0 rounded-lg object-contain">
                    <span class="min-w-0">
                        <span class="block truncate text-base font-bold text-white">SIMASET SBH</span>
                        <span class="block truncate text-xs font-medium text-orange-100/75">Sistem Informasi Manajemen Aset</span>
                    </span>
                </a>

                <div class="hidden items-center gap-7 text-sm font-semibold text-slate-300 md:flex">
                    <a href="#fitur" class="transition hover:text-orange-200">Fitur</a>
                    <a href="#alur" class="transition hover:text-orange-200">Alur Sistem</a>
                    <a href="#qr-code" class="transition hover:text-orange-200">QR Code</a>
                    <a href="{{ $statusUrl }}" class="transition hover:text-orange-200">Cek Status</a>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ $statusUrl }}" class="hidden items-center justify-center gap-2 rounded-2xl border border-white/[0.15] bg-white/10 px-4 py-2.5 text-sm font-bold text-white backdrop-blur transition hover:-translate-y-0.5 hover:border-orange-300/40 hover:bg-white/[0.15] sm:inline-flex">
                        <x-nav-icon name="view" />
                        Cek Status
                    </a>
                    <a href="{{ $loginUrl }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-orange-500 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-orange-950/20 transition hover:-translate-y-0.5 hover:bg-orange-400">
                        <x-nav-icon name="dashboard" />
                        Login
                    </a>
                </div>
            </nav>
        </header>

        <section class="relative mx-auto grid min-h-[calc(100vh-76px)] max-w-7xl gap-10 px-4 py-12 sm:px-6 lg:grid-cols-[minmax(0,1fr)_500px] lg:items-center lg:px-8 lg:py-16">
            <div class="relative z-10">
                <h1 class="max-w-4xl text-4xl font-bold leading-tight tracking-tight text-white sm:text-5xl lg:text-6xl">
                    Kelola dan Monitoring Aset Lebih Terstruktur dengan SIMASET
                </h1>
                <p class="mt-6 max-w-2xl text-base leading-8 text-slate-300 sm:text-lg">
                    SIMASET SBH membantu proses pendataan, identifikasi, monitoring kondisi, dan pelaporan aset melalui sistem berbasis web yang terintegrasi dengan QR Code.
                </p>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                    <a href="{{ $loginUrl }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-orange-500 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-orange-950/25 transition hover:-translate-y-0.5 hover:bg-orange-400">
                        <x-nav-icon name="dashboard" />
                        Masuk ke Dashboard
                    </a>
                    <a href="#fitur" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-white/[0.15] bg-white/10 px-5 py-3 text-sm font-bold text-white backdrop-blur transition hover:-translate-y-0.5 hover:border-orange-300/40 hover:bg-white/[0.15]">
                        <x-nav-icon name="category" />
                        Lihat Fitur Sistem
                    </a>
                    <a href="{{ $statusUrl }}" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-orange-300/25 bg-orange-300/10 px-5 py-3 text-sm font-bold text-orange-100 backdrop-blur transition hover:-translate-y-0.5 hover:bg-orange-300/[0.15]">
                        <x-nav-icon name="view" />
                        Cek Status Penggantian
                    </a>
                </div>

                <div class="mt-8 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ([
                        ['Berbasis Web', 'dashboard'],
                        ['Terintegrasi QR Code', 'qr'],
                        ['Monitoring Kondisi Aset', 'asset'],
                        ['Laporan Terstruktur', 'report'],
                    ] as [$label, $iconName])
                        <div class="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/[0.08] px-4 py-3 text-sm font-semibold text-slate-100 backdrop-blur">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-orange-300/[0.12] text-orange-200">
                                <x-nav-icon :name="$iconName" />
                            </span>
                            <span>{{ $label }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div id="qr-code" class="relative z-10 scroll-mt-28">
                <div class="rounded-2xl border border-white/[0.18] bg-white/[0.12] p-4 shadow-2xl shadow-slate-950/35 backdrop-blur-xl sm:p-5">
                    <div class="rounded-2xl border border-white/[0.14] bg-slate-950/45 p-5">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-cyan-300/[0.12] text-cyan-200">
                                    <x-nav-icon name="qr" />
                                </span>
                                <div>
                                    <p class="text-base font-bold text-white">QR Code Scanner</p>
                                    <p class="mt-0.5 text-xs font-medium text-slate-400">Simulasi identifikasi aset</p>
                                </div>
                            </div>
                            <span class="rounded-full border border-emerald-300/25 bg-emerald-300/10 px-3 py-1 text-xs font-bold text-emerald-200">Asset Found</span>
                        </div>

                        <div class="mt-6 grid gap-5 sm:grid-cols-[210px_minmax(0,1fr)] lg:grid-cols-1">
                            <div>
                                <div class="scanner-glow relative mx-auto aspect-square w-full max-w-[230px] overflow-hidden rounded-2xl border border-cyan-200/25 bg-white p-5">
                                    <div class="absolute inset-4 rounded-xl border border-cyan-300/60 shadow-[inset_0_0_26px_rgba(20,184,166,0.18)]" aria-hidden="true"></div>
                                    <div class="scan-line absolute left-4 right-4 z-20 h-1 rounded-full bg-gradient-to-r from-transparent via-cyan-300 to-transparent shadow-[0_0_20px_rgba(34,211,238,1)]" aria-hidden="true"></div>
                                    <svg class="relative z-10 h-full w-full text-slate-950" viewBox="0 0 120 120" fill="currentColor" aria-label="QR Code dummy">
                                        <rect width="120" height="120" rx="10" fill="white"/>
                                        <rect x="8" y="8" width="28" height="28" rx="3"/>
                                        <rect x="14" y="14" width="16" height="16" rx="2" fill="white"/>
                                        <rect x="19" y="19" width="6" height="6"/>
                                        <rect x="84" y="8" width="28" height="28" rx="3"/>
                                        <rect x="90" y="14" width="16" height="16" rx="2" fill="white"/>
                                        <rect x="95" y="19" width="6" height="6"/>
                                        <rect x="8" y="84" width="28" height="28" rx="3"/>
                                        <rect x="14" y="90" width="16" height="16" rx="2" fill="white"/>
                                        <rect x="19" y="95" width="6" height="6"/>
                                        <rect x="44" y="10" width="8" height="8"/>
                                        <rect x="58" y="10" width="6" height="6"/>
                                        <rect x="70" y="16" width="6" height="12"/>
                                        <rect x="46" y="28" width="14" height="6"/>
                                        <rect x="66" y="34" width="8" height="8"/>
                                        <rect x="40" y="44" width="8" height="8"/>
                                        <rect x="54" y="44" width="6" height="16"/>
                                        <rect x="66" y="48" width="18" height="6"/>
                                        <rect x="94" y="44" width="10" height="10"/>
                                        <rect x="42" y="66" width="14" height="6"/>
                                        <rect x="62" y="62" width="8" height="16"/>
                                        <rect x="78" y="66" width="8" height="8"/>
                                        <rect x="96" y="62" width="12" height="6"/>
                                        <rect x="44" y="84" width="8" height="8"/>
                                        <rect x="58" y="90" width="16" height="6"/>
                                        <rect x="82" y="84" width="8" height="20"/>
                                        <rect x="96" y="82" width="8" height="8"/>
                                        <rect x="100" y="100" width="12" height="12"/>
                                        <rect x="46" y="104" width="18" height="8"/>
                                    </svg>
                                </div>
                                <div class="mt-4 flex items-center justify-center gap-2 text-sm font-bold text-cyan-200">
                                    <span class="h-2 w-2 rounded-full bg-cyan-300 shadow-[0_0_12px_rgba(103,232,249,1)]"></span>
                                    Scanning QR Code...
                                </div>
                            </div>

                            <div class="asset-found-card rounded-2xl border border-white/[0.12] bg-white/10 p-4 backdrop-blur">
                                <div class="mb-4 flex items-center justify-between gap-3">
                                    <p class="text-sm font-bold text-white">Preview Data Aset</p>
                                    <span class="rounded-full border border-cyan-300/25 bg-cyan-300/10 px-3 py-1 text-xs font-bold text-cyan-100">Aktif</span>
                                </div>
                                <dl class="space-y-3 text-sm">
                                    @foreach ([
                                        ['Kode Aset', 'AST-SBH-ALT-0001', 'text-white'],
                                        ['Nama Aset', 'Perangkat Inventaris', 'text-white'],
                                        ['Unit', 'Unit Kerja', 'text-white'],
                                        ['Lokasi', 'Ruang Penyimpanan', 'text-white'],
                                        ['Kondisi', 'Baik', 'text-emerald-200'],
                                        ['Status', 'Aktif', 'text-cyan-100'],
                                    ] as [$key, $value, $tone])
                                        <div class="flex items-start justify-between gap-4">
                                            <dt class="text-slate-400">{{ $key }}</dt>
                                            <dd class="text-right font-semibold {{ $tone }}">{{ $value }}</dd>
                                        </div>
                                    @endforeach
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <main>
        <section id="fitur" class="bg-slate-50 px-4 py-16 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div class="max-w-2xl">
                        <p class="text-sm font-semibold uppercase tracking-wide text-orange-700">Fitur Utama</p>
                        <h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-950 sm:text-4xl">Fitur yang Ada di SIMASET SBH</h2>
                    </div>
                    <p class="max-w-xl text-sm leading-6 text-slate-600">Fitur disusun sesuai modul yang tersedia saat ini, dari master data sampai laporan.</p>
                </div>

                <div class="mt-10 grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                    @foreach ([
                        ['Dashboard Sesuai Peran', 'Admin, pengelola, dan kaprodi memiliki tampilan serta akses yang disesuaikan dengan kebutuhan masing-masing.', 'dashboard', 'bg-orange-50 text-orange-700'],
                        ['Master Data Aset', 'Mengelola unit kerja, kategori, lokasi, dan tempat penyimpanan sebagai struktur dasar data aset.', 'category', 'bg-slate-100 text-slate-700'],
                        ['Pendataan Aset', 'Mencatat data aset, kode aset, kondisi, satuan, unit, lokasi, dan penyimpanan secara terpusat.', 'asset', 'bg-teal-50 text-teal-700'],
                        ['Import dan Export Excel', 'Mendukung import data aset melalui template serta export laporan aset sesuai filter yang digunakan.', 'inventory', 'bg-emerald-50 text-emerald-700'],
                        ['QR Code Aset dan Penyimpanan', 'Menghasilkan dan menampilkan QR Code untuk aset maupun tempat penyimpanan agar mudah diidentifikasi.', 'qr', 'bg-cyan-50 text-cyan-700'],
                        ['Scan QR Code', 'Memindai QR untuk membuka informasi aset atau penyimpanan dan membantu proses identifikasi data.', 'view', 'bg-blue-50 text-blue-700'],
                        ['Stock Opname', 'Mendukung sesi pemeriksaan inventaris, scan aset, import kondisi, dan pembaruan status pemeriksaan.', 'inventory', 'bg-amber-50 text-amber-700'],
                        ['Penggantian Alat Rusak', 'Menyediakan form publik, pengecekan status, verifikasi, dan tindak lanjut penggantian alat rusak.', 'report', 'bg-red-50 text-red-700'],
                        ['Laporan Aset', 'Menyediakan rekap data aset untuk kebutuhan monitoring, dokumentasi, dan pengambilan keputusan.', 'report', 'bg-slate-100 text-slate-700'],
                        ['Manajemen Pengguna', 'Admin dapat mengelola pengguna sistem dan pembagian peran akses.', 'user', 'bg-orange-50 text-orange-700'],
                    ] as [$title, $description, $iconName, $tone])
                        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-orange-200 hover:shadow-xl hover:shadow-slate-200/70">
                            <span class="flex h-12 w-12 items-center justify-center rounded-2xl {{ $tone }}">
                                <x-nav-icon :name="$iconName" />
                            </span>
                            <h3 class="mt-5 text-lg font-bold text-slate-950">{{ $title }}</h3>
                            <p class="mt-3 text-sm leading-6 text-slate-600">{{ $description }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section id="alur" class="bg-white px-4 py-16 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div class="mx-auto max-w-3xl text-center">
                    <p class="text-sm font-semibold uppercase tracking-wide text-orange-700">Alur Sistem</p>
                    <h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-950 sm:text-4xl">Alur Kerja SIMASET</h2>
                    <p class="mt-4 text-base leading-7 text-slate-600">SIMASET membantu proses pengelolaan aset mulai dari pendataan, identifikasi QR Code, monitoring kondisi, sampai pelaporan.</p>
                </div>

                <div class="relative mt-12 grid gap-5 md:grid-cols-4">
                    <div class="absolute left-0 right-0 top-10 hidden h-px bg-gradient-to-r from-transparent via-orange-200 to-transparent md:block" aria-hidden="true"></div>
                    @foreach ([
                        ['01', 'Kelola Master Data', 'Unit kerja, kategori, lokasi, dan tempat penyimpanan disiapkan sebagai dasar pengelolaan aset.', 'category'],
                        ['02', 'Input atau Import Aset', 'Data aset dapat dicatat melalui form sistem atau diimpor menggunakan template Excel.', 'asset-plus'],
                        ['03', 'Generate dan Scan QR', 'QR Code digunakan untuk identifikasi aset maupun penyimpanan secara cepat dan terdokumentasi.', 'qr'],
                        ['04', 'Monitoring dan Laporan', 'Data kondisi, stock opname, penggantian alat, dan laporan aset dapat dipantau sesuai hak akses.', 'report'],
                    ] as [$number, $title, $description, $iconName])
                        <article class="relative rounded-2xl border border-slate-200 bg-slate-50 p-5 shadow-sm transition hover:-translate-y-1 hover:bg-white hover:shadow-xl hover:shadow-slate-200/70">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-950 text-orange-200 shadow-lg shadow-slate-200">
                                <x-nav-icon :name="$iconName" />
                            </div>
                            <p class="mt-5 text-sm font-bold text-orange-700">{{ $number }}</p>
                            <h3 class="mt-2 text-lg font-bold text-slate-950">{{ $title }}</h3>
                            <p class="mt-3 text-sm leading-6 text-slate-600">{{ $description }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section id="akses" class="bg-slate-50 px-4 py-16 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div class="grid gap-8 lg:grid-cols-[380px_minmax(0,1fr)] lg:items-start">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-wide text-orange-700">Akses Sistem</p>
                        <h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-950 sm:text-4xl">Akses Berdasarkan Peran</h2>
                        <p class="mt-4 text-base leading-7 text-slate-600">Sistem mendukung pengelolaan aset pada unit kerja di lingkungan STIKes Bogor Husada dengan pembagian akses sesuai peran.</p>
                    </div>

                    <div class="grid gap-5 md:grid-cols-3">
                        @foreach ([
                            ['Admin', 'Mengelola pengguna, unit, master data, aset, QR Code, stock opname, penggantian alat, dan laporan.', 'user'],
                            ['Pengelola Unit', 'Mengelola aset, kategori, lokasi, penyimpanan, QR Code, stock opname, dan tindak lanjut pada unit kerja.', 'unit'],
                            ['Kaprodi', 'Memantau data aset, hasil pemeriksaan, penggantian alat, dan laporan sebagai bahan evaluasi.', 'report'],
                        ] as [$title, $description, $iconName])
                            <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-orange-200 hover:shadow-xl hover:shadow-slate-200/70">
                                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-50 text-orange-700">
                                    <x-nav-icon :name="$iconName" />
                                </span>
                                <h3 class="mt-5 text-lg font-bold text-slate-950">{{ $title }}</h3>
                                <p class="mt-3 text-sm leading-6 text-slate-600">{{ $description }}</p>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-white px-4 py-16 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div class="relative overflow-hidden rounded-2xl bg-slate-950 p-8 text-white shadow-2xl shadow-slate-200 sm:p-10 lg:flex lg:items-center lg:justify-between lg:gap-10">
                    <div class="pointer-events-none absolute right-[-80px] top-[-80px] h-64 w-64 rounded-full bg-orange-400/20 blur-3xl"></div>
                    <div class="relative max-w-3xl">
                        <p class="text-sm font-semibold uppercase tracking-wide text-orange-200">SIMASET SBH</p>
                        <h2 class="mt-3 text-3xl font-bold tracking-tight sm:text-4xl">Siap Mengelola Aset Secara Lebih Terstruktur?</h2>
                        <p class="mt-4 text-sm leading-6 text-slate-300 sm:text-base">Masuk ke dashboard SIMASET SBH untuk mulai mengelola data aset, melakukan identifikasi aset, dan memantau kondisi aset institusi.</p>
                    </div>
                    <div class="relative mt-7 flex w-full flex-col gap-3 sm:w-auto sm:flex-row lg:mt-0">
                        <a href="{{ $loginUrl }}" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-orange-500 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-orange-950/20 transition hover:-translate-y-0.5 hover:bg-orange-400 sm:w-auto">
                            <x-nav-icon name="dashboard" />
                            Masuk ke Dashboard
                        </a>
                        <a href="{{ $statusUrl }}" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl border border-white/[0.15] bg-white/10 px-5 py-3 text-sm font-bold text-white backdrop-blur transition hover:-translate-y-0.5 hover:bg-white/[0.15] sm:w-auto">
                            <x-nav-icon name="view" />
                            Cek Status Penggantian
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="border-t border-slate-800 bg-slate-950 px-4 py-8 text-slate-400 sm:px-6 lg:px-8">
        <div class="mx-auto flex max-w-7xl flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-base font-bold text-white">SIMASET SBH</p>
                <p class="mt-1 text-sm">Sistem Informasi Manajemen Aset</p>
                <p class="mt-1 text-sm">STIKes Bogor Husada</p>
            </div>
            <p class="text-sm">&copy; 2026 SIMASET SBH - STIKes Bogor Husada. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
