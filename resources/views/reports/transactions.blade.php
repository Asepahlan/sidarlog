<!DOCTYPE html>
<html>
<head>
    <title>Laporan Transaksi</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .header { text-align: center; margin-bottom: 30px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 8pt; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN TRANSAKSI {{ strtoupper($jenis ?? 'KESELURUHAN') }}</h2>
        <p>Sistem Manajemen Logistik & Inventory Modern (SIDARLOG)</p>
        <hr>
        <p>Tanggal Cetak: {{ date('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No. Referensi</th>
                <th>Tanggal</th>
                <th>Tipe</th>
                <th>Barang</th>
                <th>Jumlah</th>
                <th>Gudang</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $index => $tx)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $tx->no_referensi }}</td>
                <td>{{ $tx->tgl_transaksi ? $tx->tgl_transaksi->format('d/m/Y H:i') : '-' }}</td>
                <td>{{ strtoupper($tx->jenis) }}</td>
                <td>{{ $tx->barang->nama_barang ?? '-' }}</td>
                <td>{{ $tx->jumlah_barang_kecil }} {{ $tx->barang->satuanKecil->nama_satuan ?? '' }}</td>
                <td>{{ $tx->gudang->nama_gudang ?? '-' }}</td>
                <td>{{ $tx->keterangan ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak secara otomatis oleh SIDARLOG System
    </div>
</body>
</html>
