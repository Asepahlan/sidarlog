<!DOCTYPE html>
<html>
<head>
    <title>Laporan Data Barang</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { bg-color: #f2f2f2; font-weight: bold; }
        .header { text-align: center; margin-bottom: 30px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 8pt; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN DATA BARANG INVENTORY</h2>
        <p>Sistem Manajemen Logistik & Inventory Modern (SIDARLOG)</p>
        <hr>
        <p>Tanggal Cetak: {{ date('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Stok Kecil</th>
                <th>Stok Besar</th>
                <th>Exp. Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->kode_barang }}</td>
                <td>{{ $item->nama_barang }}</td>
                <td>{{ $item->kategori->nama_kategori ?? '-' }}</td>
                <td>{{ $item->current_stock_kecil }} {{ $item->satuanKecil->nama_satuan ?? '' }}</td>
                <td>{{ $item->current_stock_besar }} {{ $item->satuanBesar->nama_satuan ?? '' }}</td>
                <td>{{ $item->tgl_kadaluarsa ? $item->tgl_kadaluarsa->format('d/m/Y') : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak secara otomatis oleh SIDARLOG System
    </div>
</body>
</html>
