<style>
    #report-pdf-area {
        display: none;
    }

    @media print {
        @page {
            margin: 12mm;
            size: A4 portrait;
        }

        html,
        body {
            background: #ffffff !important;
            color: #111827 !important;
        }

        aside,
        header,
        nav,
        form,
        button,
        input,
        select,
        .print-hidden,
        .print\:hidden,
        #report-print-area,
        [data-app-content] > header {
            display: none !important;
        }

        [data-app-shell],
        [data-app-content],
        main {
            display: block !important;
            width: 100% !important;
            max-width: none !important;
            min-width: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            background: #ffffff !important;
        }

        body * {
            visibility: hidden;
        }

        #report-pdf-area,
        #report-pdf-area * {
            visibility: visible;
        }

        #report-pdf-area {
            display: block !important;
            position: absolute;
            inset: 0 auto auto 0;
            width: 100%;
            background: #ffffff !important;
            color: #111827 !important;
            font-family: Arial, Helvetica, sans-serif !important;
            font-size: 10px !important;
            line-height: 1.35 !important;
        }

        #report-pdf-area .pdf-cover {
            margin-bottom: 8px;
            padding: 10px 12px;
            border: 1px solid #e5e7eb;
            border-left: 5px solid #2563eb;
            border-radius: 6px;
            background: #f8fafc !important;
        }

        #report-pdf-area .pdf-brand {
            margin: 0 0 4px;
            color: #2563eb !important;
            font-size: 10px !important;
            font-weight: 700 !important;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        #report-pdf-area .pdf-title {
            margin: 0;
            color: #111827 !important;
            font-size: 17px !important;
            font-weight: 800 !important;
            line-height: 1.2 !important;
            text-transform: uppercase;
        }

        #report-pdf-area .pdf-subtitle {
            margin: 4px 0 0;
            color: #6b7280 !important;
            font-size: 10px !important;
        }

        #report-pdf-area .pdf-blue-line {
            height: 2px;
            margin-top: 8px;
            background: #2563eb !important;
        }

        #report-pdf-area .pdf-two-col {
            width: 100%;
            border-collapse: separate !important;
            border-spacing: 8px 0 !important;
            table-layout: fixed !important;
            border: 0 !important;
        }

        #report-pdf-area .pdf-two-col > tbody > tr > td {
            width: 50%;
            padding: 0 !important;
            border: 0 !important;
            vertical-align: top !important;
        }

        #report-pdf-area .pdf-panel {
            margin-bottom: 8px;
            padding: 8px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            background: #ffffff !important;
            break-inside: avoid;
        }

        #report-pdf-area .pdf-section {
            margin-top: 8px;
            break-inside: avoid;
        }

        #report-pdf-area .pdf-section-title {
            margin: 0 0 5px;
            padding-left: 6px;
            border-left: 3px solid #2563eb;
            color: #111827 !important;
            font-size: 11px !important;
            font-weight: 800 !important;
        }

        #report-pdf-area table.pdf-table {
            width: 100% !important;
            border-collapse: collapse !important;
            table-layout: fixed !important;
            background: #ffffff !important;
        }

        #report-pdf-area .pdf-table thead {
            display: table-header-group;
        }

        #report-pdf-area .pdf-table tr {
            page-break-inside: avoid;
        }

        #report-pdf-area .pdf-table th {
            border: 1px solid #e5e7eb !important;
            background: #eff6ff !important;
            color: #1e3a8a !important;
            padding: 5px 6px !important;
            font-size: 9px !important;
            font-weight: 800 !important;
            letter-spacing: .03em;
            text-transform: uppercase;
            vertical-align: top !important;
            word-wrap: break-word !important;
            overflow-wrap: break-word !important;
            white-space: normal !important;
        }

        #report-pdf-area .pdf-table td {
            border: 1px solid #e5e7eb !important;
            color: #111827 !important;
            padding: 5px 6px !important;
            font-size: 10px !important;
            vertical-align: top !important;
            word-wrap: break-word !important;
            overflow-wrap: break-word !important;
            white-space: normal !important;
        }

        #report-pdf-area .pdf-meta th {
            width: 38%;
            background: #f8fafc !important;
            color: #374151 !important;
        }

        #report-pdf-area .pdf-number {
            text-align: right;
            font-weight: 700;
        }

        #report-pdf-area .pdf-summary {
            width: 100%;
            border-collapse: separate !important;
            border-spacing: 6px !important;
            table-layout: fixed !important;
            border: 0 !important;
        }

        #report-pdf-area .pdf-summary td {
            width: 25%;
            padding: 7px 8px !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 5px;
            background: #ffffff !important;
            vertical-align: top;
        }

        #report-pdf-area .summary-label {
            margin: 0;
            color: #6b7280 !important;
            font-size: 8.5px !important;
            font-weight: 700;
            letter-spacing: .03em;
            text-transform: uppercase;
        }

        #report-pdf-area .summary-value {
            margin: 3px 0 0;
            color: #111827 !important;
            font-size: 17px !important;
            font-weight: 800 !important;
        }

        #report-pdf-area .summary-note {
            margin: 2px 0 0;
            color: #6b7280 !important;
            font-size: 8.5px !important;
        }

        #report-pdf-area .accent-blue { border-top: 3px solid #2563eb !important; }
        #report-pdf-area .accent-green { border-top: 3px solid #16a34a !important; }
        #report-pdf-area .accent-yellow { border-top: 3px solid #d97706 !important; }
        #report-pdf-area .accent-red { border-top: 3px solid #dc2626 !important; }
        #report-pdf-area .accent-gray { border-top: 3px solid #6b7280 !important; }

        #report-pdf-area .pdf-chip {
            display: inline-block;
            min-width: 48px;
            padding: 2px 6px;
            border-radius: 999px;
            color: #ffffff !important;
            font-size: 9px !important;
            font-weight: 700;
            text-align: center;
        }

        #report-pdf-area .chip-green { background: #16a34a !important; }
        #report-pdf-area .chip-yellow { background: #d97706 !important; }
        #report-pdf-area .chip-red { background: #dc2626 !important; }
        #report-pdf-area .chip-gray { background: #6b7280 !important; }

        #report-pdf-area .pdf-desc {
            margin: 0 0 6px;
            color: #6b7280 !important;
            font-size: 9px !important;
        }

        #report-pdf-area .pdf-footer {
            position: fixed;
            right: 0;
            bottom: -5mm;
            left: 0;
            padding-top: 4px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280 !important;
            font-size: 8.5px !important;
            text-align: center;
        }

        #report-pdf-area .pdf-page-number::after {
            content: counter(page);
        }
    }
