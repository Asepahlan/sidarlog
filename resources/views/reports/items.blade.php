<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Persediaan Logistik</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 1.0cm 1.2cm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 8.5pt;
            color: #000;
            line-height: 1.25;
            margin: 0;
            padding: 0;
        }

        /* Kop Surat */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2px;
        }
        .kop-logo-cell {
            width: 80px;
            vertical-align: middle;
            text-align: left;
            padding-bottom: 3px;
        }
        .kop-logo {
            width: 65px;
            height: auto;
        }
        .kop-text-cell {
            vertical-align: middle;
            text-align: center;
            padding-right: 80px; /* balance logo width */
            padding-bottom: 3px;
        }
        .kop-pemda {
            font-size: 11pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .kop-instansi {
            font-size: 13pt;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-top: 1px;
        }
        .kop-detail {
            font-size: 8pt;
            margin-top: 1px;
        }
        .kop-divider {
            border-top: 2.5px solid #000;
            border-bottom: 0.8px solid #000;
            height: 2px;
            margin-bottom: 10px;
        }

        /* Judul Laporan */
        .report-header {
            text-align: center;
            margin-bottom: 10px;
        }
        .report-title {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0 0 2px 0;
            letter-spacing: 0.5px;
        }
        .report-subtitle {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0 0 2px 0;
        }
        .report-period {
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
        }

        /* Tabel Data */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            margin-bottom: 8px;
            font-size: 7.5pt;
        }
        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 3px 4px;
            vertical-align: middle;
        }
        .data-table th {
            font-weight: bold;
            text-align: center;
            font-size: 7.5pt;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .font-bold {
            font-weight: bold;
        }
        
        /* Auto repeat header on page break */
        thead {
            display: table-header-group;
        }
        tr {
            page-break-inside: avoid;
        }

        /* Terbilang Section */
        .terbilang-box {
            font-size: 8pt;
            margin-bottom: 15px;
            font-style: italic;
        }

        /* Tanda Tangan */
        .sig-section {
            width: 100%;
            margin-top: 15px;
            page-break-inside: avoid;
        }
        .sig-table {
            width: 100%;
            border-collapse: collapse;
        }
        .sig-table td {
            width: 50%;
            vertical-align: top;
            text-align: center;
            padding: 0 40px;
        }
        .sig-date {
            margin-bottom: 3px;
        }
        .sig-role {
            font-weight: bold;
            min-height: 30px;
        }
        .sig-space {
            height: 45px;
        }
        .sig-name {
            font-weight: bold;
            text-decoration: underline;
        }
        .sig-nip {
            font-size: 8pt;
            margin-top: 1px;
        }
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

    {{-- KOP SURAT --}}
    <table class="kop-table">
        <tr>
            <td class="kop-logo-cell">
                @if($logoSrc)
                    <img src="{{ $logoSrc }}" class="kop-logo" alt="Logo">
                @else
                    <div style="font-size: 16pt; font-weight: bold; border: 1px solid #000; padding: 5px; text-align: center;">[LOGO]</div>
                @endif
            </td>
            <td class="kop-text-cell">
                <div class="kop-pemda">Pemerintah Daerah Kabupaten Tasikmalaya</div>
                <div class="kop-instansi">Badan Penanggulangan Bencana Daerah</div>
                <div class="kop-detail">Jl. Otto Iskandardinata No. 19 Tasikmalaya Telp dan Fax (0265) 334111</div>
                <div class="kop-detail">Email: bpbd@tasikmalayakab.go.id &nbsp;|&nbsp; TASIKMALAYA - 46113</div>
            </td>
        </tr>
    </table>
    <div class="kop-divider"></div>

    {{-- JUDUL LAPORAN --}}
    <div class="report-header">
        <div class="report-title">DAFTAR PERSEDIAAN LOGISTIK</div>
        <div class="report-subtitle">BADAN PENANGGULANGAN BENCANA DAERAH KABUPATEN TASIKMALAYA</div>
        <div class="report-period">BULAN {{ $monthNameUpper }}</div>
    </div>

    <div style="font-size: 8pt; margin-bottom: 5px;">
        <strong>Gudang:</strong> {{ $namaGudang }}
    </div>

    {{-- TABEL DATA PERSIS FOTO --}}
    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 3%">No</th>
                <th rowspan="2" style="width: 18%">Nama Barang</th>
                <th rowspan="2" style="width: 10%">Sumber Anggaran</th>
                <th rowspan="2" style="width: 9%">(Diterima) Tanggal</th>
                <th colspan="4" style="width: 16%">Mutasi Barang</th>
                <th rowspan="2" style="width: 6%">Satuan</th>
                <th rowspan="2" style="width: 8%">Harga Satuan<br>(Rp)</th>
                <th colspan="4" style="width: 24%">Jumlah Nilai Barang</th>
                <th rowspan="2" style="width: 6%">Keterangan</th>
            </tr>
            <tr>
                <th style="width: 4%">Saldo Awal</th>
                <th style="width: 4%">Masuk</th>
                <th style="width: 4%">Keluar</th>
                <th style="width: 4%">Sisa Akhir</th>
                <th style="width: 6%">Saldo Awal<br>(Rp)</th>
                <th style="width: 6%">Nilai Pemasukan<br>(Rp)</th>
                <th style="width: 6%">Nilai Pengeluaran<br>(Rp)</th>
                <th style="width: 6%">Sisa Akhir<br>(Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totSaldoAwal = 0;
                $totMasukVal = 0;
                $totKeluarVal = 0;
                $totSisaAkhirVal = 0;
            @endphp
            @forelse($items as $index => $item)
                @php
                    $totSaldoAwal += $item->saldo_awal_val;
                    $totMasukVal += $item->masuk_val;
                    $totKeluarVal += $item->keluar_val;
                    $totSisaAkhirVal += $item->sisa_akhir_val;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td class="text-center">
                        {{ $item->sumberAnggaran ? $item->sumberAnggaran->nama_sumber . ' ' . $item->sumberAnggaran->tahun_anggaran : '-' }}
                    </td>
                    <td class="text-center">
                        {{ $item->created_at ? $item->created_at->translatedFormat('d M Y') : '-' }}
                    </td>
                    
                    {{-- Mutasi --}}
                    <td class="text-center">{{ number_format($item->saldo_awal) }}</td>
                    <td class="text-center">{{ number_format($item->masuk) }}</td>
                    <td class="text-center">{{ number_format($item->keluar) }}</td>
                    <td class="text-center font-bold">{{ number_format($item->sisa_akhir) }}</td>
                    
                    <td class="text-center">{{ $item->satuanKecil->nama_satuan ?? 'Pcs' }}</td>
                    <td class="text-right">{{ number_format($item->harga_satuan_kecil, 2, ',', '.') }}</td>
                    
                    {{-- Nilai Mutasi Rp --}}
                    <td class="text-right">{{ number_format($item->saldo_awal_val, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->masuk_val, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->keluar_val, 2, ',', '.') }}</td>
                    <td class="text-right font-bold">{{ number_format($item->sisa_akhir_val, 2, ',', '.') }}</td>
                    
                    <td>{{ $item->deskripsi ? Str::limit($item->deskripsi, 25) : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="15" class="text-center" style="padding: 15px; font-style: italic; color: #555;">
                        Tidak ada data persediaan logistik pada periode & gudang ini.
                    </td>
                </tr>
            @endforelse
            
            {{-- Grand Totals --}}
            <tr class="font-bold" style="background-color: #f9f9f9;">
                <td colspan="10" class="text-center">Jumlah Total</td>
                <td class="text-right">{{ number_format($totSaldoAwal, 2, ',', '.') }}</td>
                <td class="text-right">{{ number_format($totMasukVal, 2, ',', '.') }}</td>
                <td class="text-right">{{ number_format($totKeluarVal, 2, ',', '.') }}</td>
                <td class="text-right">{{ number_format($totSisaAkhirVal, 2, ',', '.') }}</td>
                <td>&nbsp;</td>
            </tr>
        </tbody>
    </table>

    {{-- Terbilang Box --}}
    <div class="terbilang-box">
        <strong>Terbilang:</strong> {{ $terbilangText }}
    </div>

    {{-- TANDA TANGAN --}}
    <div class="sig-section">
        <table class="sig-table">
            <tr>
                <td>
                    <div class="sig-date">&nbsp;</div>
                    <div class="sig-role">Mengetahui,<br>Kepala Bidang Kedaruratan dan Logistik<br>BPBD Kabupaten Tasikmalaya</div>
                    <div class="sig-space"></div>
                    <div>
                        <span class="sig-name">{{ $kabidNama }}</span>
                        @if($kabidNip && $kabidNip !== '-')
                            <div class="sig-nip">NIP. {{ $kabidNip }}</div>
                        @endif
                    </div>
                </td>
                <td>
                    <div class="sig-date">Tasikmalaya, {{ $tglCetak }}</div>
                    <div class="sig-role">Pengelola Logistik,<br>&nbsp;<br>&nbsp;</div>
                    <div class="sig-space"></div>
                    <div>
                        <span class="sig-name">{{ $pengelolaNama }}</span>
                        @if($pengelolaNip && $pengelolaNip !== '-')
                            <div class="sig-nip">NIP. {{ $pengelolaNip }}</div>
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
