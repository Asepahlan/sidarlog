<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Berita Acara Serah Terima Barang - {{ $transaction->no_referensi }}</title>
    <style>
        @page { 
            margin: 1.2cm 2cm; 
        }
        * { 
            box-sizing: border-box; 
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 0;
        }

        /* ─── KOP SURAT ─── */
        .kop-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 5px;
        }
        .kop-logo-cell { 
            width: 80px; 
            vertical-align: middle; 
            text-align: center; 
        }
        .kop-logo { 
            width: 70px; 
            height: auto; 
        }
        .kop-text-cell { 
            vertical-align: middle; 
            text-align: center; 
            padding-right: 40px; /* offset logo width to center text */
        }
        .kop-instansi-atas { 
            font-size: 13pt; 
            font-weight: bold; 
            letter-spacing: 0.5px;
        }
        .kop-instansi-nama { 
            font-size: 14pt; 
            font-weight: bold; 
            text-transform: uppercase; 
            margin-top: 2px;
        }
        .kop-instansi-alamat { 
            font-size: 9pt; 
            margin-top: 3px;
        }
        .kop-instansi-email { 
            font-size: 9pt; 
            margin-top: 1px;
        }
        .kop-divider { 
            border-top: 3px solid #000; 
            border-bottom: 1px solid #000; 
            height: 2px; 
            margin-top: 5px; 
            margin-bottom: 15px;
        }

        /* ─── JUDUL DOKUMEN ─── */
        .title-box { 
            text-align: center; 
            margin-bottom: 15px; 
        }
        .title-box h3 {
            margin: 0 0 2px;
            font-size: 12pt;
            text-transform: uppercase;
            text-decoration: underline;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .title-box .nomor { 
            font-size: 11pt; 
            margin: 0; 
        }

        /* ─── PARAGRAF PEMBUKA ─── */
        .opening { 
            margin-bottom: 12px; 
            text-align: justify; 
            text-indent: 0px;
        }

        /* ─── PIHAK-PIHAK ─── */
        .parties { 
            margin-bottom: 10px; 
            padding-left: 10px;
        }
        .party-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 5px;
        }
        .party-table td { 
            padding: 2px 0; 
            vertical-align: top; 
        }
        .col-num {
            width: 25px;
            font-weight: bold;
        }
        .col-label { 
            width: 90px; 
        }
        .col-dots { 
            width: 15px; 
        }
        .col-value { 
            font-weight: normal;
        }
        .selanjutnya { 
            margin: 2px 0 10px 130px; 
            font-weight: bold; 
        }

        /* ─── PENYERAHAN SENTENCE ─── */
        .closing-sentence {
            margin-bottom: 10px;
            text-align: justify;
        }

        /* ─── TABEL BARANG ─── */
        .items-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 15px; 
        }
        .items-table th, .items-table td {
            border: 1px solid #000;
            padding: 5px 8px;
            font-size: 10pt;
            vertical-align: middle;
        }
        .items-table th { 
            text-align: center; 
            font-weight: bold; 
            background-color: #f2f2f2;
        }
        .text-center { 
            text-align: center; 
        }

        /* ─── PARAGRAF PENUTUP ─── */
        .closing-desc { 
            margin-bottom: 15px; 
            text-align: justify; 
        }

        /* ─── TANDA TANGAN (SIGNATURES) ─── */
        .sig-section { 
            width: 100%; 
            margin-top: 15px;
        }
        .sig-table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        .sig-table td { 
            width: 50%; 
            vertical-align: top; 
            text-align: center; 
            padding: 0 15px; 
        }
        .sig-space {
            height: 55px;
        }
        .sig-name { 
            font-weight: bold; 
            text-decoration: underline;
        }
        .sig-nip { 
            margin-top: 2px;
        }

        .know-section { 
            margin-top: 20px; 
            text-align: center; 
        }
    </style>
