<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi {{ ucfirst($jenis ?? 'Keseluruhan') }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9pt; color: #1a1a1a; padding: 20px; }

        /* HEADER */
        .kop { display: flex; align-items: center; border-bottom: 3px solid #1e40af; padding-bottom: 10px; margin-bottom: 8px; }
        .kop-text { flex: 1; }
        .kop-text h1 { font-size: 14pt; color: #1e40af; font-weight: bold; }
        .kop-text p  { font-size: 8pt; color: #555; }
        .kop-badge { background: #1e40af; color: #fff; padding: 4px 12px; border-radius: 4px; font-size: 8pt; font-weight: bold; }

        .meta { margin: 8px 0 14px; font-size: 8pt; color: #555; }
        .meta span { margin-right: 18px; }
        .meta strong { color: #1a1a1a; }

        .filter-info { background: #eff6ff; border-left: 3px solid #3b82f6; padding: 5px 10px; font-size: 8pt; margin-bottom: 12px; color: #1e40af; }

        /* TABLE */
        table { width: 100%; border-collapse: collapse; margin-top: 4px; font-size: 8pt; }
        thead tr { background: #1e40af; color: #fff; }
        thead th { padding: 6px 5px; text-align: left; font-weight: bold; }
        tbody tr:nth-child(even) { background: #f0f7ff; }
        tbody tr:hover { background: #dbeafe; }
        tbody td { padding: 5px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }

        .badge-masuk  { background:#dcfce7; color:#15803d; padding:1px 6px; border-radius:3px; font-weight:bold; font-size:7.5pt; }
        .badge-keluar { background:#fee2e2; color:#b91c1c; padding:1px 6px; border-radius:3px; font-weight:bold; font-size:7.5pt; }

        .summary { margin-top: 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius:4px; padding: 8px 12px; font-size: 8.5pt; }
        .summary table { margin-top: 0; }
        .summary td { border: none; padding: 2px 6px; }
        .summary .label { color: #555; font-weight: bold; }

        .footer { margin-top: 20px; border-top: 1px solid #e5e7eb; padding-top: 6px; font-size: 7.5pt; color: #888; display: flex; justify-content: space-between; }
    </style>
</head>
<body>

    {{-- KOP SURAT --}}
    <div class="kop">
        <div class="kop-text">
            <h1>BADAN PENANGGULANGAN BENCANA DAERAH</h1>
            <p>Sistem Informasi Manajemen Logistik &amp; Inventory (SIDARLOG)</p>
        </div>
        <div class="kop-badge">LAPORAN RESMI</div>
    </div>

    <h2 style="font-size:11pt; color:#1e40af; margin:6px 0 2px;">
        LAPORAN BARANG {{ strtoupper($jenis ?? 'SEMUA TRANSAKSI') }}
    </h2>

    <div class="meta">
        <span><strong>Tanggal Cetak:</strong> {{ date('d/m/Y H:i') }}</span>
        <span><strong>Total Data:</strong> {{ $transactions->count() }} transaksi</span>
    </div>

    @if(!empty($filterInfo))
    <div class="filter-info">&#128269; Filter aktif: {{ $filterInfo }}</div>
    @endif

    <table>
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
                    @if($tx->pihakKedua)  <div>{{ $tx->pihakKedua->nama_pihak }}</div> @endif
                    @if(!$tx->pihakKesatu && !$tx->pihakKedua) - @endif
                </td>
                <td style="font-size:7.5pt;">{{ $tx->referenceBap?->nomor_ba ?? '-' }}</td>
                <td style="font-size:7.5pt;">{{ $tx->keterangan ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="10" style="text-align:center; color:#888; padding:20px;">
                    Tidak ada data transaksi.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- SUMMARY --}}
    <div class="summary" style="margin-top:14px;">
        <strong>Ringkasan:</strong>
        <table style="width:auto; margin-top:4px;">
            <tr>
                <td class="label">Total Masuk</td>
                <td>: {{ $transactions->where('jenis','masuk')->count() }} transaksi
                    ({{ $transactions->where('jenis','masuk')->sum('jumlah_barang_kecil') }} unit kecil)
                </td>
            </tr>
            <tr>
                <td class="label">Total Keluar</td>
                <td>: {{ $transactions->where('jenis','keluar')->count() }} transaksi
                    ({{ $transactions->where('jenis','keluar')->sum('jumlah_barang_kecil') }} unit kecil)
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <span>Dicetak oleh SIDARLOG &mdash; Sistem Manajemen Logistik BPBD</span>
        <span>{{ date('d/m/Y H:i:s') }}</span>
    </div>

</body>
</html>
