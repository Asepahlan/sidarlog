<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Barang Inventory</title>
    <style>
        @page { margin: 1.5cm 2cm; }
        * { box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 9pt; color: #1a1a1a; margin: 0; padding: 0; }

        .kop-table { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        .kop-logo-cell { width: 90px; vertical-align: middle; text-align: center; }
        .kop-logo { width: 78px; height: auto; }
        .kop-logo-placeholder .logo-circle {
            width: 70px; height: 70px; border-radius: 50%;
            background: #1e3a5f; display: flex; align-items: center;
            justify-content: center; margin: 0 auto;
        }
        .kop-text-cell { vertical-align: middle; text-align: center; padding: 4px 0; }
        .kop-instansi-atas { font-size: 10pt; font-weight: normal; }
        .kop-instansi-nama { font-size: 14pt; font-weight: bold; text-transform: uppercase; }
        .kop-instansi-info { font-size: 8pt; color: #333; margin-top: 1px; }
        .kop-divider-top { border-top: 4px solid #15803d; border-bottom: 1.5px solid #15803d; margin-top: 5px; height: 2px; }

        .report-title { text-align: center; margin: 10px 0 4px; }
        .report-title h2 { font-size: 12pt; margin: 0; text-decoration: underline; text-transform: uppercase; color: #15803d; }
        .meta { margin: 6px 0 10px; font-size: 8pt; color: #555; }
        .meta span { margin-right: 16px; }
        .meta strong { color: #1a1a1a; }

        table.data-table { width: 100%; border-collapse: collapse; font-size: 8pt; }
        table.data-table thead tr { background: #15803d; color: #fff; }
        table.data-table thead th { padding: 5px 4px; text-align: left; font-weight: bold; }
        table.data-table tbody tr:nth-child(even) { background: #f0fdf4; }
        table.data-table tbody td { padding: 4px; border-bottom: 1px solid #d1fae5; vertical-align: top; }

        .badge-ok   { background:#dcfce7; color:#15803d; padding:1px 5px; border-radius:3px; font-size:7.5pt; font-weight:bold; }
        .badge-low  { background:#fee2e2; color:#b91c1c; padding:1px 5px; border-radius:3px; font-size:7.5pt; font-weight:bold; }
        .badge-warn { background:#fef9c3; color:#854d0e; padding:1px 5px; border-radius:3px; font-size:7.5pt; font-weight:bold; }

        .summary { margin-top: 12px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 4px; padding: 7px 10px; font-size: 8.5pt; }
        .summary table { width: auto; border: none; }
        .summary td { border: none; padding: 2px 6px; }
        .summary .lbl { color: #444; font-weight: bold; }

        .footer { margin-top: 16px; border-top: 1px solid #cbd5e1; padding-top: 5px; font-size: 7.5pt; color: #888; }
        .footer-inner { display: flex; justify-content: space-between; }

        .sig-section { margin-top: 30px; }
        .sig-table { width: 100%; border-collapse: collapse; }
        .sig-table td { width: 50%; vertical-align: top; text-align: center; border: none; padding: 0 10px; }
        .sig-name { margin-top: 50px; font-weight: bold; }
        .sig-name .underline { text-decoration: underline; }
    </style>
</head>
<body>
@php
    $logoSrc  = null;
    $logoPath = public_path('img/logo-daerah.png');
    if (file_exists($logoPath)) {
        $ext     = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
        $mime    = $ext === 'png' ? 'image/png' : 'image/jpeg';
        $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
    }
@endphp

    <table class="kop-table">
        <tr>
            <td class="kop-logo-cell">
                @if($logoSrc)
                    <img src="{{ $logoSrc }}" class="kop-logo" alt="Logo BPBD">
                @else
                    <div class="kop-logo-placeholder">
                        <div class="logo-circle">
                            <span style="font-size:20pt; color:#fff; font-weight:bold;">⚙</span>
                        </div>
                    </div>
                @endif
            </td>
            <td class="kop-text-cell">
                <div class="kop-instansi-atas">PEMERINTAH DAERAH KABUPATEN TASIKMALAYA</div>
                <div class="kop-instansi-nama">BADAN PENANGGULANGAN BENCANA DAERAH</div>
                <div class="kop-instansi-info">Jl. Otto Iskandardinata No. 19 Tasikmalaya Telp dan Fax (0265) 334111</div>
                <div class="kop-instansi-info">Email: bpbd@tasikmalayakab.go.id &nbsp;|&nbsp; TASIKMALAYA - 46113</div>
            </td>
        </tr>
    </table>
    <div class="kop-divider-top"></div>

    <div class="report-title">
        <h2>Laporan Data Master Barang Inventory</h2>
    </div>

    <div class="meta">
        <span><strong>Tanggal Cetak:</strong> {{ date('d/m/Y H:i') }}</span>
        <span><strong>Total Barang:</strong> {{ $items->count() }} item</span>
        <span><strong>Total Stok (unit kecil):</strong> {{ number_format($items->sum('stok_saat_ini_kecil')) }}</span>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width:22px">No</th>
                <th>Kode</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th style="text-align:right">Stok Kecil</th>
                <th>Satuan</th>
                <th style="text-align:right">Stok Besar</th>
                <th>Satuan</th>
                <th style="text-align:right">Stok Min</th>
                <th>Status</th>
                <th>Exp. Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td style="font-family:monospace; font-size:7.5pt;">{{ $item->kode_barang ?? '-' }}</td>
                <td><strong>{{ $item->nama_barang }}</strong></td>
                <td>{{ $item->kategori->nama_kategori ?? '-' }}</td>
                <td style="text-align:right; font-weight:bold;">{{ number_format($item->stok_saat_ini_kecil) }}</td>
                <td>{{ $item->satuanKecil->nama_satuan ?? '-' }}</td>
                <td style="text-align:right;">{{ number_format($item->stok_saat_ini_besar) }}</td>
                <td>{{ $item->satuanBesar->nama_satuan ?? '-' }}</td>
                <td style="text-align:right;">{{ $item->stok_minimal ?? '-' }}</td>
                <td>
                    @php $stokMin = $item->stok_minimal ?? 0; $stok = $item->stok_saat_ini_kecil; @endphp
                    @if($stok <= 0)
                        <span class="badge-low">HABIS</span>
                    @elseif($stokMin > 0 && $stok <= $stokMin)
                        <span class="badge-warn">RENDAH</span>
                    @else
                        <span class="badge-ok">AMAN</span>
                    @endif
                </td>
                <td style="white-space:nowrap; font-size:7.5pt;">
                    {{ $item->tgl_kadaluarsa ? $item->tgl_kadaluarsa->format('d/m/Y') : '-' }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="11" style="text-align:center; color:#888; padding:18px; font-style:italic;">Tidak ada data barang.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        <strong>Ringkasan Stok:</strong>
        <table>
            <tr>
                <td class="lbl">Total Jenis Barang</td>
                <td>: {{ $items->count() }} item</td>
            </tr>
            <tr>
                <td class="lbl">Stok Aman</td>
                <td>: {{ $items->filter(fn($i) => $i->stok_saat_ini_kecil > ($i->stok_minimal ?? 0))->count() }} item</td>
            </tr>
            <tr>
                <td class="lbl">Stok Rendah / Habis</td>
                <td>: {{ $items->filter(fn($i) => $i->stok_saat_ini_kecil <= ($i->stok_minimal ?? 0))->count() }} item</td>
            </tr>
        </table>
    </div>

    <div class="sig-section">
        <table class="sig-table">
            <tr>
                <td></td>
                <td>
                    <div>Tasikmalaya, {{ date('d/m/Y') }}</div>
                    <div>Kepala Pelaksana BPBD Kabupaten Tasikmalaya</div>
                    <div class="sig-name">
                        <span class="underline">RONI, A.Ks., M.M</span><br>
                        <span style="font-size:8pt; font-weight:normal;">NIP. 19690901 199303 1 004</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <div class="footer-inner">
            <span>Dicetak oleh SIDARLOG &mdash; Sistem Manajemen Logistik BPBD Kab. Tasikmalaya</span>
            <span>{{ date('d/m/Y H:i:s') }}</span>
        </div>
    </div>

</body>
</html>
