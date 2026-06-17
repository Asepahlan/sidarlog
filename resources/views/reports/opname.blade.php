<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Stock Opname</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9pt; color: #1a1a1a; padding: 20px; }

        .kop { display: flex; align-items: center; border-bottom: 3px solid #d97706; padding-bottom: 10px; margin-bottom: 8px; }
        .kop h1 { font-size: 13pt; color: #d97706; font-weight: bold; }
        .kop p  { font-size: 8pt; color: #555; margin-top: 2px; }
        .kop-badge { background: #d97706; color: #fff; padding: 4px 12px; border-radius: 4px; font-size: 8pt; font-weight: bold; margin-left: auto; }

        .meta { margin: 8px 0 14px; font-size: 8pt; color: #555; }
        .meta span { margin-right: 18px; }
        .meta strong { color: #1a1a1a; }

        .filter-info { background: #fffbeb; border-left: 3px solid #f59e0b; padding: 5px 10px; font-size: 8pt; margin-bottom: 12px; color: #92400e; }

        table { width: 100%; border-collapse: collapse; font-size: 8pt; }
        thead tr { background: #d97706; color: #fff; }
        thead th { padding: 6px 5px; text-align: left; font-weight: bold; }
        tbody tr:nth-child(even) { background: #fffbeb; }
        tbody td { padding: 5px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }

        .badge        { padding: 1px 6px; border-radius: 3px; font-weight: bold; font-size: 7.5pt; }
        .badge-match  { background: #dcfce7; color: #15803d; }
        .badge-plus   { background: #dbeafe; color: #1e40af; }
        .badge-minus  { background: #fee2e2; color: #b91c1c; }

        .summary { margin-top: 14px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 4px; padding: 8px 12px; font-size: 8.5pt; }
        .summary td { border: none; padding: 2px 8px; }
        .summary .label { color: #555; font-weight: bold; width: 150px; }

        .footer { margin-top: 20px; border-top: 1px solid #e5e7eb; padding-top: 6px; font-size: 7.5pt; color: #888; display: flex; justify-content: space-between; }
    </style>
</head>
<body>

    <div class="kop">
        <div>
            <h1>BADAN PENANGGULANGAN BENCANA DAERAH</h1>
            <p>Sistem Informasi Manajemen Logistik &amp; Inventory (SIDARLOG)</p>
        </div>
        <div class="kop-badge">LAPORAN RESMI</div>
    </div>

    <h2 style="font-size:11pt; color:#d97706; margin:6px 0 2px;">LAPORAN STOCK OPNAME (AUDIT STOK)</h2>

    <div class="meta">
        <span><strong>Tanggal Cetak:</strong> {{ date('d/m/Y H:i') }}</span>
        <span><strong>Total Record:</strong> {{ $opnames->count() }} opname</span>
        @php
            $selisihPlus  = $opnames->where('selisih', '>', 0)->count();
            $selisihMinus = $opnames->where('selisih', '<', 0)->count();
            $match        = $opnames->where('selisih', 0)->count();
        @endphp
        <span><strong>Match:</strong> {{ $match }}</span>
        <span><strong>Lebih:</strong> {{ $selisihPlus }}</span>
        <span><strong>Kurang:</strong> {{ $selisihMinus }}</span>
    </div>

    @if(!empty($filterInfo))
    <div class="filter-info">&#128269; Filter aktif: {{ $filterInfo }}</div>
    @endif

    <table>
        <thead>
            <tr>
                <th style="width:22px">No</th>
                <th style="width:80px">Tanggal</th>
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
                <td colspan="9" style="text-align:center; color:#888; padding:20px; font-style:italic;">
                    Tidak ada data riwayat stock opname.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- SUMMARY --}}
    <div class="summary" style="margin-top:14px;">
        <strong>Ringkasan Hasil Opname:</strong>
        <table style="width:auto; margin-top:4px;">
            <tr>
                <td class="label">&#9989; Stok Match (selisih = 0)</td>
                <td>: {{ $match }} item</td>
            </tr>
            <tr>
                <td class="label">&#128200; Stok Lebih (selisih +)</td>
                <td>: {{ $selisihPlus }} item (total lebih: {{ $opnames->where('selisih', '>', 0)->sum('selisih') }} unit)</td>
            </tr>
            <tr>
                <td class="label">&#128201; Stok Kurang (selisih -)</td>
                <td>: {{ $selisihMinus }} item (total kurang: {{ abs($opnames->where('selisih', '<', 0)->sum('selisih')) }} unit)</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <span>Dicetak oleh SIDARLOG &mdash; Sistem Manajemen Logistik BPBD</span>
        <span>{{ date('d/m/Y H:i:s') }}</span>
    </div>

</body>
</html>
