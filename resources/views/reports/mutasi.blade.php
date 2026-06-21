<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Mutasi Antar Gudang</title>
    <style>
        @page { margin: 1.5cm 2cm; }
        * { box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 9pt; color: #1a1a1a; margin: 0; padding: 0; }

        .kop-table { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        .kop-logo-cell { width: 90px; vertical-align: middle; text-align: center; }
        .kop-logo { width: 78px; height: auto; }
        .kop-logo-placeholder .logo-circle {
            width: 70px; height: 70px; border-radius: 50%;
            background: #3730a3; display: flex; align-items: center;
            justify-content: center; margin: 0 auto;
        }
        .kop-text-cell { vertical-align: middle; text-align: center; padding: 4px 0; }
        .kop-instansi-atas { font-size: 10pt; font-weight: normal; }
        .kop-instansi-nama { font-size: 14pt; font-weight: bold; text-transform: uppercase; }
        .kop-instansi-info { font-size: 8pt; color: #333; margin-top: 1px; }
        .kop-divider-top { border-top: 4px solid #4f46e5; border-bottom: 1.5px solid #4f46e5; margin-top: 5px; height: 2px; }

        .report-title { text-align: center; margin: 10px 0 4px; }
        .report-title h2 { font-size: 12pt; margin: 0; text-decoration: underline; text-transform: uppercase; color: #4f46e5; }
        .meta { margin: 6px 0 6px; font-size: 8pt; color: #555; }
        .meta span { margin-right: 16px; }
        .meta strong { color: #1a1a1a; }
        .filter-info { background: #eef2ff; border-left: 3px solid #6366f1; padding: 4px 8px; font-size: 8pt; margin-bottom: 10px; color: #3730a3; }

        table.data-table { width: 100%; border-collapse: collapse; font-size: 8pt; }
        table.data-table thead tr { background: #4f46e5; color: #fff; }
        table.data-table thead th { padding: 5px 4px; text-align: left; font-weight: bold; }
        table.data-table tbody tr:nth-child(even) { background: #eef2ff; }
        table.data-table tbody td { padding: 4px; border-bottom: 1px solid #c7d2fe; vertical-align: top; }

        .badge-approved { background:#dcfce7; color:#15803d; padding:1px 5px; border-radius:3px; font-weight:bold; font-size:7.5pt; }
        .badge-pending  { background:#fef9c3; color:#854d0e; padding:1px 5px; border-radius:3px; font-weight:bold; font-size:7.5pt; }
        .badge-rejected { background:#fee2e2; color:#b91c1c; padding:1px 5px; border-radius:3px; font-weight:bold; font-size:7.5pt; }
        .arrow { color: #6366f1; font-weight: bold; }

        .summary { margin-top: 12px; background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 4px; padding: 7px 10px; font-size: 8.5pt; }
        .summary table { width: auto; border: none; }
        .summary td { border: none; padding: 2px 8px; }
        .summary .lbl { color: #444; font-weight: bold; width: 180px; }

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
        <h2>Laporan Mutasi Antar Gudang</h2>
    </div>

    <div class="meta">
        <span><strong>Tanggal Cetak:</strong> {{ date('d/m/Y H:i') }}</span>
        <span><strong>Total Mutasi:</strong> {{ $mutations->count() }} record</span>
        <span><strong>Total Unit Dipindah:</strong> {{ number_format($mutations->sum('jumlah_barang_kecil')) }} unit</span>
    </div>

    @if(!empty($filterInfo))
    <div class="filter-info">&#128269; Filter aktif: {{ $filterInfo }}</div>
    @endif

    <table class="data-table">
        <thead>
            <tr>
                <th style="width:22px">No</th>
                <th style="width:95px">No. Mutasi</th>
                <th>Barang</th>
                <th>Asal &#8594; Tujuan</th>
                <th style="width:55px; text-align:right">Jumlah</th>
                <th style="width:65px; text-align:center">Status</th>
                <th style="width:65px">Tanggal</th>
                <th>Keterangan</th>
                <th>Petugas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mutations as $index => $mut)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td style="font-family:monospace; font-size:7.5pt; font-weight:bold;">{{ $mut->no_mutasi }}</td>
                <td>
                    <strong>{{ $mut->barang->nama_barang ?? '-' }}</strong><br>
                    <small style="color:#888; font-family:monospace; font-size:7pt;">{{ $mut->barang->kode_barang ?? '' }}</small>
                </td>
                <td style="font-size:7.5pt;">
                    {{ $mut->gudangAsal->nama_gudang ?? '-' }}
                    <span class="arrow">&#8594;</span>
                    {{ $mut->gudangTujuan->nama_gudang ?? '-' }}
                </td>
                <td style="text-align:right; font-weight:bold;">
                    {{ number_format($mut->jumlah_barang_kecil) }}
                    <span style="color:#888; font-size:7pt;">unit</span>
                </td>
                <td style="text-align:center;">
                    @if($mut->status === 'approved')
                        <span class="badge-approved">APPROVED</span>
                    @elseif($mut->status === 'pending')
                        <span class="badge-pending">PENDING</span>
                    @else
                        <span class="badge-rejected">{{ strtoupper($mut->status) }}</span>
                    @endif
                </td>
                <td style="white-space:nowrap; font-size:7.5pt;">
                    {{ $mut->tgl_mutasi ? \Carbon\Carbon::parse($mut->tgl_mutasi)->format('d/m/Y') : '-' }}
                </td>
                <td style="font-size:7.5pt;">{{ $mut->keterangan ?? '-' }}</td>
                <td style="font-size:7.5pt;">{{ $mut->pembuat->nama_lengkap ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align:center; color:#888; padding:18px; font-style:italic;">
                    Tidak ada data mutasi gudang.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        <strong>Ringkasan Mutasi:</strong>
        <table>
            <tr>
                <td class="lbl">Total Record Mutasi</td>
                <td>: {{ $mutations->count() }} transaksi</td>
            </tr>
            <tr>
                <td class="lbl">Total Unit Dipindahkan</td>
                <td>: {{ number_format($mutations->sum('jumlah_barang_kecil')) }} unit kecil</td>
            </tr>
            <tr>
                <td class="lbl">Status Approved</td>
                <td>: {{ $mutations->where('status', 'approved')->count() }} record</td>
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
