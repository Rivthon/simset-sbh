<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - SIMASET SBH</title>
    @include('partials.assets')
    <style>
        :root {
            color-scheme: light;
            --app-bg: #f8fafc;
            --app-card: #ffffff;
            --app-card-soft: #f8fafc;
            --app-border: #e2e8f0;
            --app-text: #0f172a;
            --app-muted: #64748b;
            --app-primary: #465fff;
        }

        html.dark {
            color-scheme: dark;
            --app-bg: #020617;
            --app-card: #0f172a;
            --app-card-soft: #111827;
            --app-border: #334155;
            --app-text: #f8fafc;
            --app-muted: #94a3b8;
            --app-primary: #6d7cff;
        }

        html[data-theme="light"] body {
            background-color: #f6f8fb !important;
            color: #1f2937 !important;
        }
        html[data-theme="light"] [data-app-shell],
        html[data-theme="light"] [data-app-content],
        html[data-theme="light"] main {
            background-color: transparent !important;
            color: #1f2937 !important;
        }
        html[data-theme="light"] aside,
        html[data-theme="light"] header,
        html[data-theme="light"] .bg-white {
            background-color: #ffffff !important;
        }
        html[data-theme="light"] .text-gray-900,
        html[data-theme="light"] .text-slate-900,
        html[data-theme="light"] .text-gray-800,
        html[data-theme="light"] .text-slate-800 {
            color: #111827 !important;
        }
        html.dark body { background-color: #020617; color: #e5e7eb; }
        html.dark [data-app-shell],
        html.dark [data-app-content],
        html.dark main { background-color: #020617 !important; }
        html.dark aside,
        html.dark header,
        html.dark main .bg-white,
        html.dark .bg-white { background-color: #111827 !important; }
        html.dark .bg-gray-50,
        html.dark .bg-slate-50 { background-color: #1f2937 !important; }
        html.dark .bg-gray-100,
        html.dark .bg-slate-100 { background-color: #0f172a !important; }
        html.dark .text-gray-900,
        html.dark .text-slate-900,
        html.dark .text-gray-800,
        html.dark .text-slate-800 { color: #f8fafc !important; }
        html.dark .text-gray-700,
        html.dark .text-slate-700,
        html.dark .text-gray-600,
        html.dark .text-slate-600 { color: #cbd5e1 !important; }
        html.dark .text-gray-500,
        html.dark .text-slate-500,
        html.dark .text-gray-400 { color: #94a3b8 !important; }
        html.dark .border-gray-100,
        html.dark .border-slate-100,
        html.dark .border-gray-200,
        html.dark .border-slate-200,
        html.dark .border-gray-300,
        html.dark .border-slate-300 { border-color: #334155 !important; }
        html.dark input,
        html.dark select,
        html.dark textarea { background-color: #0f172a !important; border-color: #334155 !important; color: #f8fafc !important; }
        html.dark table thead { background-color: #1f2937 !important; color: #cbd5e1 !important; }
        html.dark tbody,
        html.dark tr,
        html.dark .divide-gray-100 > :not([hidden]) ~ :not([hidden]),
        html.dark .divide-slate-100 > :not([hidden]) ~ :not([hidden]) { border-color: #334155 !important; }
        html.dark .shadow-sm,
        html.dark .shadow {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.18) !important;
        }
        html.dark .shadow-blue-50,
        html.dark .shadow-slate-100 {
            box-shadow: none !important;
        }
        html.dark .bg-blue-50 { background-color: rgba(70, 95, 255, 0.14) !important; }
        html.dark .bg-emerald-50 { background-color: rgba(16, 185, 129, 0.14) !important; }
        html.dark .bg-red-50 { background-color: rgba(239, 68, 68, 0.14) !important; }
        html.dark .bg-yellow-50,
        html.dark .bg-amber-50 { background-color: rgba(245, 158, 11, 0.15) !important; }
        html.dark .text-blue-700 { color: #93c5fd !important; }
        html.dark .text-emerald-700 { color: #6ee7b7 !important; }
        html.dark .text-red-700,
        html.dark .text-red-800 { color: #fca5a5 !important; }
        html.dark .text-yellow-700,
        html.dark .text-amber-700 { color: #fcd34d !important; }
        html.dark .border-blue-200 { border-color: rgba(96, 165, 250, 0.35) !important; }
        html.dark .border-emerald-200 { border-color: rgba(52, 211, 153, 0.35) !important; }
        html.dark .border-red-200 { border-color: rgba(248, 113, 113, 0.35) !important; }
        html.dark .border-yellow-200,
        html.dark .border-amber-200 { border-color: rgba(251, 191, 36, 0.35) !important; }
        header button span[class~="text-xl"][class~="leading-none"] { display: none; }

        body {
            background: var(--app-bg);
        }

        main > section,
        main > form,
        main > div.rounded-lg,
        main > div.rounded-xl,
        main > div.rounded-2xl {
            transition: background-color .2s ease, border-color .2s ease, color .2s ease;
        }

        table {
            border-collapse: separate;
            border-spacing: 0;
        }

        thead th {
            white-space: nowrap;
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: .02em;
            text-transform: uppercase;
        }

        tbody tr {
            transition: background-color .15s ease;
        }

        tbody tr:hover {
            background-color: #f8fafc;
        }

        html.dark tbody tr:hover {
            background-color: rgba(30, 41, 59, 0.72) !important;
        }

        input,
        select,
        textarea {
            min-height: 2.625rem;
            outline: none;
            transition: border-color .15s ease, box-shadow .15s ease, background-color .15s ease;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: var(--app-primary) !important;
            box-shadow: 0 0 0 3px rgba(70, 95, 255, .16) !important;
        }

        button,
        a {
            transition: background-color .15s ease, border-color .15s ease, color .15s ease, box-shadow .15s ease;
        }

        html.dark aside {
            background-color: #0f172a !important;
            border-color: #233044 !important;
        }

        html.dark aside .border-b,
        html.dark aside [class*="border-b"] {
            border-color: #233044 !important;
        }

        html.dark header {
            background-color: rgba(15, 23, 42, .96) !important;
            backdrop-filter: blur(12px);
        }

        html.dark nav a.bg-\[\#ecf3ff\] {
            background-color: rgba(70, 95, 255, .18) !important;
            color: #bfdbfe !important;
        }

        html.dark nav a .bg-white {
            background-color: rgba(15, 23, 42, .9) !important;
        }

        html.dark nav a:hover {
            background-color: rgba(30, 41, 59, .82) !important;
            color: #f8fafc !important;
        }

        html.dark .ring-slate-100 {
            --tw-ring-color: rgba(51, 65, 85, .55) !important;
        }

        html.dark input::placeholder,
        html.dark textarea::placeholder {
            color: #64748b !important;
        }

        html.dark option {
            background-color: #0f172a;
            color: #f8fafc;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: .375rem;
            border-radius: 9999px;
            border-width: 1px;
            padding: .25rem .625rem;
            font-size: .75rem;
            font-weight: 700;
            line-height: 1rem;
        }

        [x-cloak] {
            display: none !important;
        }

        @media (max-width: 767px) {
            html,
            body {
                max-width: 100%;
                overflow-x: hidden;
            }
            [data-app-shell],
            [data-app-content],
            main {
                min-width: 0;
                width: 100%;
            }
            input,
            select,
            textarea {
                font-size: 16px !important;
            }
            .overflow-hidden:has(> table) {
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
            }
            .overflow-hidden:has(> table) table,
            .overflow-x-auto table {
                min-width: 720px;
            }
            .mobile-full-button {
                display: flex !important;
                width: 100% !important;
                max-width: 100% !important;
                justify-content: center !important;
                box-sizing: border-box;
                white-space: nowrap;
            }
            .desktop-table {
                display: none !important;
            }
            .mobile-card-list {
                display: block !important;
            }
            .mobile-card-list .mobile-data-card {
                overflow-wrap: anywhere;
            }
        }
        @media (min-width: 768px) {
            .mobile-card-list {
                display: none !important;
            }
        }
    </style>
    <script>
        const savedTheme = localStorage.getItem('theme') === 'dark' ? 'dark' : 'light';
        document.documentElement.dataset.theme = savedTheme;

        if (savedTheme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('theme', {
                dark: document.documentElement.classList.contains('dark'),
                toggle() {
                    this.dark = !this.dark;
                    document.documentElement.classList.toggle('dark', this.dark);
                    document.documentElement.dataset.theme = this.dark ? 'dark' : 'light';
                    localStorage.setItem('theme', this.dark ? 'dark' : 'light');
                    document.dispatchEvent(new CustomEvent('theme-changed', { detail: { dark: this.dark } }));
                }
            });

            Alpine.store('sidebar', {
                mobileOpen: false,
                toggleMobile() { this.mobileOpen = !this.mobileOpen; },
                closeMobile() { this.mobileOpen = false; },
                handleResize() {
                    if (window.innerWidth >= 1280) {
                        this.mobileOpen = false;
                    }
                }
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const button = document.querySelector('[data-theme-toggle]');
            const sun = document.querySelector('[data-theme-sun]');
            const moon = document.querySelector('[data-theme-moon]');

            const syncIcon = () => {
                const dark = document.documentElement.classList.contains('dark');
                document.documentElement.dataset.theme = dark ? 'dark' : 'light';
                sun?.classList.toggle('hidden', !dark);
                moon?.classList.toggle('hidden', dark);
            };

            button?.addEventListener('click', () => {
                const dark = !document.documentElement.classList.contains('dark');
                document.documentElement.classList.toggle('dark', dark);
                document.documentElement.dataset.theme = dark ? 'dark' : 'light';
                localStorage.setItem('theme', dark ? 'dark' : 'light');
                syncIcon();
            });

            document.addEventListener('theme-changed', syncIcon);
            syncIcon();
        });
    </script>
</head>
<body class="min-h-full bg-gray-50 font-sans text-gray-800 transition-colors dark:bg-gray-950 dark:text-gray-100">
    <div x-data @resize.window="$store.sidebar.handleResize()" @keydown.escape.window="$store.sidebar.closeMobile()" data-app-shell class="min-h-screen bg-transparent xl:flex">
        <div x-cloak x-show="$store.sidebar.mobileOpen" x-transition.opacity @click="$store.sidebar.closeMobile()" class="fixed inset-0 z-40 bg-gray-900/50 backdrop-blur-[1px] xl:hidden"></div>

        <aside id="app-sidebar" class="fixed left-0 top-0 z-50 flex h-screen w-[280px] flex-col border-r border-gray-200 bg-white shadow-[8px_0_28px_rgba(15,23,42,0.04)] transition-transform duration-300"
            :aria-hidden="(!$store.sidebar.mobileOpen && window.innerWidth < 1280).toString()"
            :class="{
                'translate-x-0': $store.sidebar.mobileOpen,
                '-translate-x-full xl:translate-x-0': !$store.sidebar.mobileOpen
            }">
            <button type="button" @click="$store.sidebar.closeMobile()" class="absolute right-4 top-6 z-10 flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 shadow-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700 xl:hidden" aria-controls="app-sidebar" :aria-expanded="$store.sidebar.mobileOpen.toString()" title="Tutup sidebar">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="m15 6-6 6 6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="sr-only">Tutup sidebar</span>
            </button>

            <div class="flex h-[84px] items-center border-b border-gray-100 px-5 pr-14 xl:pr-5">
                <a href="{{ route('dashboard') }}" class="flex min-w-0 items-center gap-3">
                    <img src="{{ asset('images/logo-simaset-rounded.png') }}" alt="Logo SIMASET SBH" class="h-11 w-11 shrink-0 rounded-lg object-contain shadow-sm">
                    <span class="truncate text-xl font-bold tracking-tight text-gray-900">SIMASET SBH</span>
                </a>
            </div>

            <nav class="flex-1 overflow-y-auto px-4 py-5">
                @php
                    $user = auth()->user();
                    $pendingReplacementCount = \App\Models\ToolReplacementRequest::query()
                        ->visibleFor($user)
                        ->where('status', 'menunggu_verifikasi')
                        ->count();
                    $itemBase = 'group flex min-h-11 items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-blue-100';
                    $item = $itemBase.' text-gray-600 hover:bg-gray-50 hover:text-gray-900';
                    $active = $itemBase.' bg-[#ecf3ff] text-[#465fff] shadow-sm shadow-blue-50';
                    $icon = 'flex h-8 w-8 shrink-0 items-center justify-center rounded-md bg-gray-50 text-gray-500 transition group-hover:bg-white group-hover:text-[#465fff]';
                    $activeIcon = 'flex h-8 w-8 shrink-0 items-center justify-center rounded-md bg-white text-[#465fff] shadow-sm';
                    $sectionClass = 'mb-2 border-b border-gray-100 pb-2 last:mb-0 last:border-b-0 last:pb-0';
                    $labelClass = 'flex w-full items-center px-3 py-2 text-[11px] font-bold uppercase tracking-wide text-gray-400';

                    $dashboardItem = ['label' => 'Dashboard', 'url' => route('dashboard'), 'icon' => 'dashboard', 'active' => request()->routeIs('dashboard*')];
                    $sections = [];

                    if ($user->isAdmin()) {
                        $sections[] = [
                            'label' => 'Master',
                            'items' => [
                                ['label' => 'Unit', 'url' => route('units.index'), 'icon' => 'unit', 'active' => request()->routeIs('units.*')],
                                ['label' => 'Kategori', 'url' => route('categories.index'), 'icon' => 'category', 'active' => request()->routeIs('categories.*')],
                                ['label' => 'Lokasi', 'url' => route('locations.index'), 'icon' => 'location', 'active' => request()->routeIs('locations.*')],
                                ['label' => 'Penyimpanan', 'url' => route('containers.index'), 'icon' => 'container', 'active' => request()->routeIs('containers.*')],
                                ['label' => 'Pengguna', 'url' => route('users.index'), 'icon' => 'user', 'active' => request()->routeIs('users.*')],
                            ],
                        ];
                    }

                    if ($user->isPengelola()) {
                        $sections[] = [
                            'label' => 'Master',
                            'items' => [
                                ['label' => 'Kategori', 'url' => route('categories.index'), 'icon' => 'category', 'active' => request()->routeIs('categories.*')],
                                ['label' => 'Lokasi', 'url' => route('locations.index'), 'icon' => 'location', 'active' => request()->routeIs('locations.*')],
                                ['label' => 'Penyimpanan', 'url' => route('containers.index'), 'icon' => 'container', 'active' => request()->routeIs('containers.*')],
                            ],
                        ];
                    }

                    if ($user->hasRole(['admin', 'pengelola', 'pimpinan'])) {
                        $operationItems = [
                            ['label' => 'Aset', 'url' => route('assets.index'), 'icon' => 'asset', 'active' => request()->routeIs('assets.*')],
                        ];

                        $operationItems[] = [
                            'label' => 'Penggantian Alat Rusak',
                            'url' => route('tool-replacements.index'),
                            'icon' => 'report',
                            'active' => request()->routeIs('tool-replacements.*'),
                            'badge' => $pendingReplacementCount,
                        ];

                        $operationItems[] = [
                            'label' => 'Scan QR',
                            'url' => route('scan.qr.index'),
                            'icon' => 'qr',
                            'active' => request()->routeIs('scan.qr.*'),
                        ];

                        $operationItems[] = [
                            'label' => 'Stock Opname',
                            'url' => route('inventory-checks.index'),
                            'icon' => 'inventory',
                            'active' => request()->routeIs('inventory-checks.*'),
                        ];

                        $operationItems[] = [
                            'label' => 'Laporan Aset',
                            'url' => route('reports.index'),
                            'icon' => 'report',
                            'active' => request()->routeIs('reports.*'),
                        ];

                        if ($user->isPimpinan()) {
                            $sections[] = [
                                'label' => null,
                                'items' => $operationItems,
                                'flat' => true,
                            ];
                        } else {
                            $sections[] = [
                                'label' => 'Operasional',
                                'items' => $operationItems,
                            ];
                        }
                    }
                @endphp

                <div class="{{ $sectionClass }}">
                    <a href="{{ $dashboardItem['url'] }}" title="{{ $dashboardItem['label'] }}" @click="$store.sidebar.closeMobile()" class="{{ $dashboardItem['active'] ? $active : $item }}">
                        <span class="{{ $dashboardItem['active'] ? $activeIcon : $icon }}"><x-nav-icon :name="$dashboardItem['icon']" /></span>
                        <span class="truncate">{{ $dashboardItem['label'] }}</span>
                    </a>
                </div>

                @foreach ($sections as $section)
                    <div class="{{ $sectionClass }}">
                        @unless ($section['flat'] ?? false)
                            <div class="{{ $labelClass }}">
                                <span>{{ $section['label'] }}</span>
                            </div>
                        @endunless
                        <div class="space-y-1">
                            @foreach ($section['items'] as $entry)
                                <a href="{{ $entry['url'] }}" title="{{ $entry['label'] }}" @click="$store.sidebar.closeMobile()" class="{{ $entry['active'] ? $active : $item }}">
                                    <span class="{{ $entry['active'] ? $activeIcon : $icon }}"><x-nav-icon :name="$entry['icon']" /></span>
                                    <span class="truncate">{{ $entry['label'] }}</span>
                                    @if (($entry['badge'] ?? 0) > 0)
                                        <span class="ml-auto rounded-full bg-amber-100 px-2 py-0.5 text-[11px] font-bold text-amber-700">{{ $entry['badge'] }}</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </nav>
        </aside>

        <div data-app-content class="min-w-0 flex-1 bg-transparent xl:ml-[280px] xl:w-[calc(100%_-_280px)]">
            <header class="sticky top-0 z-30 border-b border-gray-200 bg-white/95 backdrop-blur">
                <div class="flex min-h-16 flex-col gap-3 px-4 py-3 sm:flex-row sm:items-center sm:justify-between xl:px-6">
                    <div class="flex min-w-0 items-center gap-3">
                        <button type="button" @click="$store.sidebar.toggleMobile()" class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 xl:hidden" aria-controls="app-sidebar" :aria-expanded="$store.sidebar.mobileOpen.toString()" title="Buka/tutup sidebar">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            </svg>
                            <span class="sr-only">Buka/tutup sidebar</span>
                        </button>
                        <div class="min-w-0">
                            <p class="text-xs text-gray-500">{{ auth()->user()->unit?->name ?? 'Semua Unit' }}</p>
                            <h1 class="truncate text-lg font-semibold text-gray-900">@yield('title', 'Dashboard')</h1>
                        </div>
                    </div>

                    <div class="flex w-full flex-wrap items-center gap-2 sm:w-auto sm:justify-end sm:gap-3">
                        <div class="hidden rounded-lg border border-gray-200 bg-gray-50 px-4 py-2 text-sm font-medium text-gray-500 md:block">
                            {{ now()->format('d M Y') }}
                        </div>
                        <button type="button" data-theme-toggle class="flex h-11 w-11 items-center justify-center rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800" title="Ubah mode tampilan">
                            <svg data-theme-moon class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M20 15.2A8.5 8.5 0 0 1 8.8 4a7.8 7.8 0 1 0 11.2 11.2Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <svg data-theme-sun class="hidden h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 4V2M12 22v-2M4 12H2M22 12h-2M5.64 5.64 4.22 4.22M19.78 19.78l-1.42-1.42M18.36 5.64l1.42-1.42M4.22 19.78l1.42-1.42" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                <path d="M12 16a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="1.8"/>
                            </svg>
                        </button>
                        <div class="flex min-w-0 flex-1 items-center gap-3 rounded-lg border border-gray-200 bg-white py-1 pl-1 pr-3 shadow-sm sm:flex-none">
                            <span class="flex h-9 w-9 items-center justify-center rounded-md bg-[#465fff] text-sm font-bold uppercase text-white">{{ substr(auth()->user()->name, 0, 1) }}</span>
                            <div class="min-w-0 text-sm">
                                <p class="truncate font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-500">{{ match (auth()->user()->role) { 'pengelola' => 'Laboran / Pengelola', 'pimpinan' => 'Kaprodi', default => ucfirst(auth()->user()->role) } }}</p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="rounded-lg border border-gray-200 px-3 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 sm:px-4">Logout</button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="mx-auto w-full max-w-[1536px] min-w-0 p-4 md:p-6 xl:p-7">
                @include('partials.flash')
                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
