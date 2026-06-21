<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Berita Acara Serah Terima Barang - {{ $transaction->no_referensi }}</title>
    <style>
        @page { margin: 2cm 2.5cm; }
        * { box-sizing: border-box; }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
            margin: 0;
            padding: 0;
        }

        /* ─── KOP ─── */
        .kop-table { width: 100%; border-collapse: collapse; }
        .kop-logo-cell { width: 90px; vertical-align: middle; text-align: center; }
        .kop-logo { width: 80px; height: auto; }
        .kop-logo-placeholder .logo-circle {
            width: 75px; height: 75px; border-radius: 50%;
            background: #1e3a5f; display: flex; align-items: center;
            justify-content: center; margin: 0 auto;
        }
        .kop-text-cell { vertical-align: middle; text-align: center; padding: 4px 0; }
        .kop-instansi-atas { font-size: 12pt; font-weight: normal; }
        .kop-instansi-nama { font-size: 16pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .kop-instansi-info { font-size: 9.5pt; margin-top: 1px; }
        .kop-divider { margin-top: 6px; }

        /* ─── TITLE ─── */
        .title-box { text-align: center; margin: 16px 0 14px; }
        .title-box h3 {
            margin: 0 0 4px;
            font-size: 13pt;
            text-transform: uppercase;
            text-decoration: underline;
            letter-spacing: 1px;
        }
        .title-box .nomor { font-size: 11pt; margin: 0; }

        /* ─── OPENING ─── */
        .opening { margin-bottom: 14px; text-align: justify; font-size: 12pt; }

        /* ─── PARTIES ─── */
        .parties { margin-bottom: 14px; }
        .party-table { width: 100%; border-collapse: collapse; }
        .party-table td { padding: 2px 0; vertical-align: top; font-size: 12pt; }
        .col-label  { width: 110px; }
        .col-dots   { width: 15px; }
        .col-value  { }
        .party-label-roman { font-weight: normal; }
        .selanjutnya { margin: 6px 0 12px 125px; font-size: 12pt; }

        /* ─── TABLE ─── */
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .items-table th, .items-table td {
            border: 1px solid #000;
            padding: 5px 8px;
            font-size: 11pt;
        }
        .items-table thead tr { background: #e8e8e8; }
        .items-table th { text-align: center; font-weight: bold; }
        .text-center { text-align: center; }

        /* ─── CLOSING ─── */
        .closing { margin-bottom: 10px; text-align: justify; font-size: 12pt; }

        /* ─── SIGNATURE ─── */
        .sig-section { margin-top: 30px; width: 100%; }
        .sig-table { width: 100%; border-collapse: collapse; }
        .sig-table td { width: 50%; vertical-align: top; text-align: center; border: none; padding: 0 10px; }
        .sig-name { margin-top: 55px; font-weight: bold; }
        .sig-name .underline { text-decoration: underline; }
        .sig-nip { font-size: 11pt; }

        .know-section { margin-top: 30px; text-align: center; }
        .know-section .sig-name { margin-top: 55px; }
    </style>
</head>
<body>

    {{-- ═══ KOP SURAT ═══ --}}
    @php
        $logoSrc  = null;
        $logoPath = public_path('img/logo-daerah.png');
        if (file_exists($logoPath)) {
            $ext     = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
            $mime    = $ext === 'png' ? 'image/png' : 'image/jpeg';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        // Tanggal transaksi
        $tgl = $transaction->tgl_transaksi instanceof \Carbon\Carbon
             ? $transaction->tgl_transaksi
             : \Carbon\Carbon::parse($transaction->tgl_transaksi);

        // Hari dalam bahasa Indonesia
        $hariMap = [
            'Monday'    => 'Senin',    'Tuesday'  => 'Selasa',
            'Wednesday' => 'Rabu',     'Thursday' => 'Kamis',
            'Friday'    => 'Jumat',    'Saturday' => 'Sabtu',
            'Sunday'    => 'Minggu',
        ];
        $namaHari = $hariMap[$tgl->format('l')] ?? $tgl->format('l');

        // Bulan dalam bahasa Indonesia
        $bulanMap = [
            1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April',
            5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus',
            9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember',
        ];
        $namaBulan = $bulanMap[(int)$tgl->format('n')];

        // Tahun dalam huruf
        $tahunNum  = (int)$tgl->format('Y');
        $tahunMap = [
            2020 => 'Dua Ribu Dua Puluh',
            2021 => 'Dua Ribu Dua Puluh Satu',
            2022 => 'Dua Ribu Dua Puluh Dua',
            2023 => 'Dua Ribu Dua Puluh Tiga',
            2024 => 'Dua Ribu Dua Puluh Empat',
            2025 => 'Dua Ribu Dua Puluh Lima',
            2026 => 'Dua Ribu Dua Puluh Enam',
            2027 => 'Dua Ribu Dua Puluh Tujuh',
            2028 => 'Dua Ribu Dua Puluh Delapan',
            2029 => 'Dua Ribu Dua Puluh Sembilan',
            2030 => 'Dua Ribu Tiga Puluh',
        ];
        $tahunText = $tahunMap[$tahunNum] ?? (string)$tahunNum;

        // Terbilang tanggal
        $terbilangMap = [
            1 => 'Satu', 2 => 'Dua', 3 => 'Tiga', 4 => 'Empat', 5 => 'Lima',
            6 => 'Enam', 7 => 'Tujuh', 8 => 'Delapan', 9 => 'Sembilan', 10 => 'Sepuluh',
            11 => 'Sebelas', 12 => 'Dua Belas', 13 => 'Tiga Belas', 14 => 'Empat Belas', 15 => 'Lima Belas',
            16 => 'Enam Belas', 17 => 'Tujuh Belas', 18 => 'Delapan Belas', 19 => 'Sembilan Belas', 20 => 'Dua Puluh',
            21 => 'Dua Puluh Satu', 22 => 'Dua Puluh Dua', 23 => 'Dua Puluh Tiga', 24 => 'Dua Puluh Empat', 25 => 'Dua Puluh Lima',
            26 => 'Dua Puluh Enam', 27 => 'Dua Puluh Tujuh', 28 => 'Dua Puluh Delapan', 29 => 'Dua Puluh Sembilan', 30 => 'Tiga Puluh',
            31 => 'Tiga Puluh Satu'
        ];
        $tglText = $terbilangMap[(int)$tgl->format('d')] ?? $tgl->format('d');

        // Nomor BA
        $nomorBA = $transaction->referenceBap?->nomor_ba ?? $transaction->no_referensi;

        // Pihak I (penyerah)
        $pihakI       = $transaction->pihakKesatu;
        $namaPI       = $pihakI?->nama_pihak   ?? '......................................';
        $nipPI        = $pihakI?->nip           ?? '......................................';
        $jabatanPI    = $pihakI?->jabatan       ?? '......................................';

        // Pihak II (penerima)
        $pihakII      = $transaction->pihakKedua;
        $namaPII      = $pihakII?->nama_pihak   ?? ($transaction->penerima_penyerah ?? '......................................');
        $nipPII       = $pihakII?->nip          ?? '-';
        $jabatanPII   = $pihakII?->jabatan      ?? '-';
        $alamatPII    = $pihakII?->instansi     ?? '......................................';

        // Keperluan (dari keterangan atau kosong)
        $keperluan    = $transaction->keterangan ?? '..........................................................................';
        $desaKec      = $pihakII?->instansi     ?? '...............................................';
    @endphp

    <table class="kop-table">
        <tr>
            <td class="kop-logo-cell">
                @if($logoSrc)
                    <img src="{{ $logoSrc }}" class="kop-logo" alt="Logo">
                @else
                    <div class="kop-logo-placeholder">
                        <div class="logo-circle">
                            <span style="font-size:22pt; color:#fff;">⚙</span>
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
    <div style="border-top: 4px solid #000; border-bottom: 1.5px solid #000; margin-top: 6px; height: 2px;"></div>

    {{-- ═══ JUDUL ═══ --}}
    <div class="title-box">
        <h3>Berita Acara Serah Terima Barang</h3>
        <p class="nomor">Nomor: {{ $nomorBA }}</p>
        @if($transaction->referenceBap)
            <p style="font-size:10pt; margin-top:2px; font-style:italic;">
                BAP: {{ $transaction->referenceBap->judul_ba }}
                ({{ \Carbon\Carbon::parse($transaction->referenceBap->tgl_ba)->format('d/m/Y') }})
            </p>
        @endif
    </div>

    {{-- ═══ OPENING ═══ --}}
    <div class="opening">
        Pada hari ini, <strong>{{ $namaHari }}</strong>
        Tanggal <strong>{{ $tglText }}</strong>
        Bulan <strong>{{ $namaBulan }}</strong>
        Tahun <strong>{{ $tahunText }}</strong>
        yang bertanda tangan dibawah ini:
    </div>

    {{-- ═══ PIHAK-PIHAK ═══ --}}
    <div class="parties">
        <table class="party-table">
            <tr>
                <td class="col-label">I.&nbsp;&nbsp;Nama</td>
                <td class="col-dots">:</td>
                <td class="col-value"><strong>{{ $namaPI }}</strong></td>
            </tr>
            <tr>
                <td class="col-label">&nbsp;&nbsp;&nbsp;&nbsp;NIP</td>
                <td class="col-dots">:</td>
                <td class="col-value">{{ $nipPI }}</td>
            </tr>
            <tr>
                <td class="col-label">&nbsp;&nbsp;&nbsp;&nbsp;Jabatan</td>
                <td class="col-dots">:</td>
                <td class="col-value">{{ $jabatanPI }}</td>
            </tr>
        </table>
        <div class="selanjutnya">Selanjutnya disebut <strong>PIHAK PERTAMA</strong></div>

        <table class="party-table">
            <tr>
                <td class="col-label">II.&nbsp;Nama</td>
                <td class="col-dots">:</td>
                <td class="col-value"><strong>{{ $namaPII }}</strong></td>
            </tr>
            <tr>
                <td class="col-label">&nbsp;&nbsp;&nbsp;&nbsp;NIP</td>
                <td class="col-dots">:</td>
                <td class="col-value">{{ $nipPII }}</td>
            </tr>
            <tr>
                <td class="col-label">&nbsp;&nbsp;&nbsp;&nbsp;Jabatan</td>
                <td class="col-dots">:</td>
                <td class="col-value">{{ $jabatanPII }}</td>
            </tr>
            <tr>
                <td class="col-label">&nbsp;&nbsp;&nbsp;&nbsp;Alamat</td>
                <td class="col-dots">:</td>
                <td class="col-value">{{ $alamatPII }}</td>
            </tr>
        </table>
        <div class="selanjutnya">Selanjutnya disebut <strong>PIHAK KEDUA</strong></div>
    </div>

    {{-- ═══ KALIMAT PENYERAHAN ═══ --}}
    <div class="closing">
        Dengan ini <strong>PIHAK PERTAMA</strong> menyerahkan bantuan kepada <strong>PIHAK KEDUA</strong>, berupa:
    </div>

    {{-- ═══ TABEL BARANG ═══ --}}
    <table class="items-table">
        <thead>
            <tr>
                <th width="35">No</th>
                <th>Nama Barang</th>
                <th width="160">Banyaknya</th>
                <th width="160">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $index => $tx)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $tx->barang->nama_barang ?? '-' }}</td>
                <td class="text-center">
                    @if($tx->jumlah_barang_besar > 0)
                        {{ number_format($tx->jumlah_barang_besar) }} {{ $tx->barang->satuanBesar->nama_satuan ?? 'Pcs' }}
                        @if($tx->jumlah_barang_kecil > 0)
                            &amp; {{ number_format($tx->jumlah_barang_kecil) }} {{ $tx->barang->satuanKecil->nama_satuan ?? 'Pcs' }}
                        @endif
                    @else
                        {{ number_format($tx->jumlah_barang_kecil) }} {{ $tx->barang->satuanKecil->nama_satuan ?? 'Pcs' }}
                    @endif
                </td>
                <td>{{ $tx->keterangan ?? '' }}</td>
            </tr>
            @endforeach
            {{-- Padding baris kosong minimum 10 baris --}}
            @for($i = count($transactions); $i < 10; $i++)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            @endfor
        </tbody>
    </table>

    {{-- ═══ KALIMAT PENUTUP ═══ --}}
    <div class="closing">
        PIHAK KEDUA menerima bantuan sebagaimana tertulis diatas dari PIHAK PERTAMA untuk
        {{ $keperluan }} di {{ $desaKec }}.
    </div>
    <div class="closing">
        Demikian berita acara ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.
    </div>

    {{-- ═══ TANDA TANGAN ═══ --}}
    <div class="sig-section">
        <table class="sig-table">
            <tr>
                <td>
                    <div>Yang Menerima</div>
                    <div><strong>PIHAK KEDUA,</strong></div>
                    <div class="sig-name">
                        <span class="underline">{{ $namaPII }}</span>
                        @if($nipPII && $nipPII !== '-')
                        <br><span class="sig-nip">NIP. {{ $nipPII }}</span>
                        @endif
                    </div>
                </td>
                <td>
                    <div>Yang Menyerahkan</div>
                    <div><strong>PIHAK PERTAMA,</strong></div>
                    <div class="sig-name">
                        <span class="underline">{{ $namaPI }}</span><br>
                        <span class="sig-nip">NIP. {{ $nipPI }}</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- ═══ MENGETAHUI ═══ --}}
    <div class="know-section">
        <div>Mengetahui,</div>
        <div>Kepala Pelaksana</div>
        <div>BPBD Kabupaten Tasikmalaya</div>
        <div class="sig-name">
            <span class="underline">RONI, A.Ks., M.M</span><br>
            <span class="sig-nip">NIP. 19690901 199303 1 004</span>
        </div>
    </div>

</body>
</html>
