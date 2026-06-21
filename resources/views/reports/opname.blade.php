<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Stock Opname</title>
    <style>
        @page { margin: 1.5cm 2cm; }
        * { box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 9pt; color: #1a1a1a; margin: 0; padding: 0; }

        .kop-table { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        .kop-logo-cell { width: 90px; vertical-align: middle; text-align: center; }
        .kop-logo { width: 78px; height: auto; }
        .kop-logo-placeholder .logo-circle {
            width: 70px; height: 70px; border-radius: 50%;
            background: #92400e; display: flex; align-items: center;
            justify-content: center; margin: 0 auto;
        }
        .kop-text-cell { vertical-align: middle; text-align: center; padding: 4px 0; }
        .kop-instansi-atas { font-size: 10pt; font-weight: normal; }
        .kop-instansi-nama { font-size: 14pt; font-weight: bold; text-transform: uppercase; }
        .kop-instansi-info { font-size: 8pt; color: #333; margin-top: 1px; }
        .kop-divider-top { border-top: 4px solid #d97706; border-bottom: 1.5px solid #d97706; margin-top: 5px; height: 2px; }

        .report-title { text-align: center; margin: 10px 0 4px; }
        .report-title h2 { font-size: 12pt; margin: 0; text-decoration: underline; text-transform: uppercase; color: #d97706; }
        .meta { margin: 6px 0 6px; font-size: 8pt; color: #555; }
        .meta span { margin-right: 16px; }
        .meta strong { color: #1a1a1a; }
        .filter-info { background: #fffbeb; border-left: 3px solid #f59e0b; padding: 4px 8px; font-size: 8pt; margin-bottom: 10px; color: #92400e; }

        table.data-table { width: 100%; border-collapse: collapse; font-size: 8pt; }
        table.data-table thead tr { background: #d97706; color: #fff; }
        table.data-table thead th { padding: 5px 4px; text-align: left; font-weight: bold; }
        table.data-table tbody tr:nth-child(even) { background: #fffbeb; }
        table.data-table tbody td { padding: 4px; border-bottom: 1px solid #fde68a; vertical-align: top; }

        .badge        { padding: 1px 5px; border-radius: 3px; font-weight: bold; font-size: 7.5pt; }
        .badge-match  { background: #dcfce7; color: #15803d; }
        .badge-plus   { background: #dbeafe; color: #1e40af; }
        .badge-minus  { background: #fee2e2; color: #b91c1c; }

        .summary { margin-top: 12px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 4px; padding: 7px 10px; font-size: 8.5pt; }
        .summary table { width: auto; border: none; }
        .summary td { border: none; padding: 2px 8px; }
        .summary .lbl { color: #444; font-weight: bold; width: 170px; }

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
    $selisihPlus  = $opnames->where('selisih', '>', 0)->count();
    $selisihMinus = $opnames->where('selisih', '<', 0)->count();
    $match        = $opnames->where('selisih', 0)->count();
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
        <h2>Laporan Stock Opname (Audit Stok)</h2>
    </div>

    <div class="meta">
        <span><strong>Tanggal Cetak:</strong> {{ date('d/m/Y H:i') }}</span>
        <span><strong>Total Record:</strong> {{ $opnames->count() }} opname</span>
        <span><strong>Match:</strong> {{ $match }}</span>
        <span><strong>Lebih:</strong> {{ $selisihPlus }}</span>
        <span><strong>Kurang:</strong> {{ $selisihMinus }}</span>
    </div>

    @if(!empty($filterInfo))
    <div class="filter-info">&#128269; Filter aktif: {{ $filterInfo }}</div>
    @endif

    <table class="data-table">
        <thead>
            <tr>
                <th style="width:22px">No</th>
                <th style="width:75px">Tanggal</th>
                <th>Barang</th>
                <th>Gudang</th>
                <th style="width:55px; text-align:right">Sistem</th>
                <th style="width:55px; text-align:right">Fisik</th>
                <th style="width:65px; text-align:center">Selisih</th>
                <th>Keterangan</th>
                <th>Auditor</th>
            </tr>
        </thead>
        <tbody>
            @forelse($opnames as $index => $op)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td style="white-space:nowrap; font-size:7.5pt;">
                    {{ $op->created_at ? $op->created_at->format('d/m/Y') : '-' }}<br>
                    <span style="color:#888">{{ $op->created_at ? $op->created_at->format('H:i') : '' }}</span>
                </td>
                <td>
                    <strong>{{ $op->barang->nama_barang ?? '-' }}</strong><br>
                    <small style="color:#888; font-family:monospace; font-size:7pt;">{{ $op->barang->kode_barang ?? '' }}</small>
                </td>
                <td>{{ $op->gudang->nama_gudang ?? '-' }}</td>
                <td style="text-align:right; font-weight:bold;">{{ number_format($op->stok_sistem) }}</td>
                <td style="text-align:right; font-weight:bold;">{{ number_format($op->stok_fisik) }}</td>
                <td style="text-align:center;">
                    @if($op->selisih == 0)
                        <span class="badge badge-match">Match</span>
                    @elseif($op->selisih > 0)
                        <span class="badge badge-plus">+{{ $op->selisih }}</span>
                    @else
                        <span class="badge badge-minus">{{ $op->selisih }}</span>
                    @endif
                </td>
                <td style="font-size:7.5pt;">{{ $op->keterangan ?? '-' }}</td>
                <td style="font-size:7.5pt;">{{ $op->pengguna->nama_lengkap ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align:center; color:#888; padding:18px; font-style:italic;">
                    Tidak ada data riwayat stock opname.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        <strong>Ringkasan Hasil Opname:</strong>
        <table>
            <tr>
                <td class="lbl">&#9989; Stok Match (selisih = 0)</td>
                <td>: {{ $match }} item</td>
            </tr>
            <tr>
                <td class="lbl">&#128200; Stok Lebih (selisih +)</td>
                <td>: {{ $selisihPlus }} item (total lebih: {{ $opnames->where('selisih', '>', 0)->sum('selisih') }} unit)</td>
            </tr>
            <tr>
                <td class="lbl">&#128201; Stok Kurang (selisih -)</td>
                <td>: {{ $selisihMinus }} item (total kurang: {{ abs($opnames->where('selisih', '<', 0)->sum('selisih')) }} unit)</td>
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
