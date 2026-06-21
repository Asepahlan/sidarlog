<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi {{ ucfirst($jenis ?? 'Keseluruhan') }}</title>
    <style>
        @page { margin: 1.5cm 2cm; }
        * { box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 9pt; color: #1a1a1a; margin: 0; padding: 0; }

        /* ─── KOP ─── */
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
        .kop-divider-top { border-top: 4px solid #1e3a5f; border-bottom: 1.5px solid #1e3a5f; margin-top: 5px; height: 2px; }

        /* ─── TITLE ─── */
        .report-title { text-align: center; margin: 10px 0 4px; }
        .report-title h2 { font-size: 12pt; margin: 0; text-decoration: underline; text-transform: uppercase; }
        .meta { margin: 6px 0 10px; font-size: 8pt; color: #555; }
        .meta span { margin-right: 16px; }
        .meta strong { color: #1a1a1a; }
        .filter-info { background: #eff6ff; border-left: 3px solid #3b82f6; padding: 4px 8px; font-size: 8pt; margin-bottom: 10px; color: #1e40af; }

        /* ─── TABLE ─── */
        table.data-table { width: 100%; border-collapse: collapse; margin-top: 4px; font-size: 8pt; }
        table.data-table thead tr { background: #1e3a5f; color: #fff; }
        table.data-table thead th { padding: 5px 4px; text-align: left; font-weight: bold; }
        table.data-table tbody tr:nth-child(even) { background: #f0f6ff; }
        table.data-table tbody td { padding: 4px; border-bottom: 1px solid #dde3ea; vertical-align: top; }

        .badge-masuk  { background:#dcfce7; color:#15803d; padding:1px 5px; border-radius:3px; font-weight:bold; font-size:7.5pt; }
        .badge-keluar { background:#fee2e2; color:#b91c1c; padding:1px 5px; border-radius:3px; font-weight:bold; font-size:7.5pt; }

        /* ─── SUMMARY ─── */
        .summary { margin-top: 12px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 4px; padding: 7px 10px; font-size: 8.5pt; }
        .summary table { width: auto; border: none; }
        .summary td { border: none; padding: 2px 6px; }
        .summary .lbl { color: #444; font-weight: bold; }

        /* ─── FOOTER ─── */
        .footer { margin-top: 16px; border-top: 1px solid #cbd5e1; padding-top: 5px; font-size: 7.5pt; color: #888; }
        .footer-inner { display: flex; justify-content: space-between; }

        /* ─── SIGNATURE ─── */
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

    {{-- ═══ KOP SURAT ═══ --}}
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
        <h2>Laporan Barang {{ ucfirst($jenis ?? 'Semua Transaksi') }}</h2>
    </div>

    <div class="meta">
        <span><strong>Tanggal Cetak:</strong> {{ date('d/m/Y H:i') }}</span>
        <span><strong>Total Data:</strong> {{ $transactions->count() }} transaksi</span>
        @if($jenis)
        <span><strong>Jenis:</strong> {{ strtoupper($jenis) }}</span>
        @endif
    </div>

    @if(!empty($filterInfo))
    <div class="filter-info">&#128269; Filter aktif: {{ $filterInfo }}</div>
    @endif

    <table class="data-table">
        <thead>
            <tr>
                <th style="width:22px">No</th>
                <th>No. Referensi</th>
                <th>Tanggal</th>
                <th>Tipe</th>
                <th>Barang</th>
                <th>Jumlah</th>
                <th>Gudang</th>
                <th>Pihak Terkait</th>
                <th>No. BAP</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $index => $tx)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td style="font-family:monospace; font-size:7.5pt;">{{ $tx->no_referensi ?? '-' }}</td>
                <td style="white-space:nowrap">{{ $tx->tgl_transaksi ? \Carbon\Carbon::parse($tx->tgl_transaksi)->format('d/m/Y') : '-' }}</td>
                <td>
                    @if($tx->jenis === 'masuk')
                        <span class="badge-masuk">MASUK</span>
                    @elseif($tx->jenis === 'keluar')
                        <span class="badge-keluar">KELUAR</span>
                    @else
                        <span>{{ strtoupper($tx->jenis) }}</span>
                    @endif
                </td>
                <td>
                    {{ $tx->barang->nama_barang ?? '-' }}
                    @if($tx->barang?->kode_barang)
                        <br><small style="color:#888;font-size:7pt;">{{ $tx->barang->kode_barang }}</small>
                    @endif
                </td>
                <td style="white-space:nowrap">
                    {{ $tx->jumlah_barang_kecil ?? 0 }} {{ $tx->barang?->satuanKecil?->nama_satuan ?? '' }}
                    @if($tx->jumlah_barang_besar)
                        <br><small>{{ $tx->jumlah_barang_besar }} {{ $tx->barang?->satuanBesar?->nama_satuan ?? '' }}</small>
                    @endif
                </td>
                <td>{{ $tx->gudang->nama_gudang ?? '-' }}</td>
                <td style="font-size:7.5pt;">
                    @if($tx->pihakKesatu) <div>{{ $tx->pihakKesatu->nama_pihak }}</div> @endif
                    @if($tx->pihakKedua)  <div>{{ $tx->pihakKedua->nama_pihak }}</div>  @endif
                    @if($tx->penerima_penyerah) <div>{{ $tx->penerima_penyerah }}</div> @endif
                    @if(!$tx->pihakKesatu && !$tx->pihakKedua && !$tx->penerima_penyerah) - @endif
                </td>
                <td style="font-size:7.5pt;">{{ $tx->referenceBap?->nomor_ba ?? '-' }}</td>
                <td style="font-size:7.5pt;">{{ $tx->keterangan ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="10" style="text-align:center; color:#888; padding:18px; font-style:italic;">
                    Tidak ada data transaksi.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- SUMMARY --}}
    <div class="summary">
        <strong>Ringkasan:</strong>
        <table>
            <tr>
                <td class="lbl">Total Masuk</td>
                <td>: {{ $transactions->where('jenis','masuk')->count() }} transaksi
                    ({{ number_format($transactions->where('jenis','masuk')->sum('jumlah_barang_kecil')) }} unit kecil)
                </td>
            </tr>
            <tr>
                <td class="lbl">Total Keluar</td>
                <td>: {{ $transactions->where('jenis','keluar')->count() }} transaksi
                    ({{ number_format($transactions->where('jenis','keluar')->sum('jumlah_barang_kecil')) }} unit kecil)
                </td>
            </tr>
        </table>
    </div>

    {{-- TANDA TANGAN --}}
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