</style>

<div id="report-pdf-area">
    <section class="pdf-cover">
        <p class="pdf-brand">SIMASET SBH</p>
        <h1 class="pdf-title">{{ ($reportScope ?? 'attention') === 'all' ? 'Laporan Aset Laboratorium' : 'Laporan Aset Perlu Perhatian' }}</h1>
        <p class="pdf-subtitle">STIKes Bogor Husada</p>
        <div class="pdf-blue-line"></div>
    </section>

    <section class="pdf-panel">
        <h2 class="pdf-section-title">Informasi Laporan</h2>
        <table class="pdf-table pdf-meta">
            <tbody>
                @foreach ($reportMeta as [$label, $value])
                    <tr>
                        <th>{{ $label }}</th>
                        <td>{{ $value }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>

    <section class="pdf-section">
        <h2 class="pdf-section-title">Ringkasan Utama</h2>
        <table class="pdf-summary">
            <tbody>
                <tr>
                    @foreach (array_slice($summaryRows, 0, 4) as [$label, $value, $description])
                        @php
                            $accent = match ($label) {
                                'Baik' => 'accent-green',
                                'Sedang' => 'accent-yellow',
                                'Rusak', 'Hilang', 'Perlu Perhatian' => 'accent-red',
                                default => 'accent-blue',
                            };
                        @endphp
                        <td class="{{ $accent }}">
                            <p class="summary-label">{{ $label }}</p>
                            <p class="summary-value">{{ number_format($value) }}</p>
                            <p class="summary-note">{{ $description ?: 'Data aset' }}</p>
                        </td>
                    @endforeach
                </tr>
                <tr>
                    @foreach (array_slice($summaryRows, 4) as [$label, $value, $description])
                        @php
                            $accent = match ($label) {
                                'Baik' => 'accent-green',
                                'Sedang' => 'accent-yellow',
                                'Rusak', 'Hilang', 'Perlu Perhatian' => 'accent-red',
                                default => 'accent-blue',
                            };
                        @endphp
                        <td class="{{ $accent }}">
                            <p class="summary-label">{{ $label }}</p>
                            <p class="summary-value">{{ number_format($value) }}</p>
                            <p class="summary-note">{{ $description ?: 'Data aset' }}</p>
                        </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </section>

    <table class="pdf-two-col">
        <tbody>
            <tr>
                @foreach (array_slice($recapSections, 0, 2) as [$title, $items, $relation])
                    <td>
                        <section class="pdf-panel">
                            <h2 class="pdf-section-title">{{ $title }}</h2>
                            <table class="pdf-table">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th style="width: 20%;">Data</th>
                                        <th style="width: 22%;">{{ $quantityColumnLabel }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($items as $item)
                                        <tr>
                                            <td>{{ $item->{$relation}?->name ?? ($relation === 'container' ? 'Tanpa Penyimpanan' : '-') }}</td>
                                            <td class="pdf-number">{{ number_format($item->total) }}</td>
                                            <td class="pdf-number">{{ number_format($item->quantity_total) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" style="text-align: center;">Data belum tersedia.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </section>
                    </td>
                @endforeach
            </tr>
            <tr>
                @foreach (array_slice($recapSections, 2, 2) as [$title, $items, $relation])
                    <td>
                        <section class="pdf-panel">
                            <h2 class="pdf-section-title">{{ $title }}</h2>
                            <table class="pdf-table">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th style="width: 20%;">Data</th>
                                        <th style="width: 22%;">{{ $quantityColumnLabel }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($items as $item)
                                        <tr>
                                            <td>{{ $item->{$relation}?->name ?? ($relation === 'container' ? 'Tanpa Penyimpanan' : '-') }}</td>
                                            <td class="pdf-number">{{ number_format($item->total) }}</td>
                                            <td class="pdf-number">{{ number_format($item->quantity_total) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" style="text-align: center;">Data belum tersedia.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </section>
                    </td>
                @endforeach
            </tr>
        </tbody>
    </table>

    <section class="pdf-panel">
        <h2 class="pdf-section-title">Rekap Kondisi Aset</h2>
        <table class="pdf-table">
            <thead>
                <tr>
                    <th>Kondisi</th>
                    <th style="width: 22%;">Data</th>
                    <th style="width: 24%;">{{ $quantityColumnLabel }}</th>
                    <th style="width: 26%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($displayConditionLabels as $value => $label)
                    @php
                        $chip = match ($value) {
                            'baik' => 'chip-green',
                            'sedang' => 'chip-yellow',
                            'rusak', 'hilang' => 'chip-red',
                            default => 'chip-gray',
                        };
                    @endphp
                    <tr>
                        <td>{{ $label }}</td>
                        <td class="pdf-number">{{ number_format($byCondition[$value] ?? 0) }}</td>
                        <td class="pdf-number">{{ number_format($byConditionQuantity[$value] ?? 0) }}</td>
                        <td><span class="pdf-chip {{ $chip }}">{{ $label }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>

    <section class="pdf-panel">
        <h2 class="pdf-section-title">Rincian Aset</h2>
        <p class="pdf-desc">{{ number_format($reportAssets->count()) }} data aset sesuai filter laporan.</p>
        <table class="pdf-table">
            <thead>
                <tr>
                    <th style="width: 15%;">Kode</th>
                    <th>Nama Aset</th>
                    <th style="width: 17%;">Unit</th>
                    <th style="width: 15%;">Lokasi</th>
                    <th style="width: 13%;">{{ $quantityColumnLabel }}</th>
                    <th style="width: 13%;">Kondisi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reportAssets as $asset)
                    <tr>
                        <td>{{ $asset->asset_code }}</td>
                        <td>{{ $asset->name }}<br><span style="color: #6b7280;">{{ $asset->category?->name ?? 'Tanpa kategori' }}</span></td>
                        <td>{{ $asset->unit?->name ?? '-' }}</td>
                        <td>{{ $asset->location?->name ?? 'Tanpa Lokasi' }}</td>
                        <td class="pdf-number">{{ number_format($reportQuantityForAsset($asset)) }} {{ $asset->satuan ?: 'unit' }}</td>
                        <td>{{ $asset->kondisi_aset_label }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align: center;">Tidak ada aset yang cocok dengan filter laporan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <div class="pdf-footer">
        Dokumen ini dihasilkan oleh SIMASET SBH. Dicetak {{ now()->format('d/m/Y H:i') }}. Halaman <span class="pdf-page-number"></span>
    </div>
</div>