</head>
<body>

    @php
        // Logo BPBD Daerah Tasikmalaya
        $logoSrc  = null;
        $logoPath = public_path('img/logo-daerah.png');
        if (file_exists($logoPath)) {
            $ext     = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
            $mime    = $ext === 'png' ? 'image/png' : 'image/jpeg';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        // Ambil data BAST dari record database jika diisi
        $modeTanggal = $transaction->mode_tanggal ?? 'otomatis';
        $namaHari = $transaction->hari ?? '............';
        $tglText = $transaction->tanggal ?? '............';
        $namaBulan = $transaction->bulan ?? '............';
        $tahunText = $transaction->tahun ?? '............';

        if ($modeTanggal === 'otomatis') {
            $tgl = $transaction->tgl_transaksi instanceof \Carbon\Carbon
                 ? $transaction->tgl_transaksi
                 : \Carbon\Carbon::parse($transaction->tgl_transaksi);

            $hariMap = [
                'Monday'    => 'Senin',    'Tuesday'  => 'Selasa',
                'Wednesday' => 'Rabu',     'Thursday' => 'Kamis',
                'Friday'    => 'Jumat',    'Saturday' => 'Sabtu',
                'Sunday'    => 'Minggu',
            ];
            $namaHari = $hariMap[$tgl->format('l')] ?? $tgl->format('l');

            $bulanMap = [
                1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April',
                5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus',
                9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember',
            ];
            $namaBulan = $bulanMap[(int)$tgl->format('n')];

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
        }

        // Format nomor berita acara
        $nomorBA = $transaction->nomor_berita_acara 
            ?? ($transaction->referenceBap?->nomor_ba ?? '300.2.2/BA.             /Darlog/2026');

        // Pihak I (Yang Menyerahkan) — selalu dari input manual
        $namaPI    = $transaction->penyerah_nama    ?? '......................................';
        $nipPI     = $transaction->penyerah_nip     ?? '-';
        $jabatanPI = $transaction->penyerah_jabatan ?? '......................................';
        $alamatPI  = $transaction->penyerah_alamat  ?? '......................................';

        if ($transaction->jenis === 'masuk') {
            // Pihak II (Yang Menerima) = Petugas BPBD
            $penerimaUser = $transaction->penerima;
            $pihakI = $transaction->pihakKesatu;
            $namaPII    = $penerimaUser?->nama_lengkap ?? $penerimaUser?->name ?? ($pihakI?->nama_pihak ?? 'Petugas BPBD');
            $nipPII     = $penerimaUser?->nip ?? ($pihakI?->nip ?? '-');
            $jabatanPII = $penerimaUser?->jabatan->nama_jabatan ?? ($pihakI?->jabatan ?? 'Staf BPBD');
            $alamatPII  = $pihakI?->instansi ?? 'BPBD Kabupaten Tasikmalaya';
        } else {
            // Pihak II (Yang Menerima) = Pihak Kedua / Penerima Bantuan
            $pihakII    = $transaction->pihakKedua;
            $namaPII    = $pihakII?->nama_pihak ?? ($transaction->penerima_penyerah ?? '......................................');
            $nipPII     = $pihakII?->nip ?? '-';
            $jabatanPII = $pihakII?->jabatan ?? '-';
            $alamatPII  = $pihakII?->instansi ?? '......................................';
        }

        // Kecamatan & Desa
        $desa      = $transaction->desa      ?? '......................................';
        $kecamatan = $transaction->kecamatan ?? '......................................';
        $catatan   = $transaction->catatan   ?? '....................................................................';

        // Mengetahui (Kepala Pelaksana BPBD)
        $kepalaPelaksana = \App\Models\FirstParty::where('jabatan', 'like', '%Kepala Pelaksana%')->first();
        $namaKP = $kepalaPelaksana?->nama_pihak ?? 'RONI, A.Ks., M.M';
        $nipKP  = $kepalaPelaksana?->nip ?? '19690901 199303 1 004';
    @endphp


    {{-- KOP SURAT --}}
    <table class="kop-table">
        <tr>
            <td class="kop-logo-cell">
                @if($logoSrc)
                    <img src="{{ $logoSrc }}" class="kop-logo" alt="Logo">
                @else
                    <div style="font-size:24pt; font-weight:bold;">[LOGO]</div>
                @endif
            </td>
            <td class="kop-text-cell">
                <div class="kop-instansi-atas">PEMERINTAH DAERAH KABUPATEN TASIKMALAYA</div>
                <div class="kop-instansi-nama">BADAN PENANGGULANGAN BENCANA DAERAH</div>
                <div class="kop-instansi-alamat">Jl. Otto Iskandardinata No. 19 Tasikmalaya Telp dan Fax (0265) 334111</div>
                <div class="kop-instansi-email">Email : bpbd@tasikmalayakab.go.id | TASIKMALAYA - 46113</div>
            </td>
        </tr>
    </table>
    <div class="kop-divider"></div>

    {{-- JUDUL DOKUMEN --}}
    <div class="title-box">
        <h3>BERITA ACARA SERAH TERIMA BARANG</h3>
        <p class="nomor">Nomor : {{ $nomorBA }}</p>
    </div>

    {{-- PARAGRAF PEMBUKA --}}
    <div class="opening">
        Pada hari ini, <strong>{{ $namaHari }}</strong> Tanggal <strong>{{ $tglText }}</strong> Bulan <strong>{{ $namaBulan }}</strong> Tahun <strong>{{ $tahunText }}</strong> yang bertanda tangan dibawah ini :
    </div>

    {{-- PIHAK-PIHAK --}}
    <div class="parties">
        <table class="party-table">
            <tr>
                <td class="col-num">I.</td>
                <td class="col-label">Nama</td>
                <td class="col-dots">:</td>
                <td class="col-value"><strong>{{ $namaPI }}</strong></td>
            </tr>
            <tr>
                <td></td>
                <td class="col-label">NIP</td>
                <td class="col-dots">:</td>
                <td class="col-value">{{ $nipPI }}</td>
            </tr>
            <tr>
                <td></td>
                <td class="col-label">Jabatan</td>
                <td class="col-dots">:</td>
                <td class="col-value">{{ $jabatanPI }}</td>
            </tr>
        </table>
        <div class="selanjutnya">Selanjutnya disebut PIHAK PERTAMA</div>

        <table class="party-table">
            <tr>
                <td class="col-num">II.</td>
                <td class="col-label">Nama</td>
                <td class="col-dots">:</td>
                <td class="col-value"><strong>{{ $namaPII }}</strong></td>
            </tr>
            <tr>
                <td></td>
                <td class="col-label">NIP</td>
                <td class="col-dots">:</td>
                <td class="col-value">{{ $nipPII }}</td>
            </tr>
            <tr>
                <td></td>
                <td class="col-label">Jabatan</td>
                <td class="col-dots">:</td>
                <td class="col-value">{{ $jabatanPII }}</td>
            </tr>
            <tr>
                <td></td>
                <td class="col-label">Alamat</td>
                <td class="col-dots">:</td>
                <td class="col-value">{{ $alamatPII }}</td>
            </tr>
        </table>
        <div class="selanjutnya">Selanjutnya disebut PIHAK KEDUA</div>
    </div>

    {{-- PENYERAHAN SENTENCE --}}
    <div class="closing-sentence">
        Dengan ini <strong>PIHAK PERTAMA</strong> menyerahkan bantuan kepada <strong>PIHAK KEDUA</strong>, berupa :
    </div>

    {{-- TABEL BARANG (Pre-rendered 10 rows) --}}
    <table class="items-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="50%">Nama Barang</th>
                <th width="20%">Banyaknya</th>
                <th width="25%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $index => $tx)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $tx->barang->nama_barang ?? '-' }}</td>
                <td class="text-center">
                    {{ number_format($tx->jumlah_barang_kecil) }} {{ $tx->barang->satuanKecil->nama_satuan ?? 'Pcs' }}
                </td>
                <td>{{ $tx->keterangan ?? '' }}</td>
            </tr>
            @endforeach
            {{-- Pad to exactly 10 rows minimum --}}
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

    {{-- PARAGRAF PENUTUP --}}
    <div class="closing-desc">
        @if($transaction->jenis == 'keluar')
            PIHAK KEDUA menerima bantuan sebagaimana tertulis diatas dari PIHAK PERTAMA untuk <strong>{{ $catatan }}</strong> di Desa <strong>{{ $desa }}</strong> Kecamatan <strong>{{ $kecamatan }}</strong>.
        @else
            PIHAK KEDUA menerima bantuan sebagaimana tertulis diatas dari PIHAK PERTAMA untuk <strong>{{ $catatan }}</strong>.
        @endif
    </div>
    <div class="closing-desc">
        Demikian berita acara ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.
    </div>

    {{-- TANDA TANGAN --}}
    <div class="sig-section">
        <table class="sig-table">
            <tr>
                <td>
                    <div>Yang Menerima</div>
                    <div><strong>PIHAK KEDUA,</strong></div>
                    <div class="sig-space"></div>
                    <div>
                        <span class="sig-name">{{ $namaPII }}</span>
                        @if($nipPII && $nipPII !== '-' && $nipPII !== '......................................')
                            <div class="sig-nip">NIP. {{ $nipPII }}</div>
                        @endif
                        @if(!empty($jabatanPII) && $jabatanPII !== '-')
                            <div style="font-size:7.5pt; color:#444;">{{ $jabatanPII }}</div>
                        @endif
                    </div>
                </td>
                <td>
                    <div>Yang Menyerahkan</div>
                    <div><strong>PIHAK PERTAMA,</strong></div>
                    <div class="sig-space"></div>
                    <div>
                        <span class="sig-name">{{ $namaPI }}</span>
                        @if(!empty($nipPI) && $nipPI !== '-')
                            <div class="sig-nip">NIP. {{ $nipPI }}</div>
                        @endif
                        @if(!empty($jabatanPI) && $jabatanPI !== '......................................')
                            <div style="font-size:7.5pt; color:#444;">{{ $jabatanPI }}</div>
                        @endif
                        @if(!empty($alamatPI) && $alamatPI !== '......................................')
                            <div style="font-size:7pt; color:#666;">{{ $alamatPI }}</div>
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- MENGETAHUI --}}
    <div class="know-section">
        <div>Mengetahui,</div>
        <div>Kepala Pelaksana</div>
        <div>BPBD Kabupaten Tasikmalaya</div>
        <div class="sig-space"></div>
        <div>
            <span class="sig-name">{{ $namaKP }}</span>
            <div class="sig-nip">NIP. {{ $nipKP }}</div>
        </div>
    </div>

</body>
</html>
