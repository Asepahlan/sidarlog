<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Barang Inventory</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9pt; color: #1a1a1a; padding: 20px; }

        /* KOP */
        .kop { display: flex; align-items: center; border-bottom: 3px solid #1e40af; padding-bottom: 10px; margin-bottom: 8px; }
        .kop h1 { font-size: 13pt; color: #1e40af; font-weight: bold; }
        .kop p  { font-size: 8pt; color: #555; margin-top: 2px; }
        .kop-badge { background: #1e40af; color: #fff; padding: 4px 12px; border-radius: 4px; font-size: 8pt; font-weight: bold; margin-left: auto; }

        .meta { margin: 8px 0 14px; font-size: 8pt; color: #555; }
        .meta span { margin-right: 18px; }
        .meta strong { color: #1a1a1a; }

        /* TABLE */
        table { width: 100%; border-collapse: collapse; font-size: 8pt; }
        thead tr { background: #1e40af; color: #fff; }
        thead th { padding: 6px 5px; text-align: left; font-weight: bold; }
        tbody tr:nth-child(even) { background: #f0f7ff; }
        tbody td { padding: 5px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }

        .badge-ok     { background: #dcfce7; color: #15803d; padding: 1px 5px; border-radius: 3px; font-size: 7.5pt; font-weight: bold; }
        .badge-low    { background: #fee2e2; color: #b91c1c; padding: 1px 5px; border-radius: 3px; font-size: 7.5pt; font-weight: bold; }
        .badge-warn   { background: #fef9c3; color: #854d0e; padding: 1px 5px; border-radius: 3px; font-size: 7.5pt; font-weight: bold; }

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

    <h2 style="font-size:11pt; color:#1e40af; margin:6px 0 2px;">LAPORAN DATA MASTER BARANG INVENTORY</h2>

    <div class="meta">
        <span><strong>Tanggal Cetak:</strong> {{ date('d/m/Y H:i') }}</span>
        <span><strong>Total Barang:</strong> {{ $items->count() }} item</span>
        <span><strong>Total Stok (unit kecil):</strong> {{ $items->sum('stok_saat_ini_kecil') }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:22px">No</th>
                <th>Kode</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Stok Kecil</th>
                <th>Satuan</th>
                <th>Stok Besar</th>
                <th>Satuan</th>
                <th>Stok Min</th>
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
                <td colspan="11" style="text-align:center; color:#888; padding:20px;">Tidak ada data barang.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <span>Dicetak oleh SIDARLOG &mdash; Sistem Manajemen Logistik BPBD</span>
        <span>{{ date('d/m/Y H:i:s') }}</span>
    </div>

</body>
</html>
