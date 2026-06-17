<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Mutasi Antar Gudang</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9pt; color: #1a1a1a; padding: 20px; }

        .kop { display: flex; align-items: center; border-bottom: 3px solid #4f46e5; padding-bottom: 10px; margin-bottom: 8px; }
        .kop h1 { font-size: 13pt; color: #4f46e5; font-weight: bold; }
        .kop p  { font-size: 8pt; color: #555; margin-top: 2px; }
        .kop-badge { background: #4f46e5; color: #fff; padding: 4px 12px; border-radius: 4px; font-size: 8pt; font-weight: bold; margin-left: auto; }

        .meta { margin: 8px 0 14px; font-size: 8pt; color: #555; }
        .meta span { margin-right: 18px; }
        .meta strong { color: #1a1a1a; }

        .filter-info { background: #eef2ff; border-left: 3px solid #6366f1; padding: 5px 10px; font-size: 8pt; margin-bottom: 12px; color: #3730a3; }

        table { width: 100%; border-collapse: collapse; font-size: 8pt; }
        thead tr { background: #4f46e5; color: #fff; }
        thead th { padding: 6px 5px; text-align: left; font-weight: bold; }
        tbody tr:nth-child(even) { background: #eef2ff; }
        tbody td { padding: 5px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }

        .badge-approved { background: #dcfce7; color: #15803d; padding: 1px 6px; border-radius: 3px; font-weight: bold; font-size: 7.5pt; }
        .badge-pending  { background: #fef9c3; color: #854d0e; padding: 1px 6px; border-radius: 3px; font-weight: bold; font-size: 7.5pt; }
        .badge-rejected { background: #fee2e2; color: #b91c1c; padding: 1px 6px; border-radius: 3px; font-weight: bold; font-size: 7.5pt; }

        .arrow { color: #6366f1; font-weight: bold; }

        .summary { margin-top: 14px; background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 4px; padding: 8px 12px; font-size: 8.5pt; }
        .summary td { border: none; padding: 2px 8px; }
        .summary .label { color: #555; font-weight: bold; width: 160px; }

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

    <h2 style="font-size:11pt; color:#4f46e5; margin:6px 0 2px;">LAPORAN MUTASI ANTAR GUDANG</h2>

    <div class="meta">
        <span><strong>Tanggal Cetak:</strong> {{ date('d/m/Y H:i') }}</span>
        <span><strong>Total Mutasi:</strong> {{ $mutations->count() }} record</span>
        <span><strong>Total Unit Dipindah:</strong> {{ number_format($mutations->sum('jumlah_barang_kecil')) }} unit</span>
    </div>

    @if(!empty($filterInfo))
    <div class="filter-info">&#128269; Filter aktif: {{ $filterInfo }}</div>
    @endif

    <table>
        <thead>
            <tr>
                <th style="width:22px">No</th>
                <th style="width:100px">No. Mutasi</th>
                <th>Barang</th>
                <th>Asal &#8594; Tujuan</th>
                <th style="width:60px; text-align:right">Jumlah</th>
                <th style="width:65px; text-align:center">Status</th>
                <th style="width:70px">Tanggal</th>
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
                <td colspan="9" style="text-align:center; color:#888; padding:20px; font-style:italic;">
                    Tidak ada data mutasi gudang.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- SUMMARY --}}
    <div class="summary" style="margin-top:14px;">
        <strong>Ringkasan Mutasi:</strong>
        <table style="width:auto; margin-top:4px;">
            <tr>
                <td class="label">Total Record Mutasi</td>
                <td>: {{ $mutations->count() }} transaksi</td>
            </tr>
            <tr>
                <td class="label">Total Unit Dipindahkan</td>
                <td>: {{ number_format($mutations->sum('jumlah_barang_kecil')) }} unit kecil</td>
            </tr>
            <tr>
                <td class="label">Status Approved</td>
                <td>: {{ $mutations->where('status', 'approved')->count() }} record</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <span>Dicetak oleh SIDARLOG &mdash; Sistem Manajemen Logistik BPBD</span>
        <span>{{ date('d/m/Y H:i:s') }}</span>
    </div>

</body>
</html>
