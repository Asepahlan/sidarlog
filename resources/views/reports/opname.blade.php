<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Hasil Stock Opname</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 1.2cm 1.5cm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 9.5pt;
            color: #000;
            line-height: 1.35;
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
            width: 85px;
            vertical-align: middle;
            text-align: left;
            padding-bottom: 5px;
        }
        .kop-logo {
            width: 75px;
            height: auto;
        }
        .kop-text-cell {
            vertical-align: middle;
            text-align: center;
            padding-right: 85px; /* balanced alignment with logo width */
            padding-bottom: 5px;
        }
        .kop-pemda {
            font-size: 11pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .kop-instansi {
            font-size: 14pt;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-top: 1px;
        }
        .kop-detail {
            font-size: 8.5pt;
            margin-top: 2px;
        }
        .kop-divider {
            border-top: 2.5px solid #000;
            border-bottom: 0.8px solid #000;
            height: 2px;
            margin-bottom: 12px;
        }

        /* Judul Laporan */
        .report-header {
            text-align: center;
            margin-bottom: 15px;
        }
        .report-title {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin: 0 0 2px 0;
        }
        .report-doc-number {
            font-size: 10pt;
            font-weight: normal;
            margin: 0 0 8px 0;
        }

        /* Informasi Metadata */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 9.5pt;
        }
        .meta-table td {
            padding: 2px 0;
            vertical-align: top;
        }
        .meta-label {
            width: 150px;
        }
        .meta-separator {
            width: 15px;
            text-align: center;
        }

        /* Tabel Data */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            margin-bottom: 15px;
            font-size: 9pt;
        }
        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 5px 6px;
            vertical-align: middle;
        }
        .data-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            font-size: 9pt;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        
        /* Auto repeat header on page break */
        thead {
            display: table-header-group;
        }
        tr {
            page-break-inside: avoid;
        }

        /* Tanda Tangan */
        .sig-section {
            width: 100%;
            margin-top: 20px;
            page-break-inside: avoid;
        }
        .sig-table {
            width: 100%;
            border-collapse: collapse;
        }
        .sig-table td {
            width: 33.33%;
            vertical-align: top;
            text-align: center;
            padding: 0 10px;
        }
        .sig-date {
            margin-bottom: 5px;
        }
        .sig-role {
            font-weight: bold;
            min-height: 35px;
        }
        .sig-space {
            height: 55px;
        }
        .sig-name {
            font-weight: bold;
            text-decoration: underline;
        }
        .sig-nip {
            font-size: 8.5pt;
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
                    <div style="font-size: 20pt; font-weight: bold; border: 1px solid #000; padding: 5px; text-align: center;">[LOGO]</div>
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
        <h2 class="report-title">LAPORAN HASIL STOCK OPNAME (AUDIT STOK)</h2>
        <div class="report-doc-number">Nomor : {{ $nomorSo ?? '......................................' }}</div>
    </div>

    {{-- METADATA LAPORAN --}}
    <table class="meta-table">
        <tr>
            <td class="meta-label">Nama Gudang</td>
            <td class="meta-separator">:</td>
            <td><strong>{{ $gudang->nama_gudang }}</strong></td>
        </tr>
        <tr>
            <td class="meta-label">Periode Stock Opname</td>
            <td class="meta-separator">:</td>
            <td>{{ $periode }}</td>
        </tr>
        <tr>
            <td class="meta-label">Tanggal Cetak / Laporan</td>
            <td class="meta-separator">:</td>
            <td>{{ $tglCetak }}</td>
        </tr>
    </table>

    {{-- TABEL DATA --}}
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 12%">Kode Barang</th>
                <th style="width: 25%">Nama Barang</th>
                <th style="width: 10%">Satuan</th>
                <th style="width: 10%">Stok Sistem</th>
                <th style="width: 10%">Stok Fisik</th>
                <th style="width: 10%">Selisih</th>
                <th style="width: 10%">Kondisi</th>
                <th style="width: 18%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($opnames as $index => $op)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $op->barang->kode_barang ?? '-' }}</td>
                    <td>{{ $op->barang->nama_barang ?? '-' }}</td>
                    <td class="text-center">{{ $op->barang->satuanKecil->nama_satuan ?? 'Pcs' }}</td>
                    <td class="text-right">{{ number_format($op->stok_sistem) }}</td>
                    <td class="text-right">{{ number_format($op->stok_fisik) }}</td>
                    <td class="text-right" style="font-weight: bold; color: {{ $op->selisih == 0 ? '#000' : ($op->selisih > 0 ? '#0d5885' : '#b91c1c') }}">
                        {{ $op->selisih > 0 ? '+' : '' }}{{ number_format($op->selisih) }}
                    </td>
                    <td class="text-center">{{ $op->kondisi_barang ?? 'Baik' }}</td>
                    <td>{{ $op->keterangan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 20px; font-style: italic; color: #555;">
                        Tidak ada data stock opname untuk dicetak.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- TANDA TANGAN --}}
    <div class="sig-section">
        <table class="sig-table">
            <tr>
                <td>
                    <div class="sig-date">&nbsp;</div>
                    <div class="sig-role">Petugas Stock Opname,</div>
                    <div class="sig-space"></div>
                    <div>
                        <span class="sig-name">{{ $petugas['nama'] }}</span>
                        @if(!empty($petugas['nip']) && $petugas['nip'] !== '-')
                            <div class="sig-nip">NIP. {{ $petugas['nip'] }}</div>
                        @endif
                    </div>
                </td>
                <td>
                    <div class="sig-date">&nbsp;</div>
                    <div class="sig-role">Mengetahui,<br>Kepala Pelaksana BPBD</div>
                    <div class="sig-space"></div>
                    <div>
                        <span class="sig-name">{{ $mengetahui['nama'] }}</span>
                        @if(!empty($mengetahui['nip']) && $mengetahui['nip'] !== '-')
                            <div class="sig-nip">NIP. {{ $mengetahui['nip'] }}</div>
                        @endif
                    </div>
                </td>
                <td>
                    <div class="sig-date">Tasikmalaya, {{ $tglCetak }}</div>
                    <div class="sig-role">Kepala Gudang,</div>
                    <div class="sig-space"></div>
                    <div>
                        <span class="sig-name">{{ $kepalaGudang['nama'] }}</span>
                        @if(!empty($kepalaGudang['nip']) && $kepalaGudang['nip'] !== '-')
                            <div class="sig-nip">NIP. {{ $kepalaGudang['nip'] }}</div>
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
